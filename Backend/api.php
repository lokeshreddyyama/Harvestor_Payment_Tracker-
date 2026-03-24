<?php
// ============================================================
//  HARVESTER TRACKER — api.php (v2 with Authentication)
// ============================================================

define('DB_HOST', '');
define('DB_USER', '');
define('DB_PASS', '');
define('DB_NAME', '');
define('UPLOAD_DIR', __DIR__ . '/uploads/');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Auth-Token');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) respond(false, null, 'DB connection failed: '.$conn->connect_error);
$conn->set_charset('utf8mb4');

// Auto-create tables
$conn->query("
    CREATE TABLE IF NOT EXISTS ht_users (
        id         INT AUTO_INCREMENT PRIMARY KEY,
        name       VARCHAR(150) NOT NULL,
        username   VARCHAR(80)  NOT NULL UNIQUE,
        phone      VARCHAR(20),
        vehicle    VARCHAR(50),
        password   VARCHAR(255) NOT NULL,
        token      VARCHAR(64),
        token_exp  DATETIME,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

$conn->query("
    CREATE TABLE IF NOT EXISTS harvester_entries (
        id         INT AUTO_INCREMENT PRIMARY KEY,
        user_id    INT NOT NULL DEFAULT 1,
        name       VARCHAR(150) NOT NULL,
        phone      VARCHAR(20),
        address    VARCHAR(255),
        date       DATE,
        acres      DECIMAL(8,2)  DEFAULT 0,
        crop       VARCHAR(100),
        rate       DECIMAL(10,2) DEFAULT 0,
        amount     DECIMAL(10,2) DEFAULT 0,
        collected  DECIMAL(10,2) DEFAULT 0,
        balance    DECIMAL(10,2) DEFAULT 0,
        vehicle    VARCHAR(50),
        read_start DECIMAL(10,2) DEFAULT 0,
        read_end   DECIMAL(10,2) DEFAULT 0,
        fuel_l     DECIMAL(8,2)  DEFAULT 0,
        fuel_rate  DECIMAL(8,2)  DEFAULT 0,
        fuel_cost  DECIMAL(10,2) DEFAULT 0,
        notes      TEXT,
        photo      VARCHAR(255),
        paid       TINYINT(1)    DEFAULT 0,
        created_at TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
        INDEX (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

$conn->query("ALTER TABLE harvester_entries ADD COLUMN IF NOT EXISTS user_id INT NOT NULL DEFAULT 1");

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];
$public = ['login','register','health','check_user'];
if (!in_array($action, $public)) $userId = require_auth($conn);

switch ($action) {

    case 'health':
        respond(true, ['status'=>'ok','db'=>DB_NAME]);
        break;

    case 'check_user':
        $uname = sanitize($_GET['username'] ?? '');
        if (!$uname) respond(true, ['available'=>false]);
        $stmt = $conn->prepare('SELECT id FROM ht_users WHERE username=? LIMIT 1');
        $stmt->bind_param('s',$uname); $stmt->execute(); $stmt->store_result();
        respond(true, ['available'=>$stmt->num_rows===0]);
        break;

    case 'register':
        if ($method!=='POST') respond(false,null,'POST required');
        $data     = get_body();
        $name     = sanitize($data['name']     ?? '');
        $username = sanitize($data['username'] ?? '');
        $phone    = sanitize($data['phone']    ?? '');
        $vehicle  = strtoupper(sanitize($data['vehicle'] ?? ''));
        $password = $data['password'] ?? '';
        if (!$name||!$username||!$password) respond(false,null,'Name, username and password are required');
        if (strlen($username)<3) respond(false,null,'Username must be at least 3 characters');
        if (!preg_match('/^[a-zA-Z0-9_]+$/',$username)) respond(false,null,'Username: only letters, numbers and underscores');
        if (strlen($password)<6) respond(false,null,'Password must be at least 6 characters');
        $ck=$conn->prepare('SELECT id FROM ht_users WHERE username=? LIMIT 1');
        $ck->bind_param('s',$username); $ck->execute(); $ck->store_result();
        if ($ck->num_rows>0) respond(false,null,'Username already taken. Please choose another.');
        $hash=$conn->real_escape_string(password_hash($password,PASSWORD_BCRYPT));
        $stmt=$conn->prepare('INSERT INTO ht_users (name,username,phone,vehicle,password) VALUES (?,?,?,?,?)');
        $stmt->bind_param('sssss',$name,$username,$phone,$vehicle,$hash);
        if (!$stmt->execute()) respond(false,null,'Registration failed: '.$stmt->error);
        respond(true,['message'=>'Account created successfully']);
        break;

    case 'login':
        if ($method!=='POST') respond(false,null,'POST required');
        $data     = get_body();
        $username = sanitize($data['username'] ?? '');
        $password = $data['password'] ?? '';
        if (!$username||!$password) respond(false,null,'Username and password required');
        $stmt=$conn->prepare('SELECT id,name,username,phone,vehicle,password FROM ht_users WHERE username=? LIMIT 1');
        $stmt->bind_param('s',$username); $stmt->execute();
        $user=$stmt->get_result()->fetch_assoc();
        if (!$user||!password_verify($password,$user['password'])) respond(false,null,'Invalid username or password');
        $token  = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s',strtotime('+24 hours'));
        $uid    = $user['id'];
        $tu=$conn->prepare('UPDATE ht_users SET token=?,token_exp=? WHERE id=?');
        $tu->bind_param('ssi',$token,$expiry,$uid); $tu->execute();
        respond(true,['token'=>$token,'user'=>['id'=>$user['id'],'name'=>$user['name'],'username'=>$user['username'],'phone'=>$user['phone'],'vehicle'=>$user['vehicle']]]);
        break;

    case 'logout':
        $stmt=$conn->prepare('UPDATE ht_users SET token=NULL,token_exp=NULL WHERE id=?');
        $stmt->bind_param('i',$userId); $stmt->execute();
        respond(true,['message'=>'Logged out']);
        break;

    case 'profile':
        $stmt=$conn->prepare('SELECT id,name,username,phone,vehicle,created_at FROM ht_users WHERE id=?');
        $stmt->bind_param('i',$userId); $stmt->execute();
        respond(true,$stmt->get_result()->fetch_assoc());
        break;

    case 'records':
        if ($method==='GET') {
            $where=['user_id=?']; $params=[$userId]; $types='i';
            if (!empty($_GET['search'])) {
                $s='%'.$_GET['search'].'%'; $where[]='(name LIKE ? OR phone LIKE ? OR address LIKE ?)';
                $params=array_merge($params,[$s,$s,$s]); $types.='sss';
            }
            if (isset($_GET['status'])&&$_GET['status']!=='') {
                if ($_GET['status']==='paid') $where[]='paid=1';
                elseif ($_GET['status']==='pending') $where[]='paid=0';
            }
            $sql='SELECT * FROM harvester_entries WHERE '.implode(' AND ',$where).' ORDER BY date DESC, id DESC';
            $stmt=$conn->prepare($sql); $stmt->bind_param($types,...$params); $stmt->execute();
            $rows=[]; $res=$stmt->get_result();
            while ($row=$res->fetch_assoc()) $rows[]=format_row($row);
            respond(true,$rows);
        }
        if ($method==='POST') {
            $data=get_body();
            if (empty($data['name'])) respond(false,null,'Customer name is required');
            $photoPath=null;
            if (!empty($data['photo'])&&strpos($data['photo'],'data:image')===0) $photoPath=save_photo($data['photo']);
            $stmt=$conn->prepare("INSERT INTO harvester_entries (user_id,name,phone,address,date,acres,crop,rate,amount,collected,balance,vehicle,read_start,read_end,fuel_l,fuel_rate,fuel_cost,notes,photo,paid) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $name      =sanitize($data['name']);
            $phone     =sanitize($data['phone']    ?? '');
            $address   =sanitize($data['address']  ?? '');
            $date      =sanitize($data['date']     ?? date('Y-m-d'));
            $acres     =(float)($data['acres']     ?? 0);
            $crop      =sanitize($data['crop']     ?? '');
            $rate      =(float)($data['rate']      ?? 0);
            $amount    =(float)($data['amount']    ?? 0);
            $collected =(float)($data['collected'] ?? 0);
            $balance   =(float)($data['balance']   ?? 0);
            $vehicle   =strtoupper(sanitize($data['vehicle'] ?? ''));
            $rstart    =(float)($data['readStart'] ?? 0);
            $rend      =(float)($data['readEnd']   ?? 0);
            $fuelL     =(float)($data['fuelL']     ?? 0);
            $fuelRate  =(float)($data['fuelRate']  ?? 0);
            $fuelCost  =(float)($data['fuelCost']  ?? 0);
            $notes     =sanitize($data['notes']    ?? '');
            $paid      =0;
            $stmt->bind_param('issssdsddddsdddddssi',$userId,$name,$phone,$address,$date,$acres,$crop,$rate,$amount,$collected,$balance,$vehicle,$rstart,$rend,$fuelL,$fuelRate,$fuelCost,$notes,$photoPath,$paid);
            if (!$stmt->execute()) respond(false,null,'Insert failed: '.$stmt->error);
            $entry=get_entry($conn,$conn->insert_id,$userId);
            respond(true,format_row($entry),'Entry saved');
        }
        break;

    case 'record':
        $id=(int)($_GET['id'] ?? 0);
        if (!$id) respond(false,null,'Invalid ID');
        if ($method==='PUT') {
            $data=get_body();
            $row=get_entry($conn,$id,$userId);
            if (!$row) respond(false,null,'Entry not found or access denied');
            $stmt=$conn->prepare("UPDATE harvester_entries SET name=?,phone=?,address=?,date=?,acres=?,crop=?,rate=?,amount=?,collected=?,balance=?,vehicle=?,read_start=?,read_end=?,fuel_l=?,fuel_cost=?,notes=? WHERE id=? AND user_id=?");
            $name      =sanitize($data['name']      ?? '');
            $phone     =sanitize($data['phone']     ?? '');
            $address   =sanitize($data['address']   ?? '');
            $date      =sanitize($data['date']      ?? date('Y-m-d'));
            $acres     =(float)($data['acres']      ?? 0);
            $crop      =sanitize($data['crop']      ?? '');
            $rate      =(float)($data['rate']       ?? 0);
            $amount    =(float)($data['amount']     ?? 0);
            $collected =(float)($data['collected']  ?? 0);
            $balance   =(float)($data['balance']    ?? 0);
            $vehicle   =strtoupper(sanitize($data['vehicle'] ?? ''));
            $rstart    =(float)($data['readStart']  ?? 0);
            $rend      =(float)($data['readEnd']    ?? 0);
            $fuelL     =(float)($data['fuelL']      ?? 0);
            $fuelCost  =(float)($data['fuelCost']   ?? 0);
            $notes     =sanitize($data['notes']     ?? '');
            $stmt->bind_param('ssssdsddddsddddssii',$name,$phone,$address,$date,$acres,$crop,$rate,$amount,$collected,$balance,$vehicle,$rstart,$rend,$fuelL,$fuelCost,$notes,$id,$userId);
            if (!$stmt->execute()) respond(false,null,'Update failed: '.$stmt->error);
            respond(true,format_row(get_entry($conn,$id,$userId)),'Entry updated');
        }
        if ($method==='DELETE') {
            $row=get_entry($conn,$id,$userId);
            if (!$row) respond(false,null,'Entry not found or access denied');
            if (!empty($row['photo'])) { $f=UPLOAD_DIR.basename($row['photo']); if(file_exists($f)) unlink($f); }
            $stmt=$conn->prepare('DELETE FROM harvester_entries WHERE id=? AND user_id=?');
            $stmt->bind_param('ii',$id,$userId); $stmt->execute();
            respond(true,null,'Entry deleted');
        }
        break;

    case 'toggle':
        $id=(int)($_GET['id'] ?? 0);
        if (!$id) respond(false,null,'Invalid ID');
        $row=get_entry($conn,$id,$userId);
        if (!$row) respond(false,null,'Entry not found or access denied');
        $newPaid=$row['paid']?0:1;
        $stmt=$conn->prepare('UPDATE harvester_entries SET paid=? WHERE id=? AND user_id=?');
        $stmt->bind_param('iii',$newPaid,$id,$userId); $stmt->execute();
        respond(true,['id'=>$id,'paid'=>(bool)$newPaid,'message'=>$newPaid?'✅ Marked as Paid':'↩️ Marked as Pending']);
        break;

    case 'stats':
        $today=date('Y-m-d');
        $stmt=$conn->prepare("SELECT COUNT(*) AS total_entries,SUM(amount) AS total_amount,SUM(collected) AS total_collected,SUM(balance) AS total_balance,SUM(acres) AS total_acres,SUM(fuel_cost) AS total_fuel_cost,SUM(paid=1) AS paid_count FROM harvester_entries WHERE user_id=?");
        $stmt->bind_param('i',$userId); $stmt->execute();
        $overall=$stmt->get_result()->fetch_assoc();
        $stmt2=$conn->prepare("SELECT COUNT(*) AS entry_count,COALESCE(SUM(acres),0) AS acres,COALESCE(SUM(collected),0) AS collected,COALESCE(SUM(fuel_cost),0) AS fuelCost FROM harvester_entries WHERE user_id=? AND date=?");
        $stmt2->bind_param('is',$userId,$today); $stmt2->execute();
        $todayData=$stmt2->get_result()->fetch_assoc();
        $stmt3=$conn->prepare("SELECT id,name,phone,address,date,balance FROM harvester_entries WHERE user_id=? AND paid=0 AND balance>0 ORDER BY balance DESC LIMIT 20");
        $stmt3->bind_param('i',$userId); $stmt3->execute();
        $pending=[]; $res3=$stmt3->get_result();
        while ($r=$res3->fetch_assoc()) $pending[]=$r;
        respond(true,['overall'=>$overall,'today'=>$todayData,'pending'=>$pending]);
        break;

    case 'export':
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="harvester_records_'.date('Y-m-d').'.csv"');
        $stmt=$conn->prepare('SELECT * FROM harvester_entries WHERE user_id=? ORDER BY date DESC');
        $stmt->bind_param('i',$userId); $stmt->execute();
        $result=$stmt->get_result();
        $out=fopen('php://output','w');
        fputcsv($out,['ID','Name','Phone','Address','Date','Acres','Crop','Rate','Amount','Collected','Balance','Vehicle','Start KM','End KM','Fuel (L)','Fuel Rate','Fuel Cost','Notes','Photo','Paid','Created At']);
        while ($row=$result->fetch_assoc()) {
            fputcsv($out,[$row['id'],$row['name'],$row['phone'],$row['address'],$row['date'],$row['acres'],$row['crop'],$row['rate'],$row['amount'],$row['collected'],$row['balance'],$row['vehicle'],$row['read_start'],$row['read_end'],$row['fuel_l'],$row['fuel_rate'],$row['fuel_cost'],$row['notes'],$row['photo'],$row['paid']?'Yes':'No',$row['created_at']]);
        }
        fclose($out); exit;

    default:
        respond(false,null,'Unknown action: '.$action);
}
$conn->close();

// ── HELPERS ──────────────────────────────────────────────────
function require_auth(mysqli $conn): int {
    $token = $_SERVER['HTTP_X_AUTH_TOKEN'] ?? $_GET['token'] ?? null;
    if (!$token) respond(false,null,'Authentication required. Please login.');
    $stmt=$conn->prepare('SELECT id FROM ht_users WHERE token=? AND token_exp>NOW() LIMIT 1');
    $stmt->bind_param('s',$token); $stmt->execute();
    $row=$stmt->get_result()->fetch_assoc();
    if (!$row) respond(false,null,'Session expired or invalid. Please login again.');
    return (int)$row['id'];
}
function respond(bool $success, $data=null, string $msg='') {
    $out=['success'=>$success];
    if ($success) { $out['data']=$data; if($msg) $out['message']=$msg; }
    else $out['error']=$msg;
    echo json_encode($out,JSON_UNESCAPED_UNICODE); exit;
}
function get_body(): array {
    $raw=file_get_contents('php://input');
    if (!$raw) return [];
    $data=json_decode($raw,true);
    return is_array($data)?$data:[];
}
function sanitize(string $val): string {
    return trim(htmlspecialchars($val,ENT_QUOTES,'UTF-8'));
}
function get_entry(mysqli $conn, int $id, int $userId): ?array {
    $stmt=$conn->prepare('SELECT * FROM harvester_entries WHERE id=? AND user_id=? LIMIT 1');
    $stmt->bind_param('ii',$id,$userId); $stmt->execute();
    $row=$stmt->get_result()->fetch_assoc();
    return $row?:null;
}
function format_row(array $row): array {
    return ['id'=>(int)$row['id'],'userId'=>(int)$row['user_id'],'name'=>$row['name'],'phone'=>$row['phone'],'address'=>$row['address'],'date'=>$row['date'],'acres'=>(float)$row['acres'],'crop'=>$row['crop'],'rate'=>(float)$row['rate'],'amount'=>(float)$row['amount'],'collected'=>(float)$row['collected'],'balance'=>(float)$row['balance'],'vehicle'=>$row['vehicle'],'readStart'=>(float)$row['read_start'],'readEnd'=>(float)$row['read_end'],'fuelL'=>(float)$row['fuel_l'],'fuelRate'=>(float)$row['fuel_rate'],'fuelCost'=>(float)$row['fuel_cost'],'notes'=>$row['notes'],'photo'=>$row['photo']?'uploads/'.basename($row['photo']):null,'paid'=>(bool)$row['paid'],'createdAt'=>$row['created_at']];
}
function save_photo(string $base64): ?string {
    if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR,0755,true);
    if (!preg_match('/^data:(image\/\w+);base64,(.+)$/',$base64,$m)) return null;
    $ext=str_replace('image/','',$m[1]); $ext=($ext==='jpeg')?'jpg':$ext;
    $filename='photo_'.time().'_'.rand(1000,9999).'.'.$ext;
    $decoded=base64_decode($m[2]);
    if (!$decoded) return null;
    file_put_contents(UPLOAD_DIR.$filename,$decoded);
    return $filename;
}