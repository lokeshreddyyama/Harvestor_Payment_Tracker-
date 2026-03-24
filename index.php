<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Harvester Payment Tracker</title>
    <link rel="icon" type="image/x-icon" href="./uploads/photos/Harveterlogo.jpg" style="border-radius:10px;">
<link href="https://fonts.googleapis.com/css2?family=Tiro+Telugu&family=Rajdhani:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<style>
:root {
  --gold:#c9a84c; --gold-light:#e8c97a;
  --dark:#0f1a0f; --dark2:#162516; --dark3:#1e321e;
  --green:#2d6a2d; --green-light:#4a9e4a; --green-bright:#6fcf6f;
  --red:#c0392b; --blue:#1a5276; --amber:#e67e22;
  --text:#e8f0e8; --text-muted:#8aaa8a;
  --border:rgba(201,168,76,0.25);
  --card-bg:rgba(22,37,22,0.92);
  --input-bg:rgba(15,26,15,0.8);
}
* { margin:0; padding:0; box-sizing:border-box; }
body {
  font-family:'Rajdhani',sans-serif; background:var(--dark); color:var(--text); min-height:100vh;
  background-image:
    radial-gradient(ellipse at 20% 20%,rgba(45,106,45,0.15) 0%,transparent 50%),
    radial-gradient(ellipse at 80% 80%,rgba(201,168,76,0.08) 0%,transparent 50%),
    repeating-linear-gradient(0deg,transparent,transparent 40px,rgba(201,168,76,0.03) 40px,rgba(201,168,76,0.03) 41px),
    repeating-linear-gradient(90deg,transparent,transparent 40px,rgba(201,168,76,0.03) 40px,rgba(201,168,76,0.03) 41px);
}
header {
  background:linear-gradient(135deg,#0a150a 0%,#1a2e1a 50%,#0a150a 100%);
  border-bottom:2px solid var(--gold); padding:14px 20px;
  display:flex; align-items:center; justify-content:space-between;
  position:sticky; top:0; z-index:50; box-shadow:0 4px 30px rgba(0,0,0,0.5); flex-wrap:wrap; gap:10px;
}
.header-brand { display:flex; align-items:center; gap:12px; }
.header-icon { font-size:2rem; filter:drop-shadow(0 0 8px rgba(201,168,76,0.5)); }
.header-title { font-size:1.4rem; font-weight:700; color:var(--gold); letter-spacing:1px; line-height:1; }
.header-sub { font-size:0.8rem; color:var(--text-muted); font-family:'Tiro Telugu',serif; }
.header-right { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.conn-dot { width:10px; height:10px; border-radius:50%; background:#f39c12; display:inline-block; margin-right:4px; transition:background 0.3s; }
.conn-dot.ok { background:#6fcf6f; box-shadow:0 0 6px #6fcf6f; }
.conn-dot.err { background:#e74c3c; }
.conn-label { font-size:0.8rem; color:var(--text-muted); }
.user-badge { display:flex; align-items:center; gap:6px; background:rgba(201,168,76,0.1); border:1px solid var(--border); border-radius:8px; padding:5px 10px; font-size:0.8rem; }
.user-badge .uname { color:var(--gold); font-weight:700; }
.user-badge .uvehicle { color:var(--text-muted); font-size:0.72rem; }
.header-nav { display:flex; gap:5px; flex-wrap:wrap; }
.nav-btn { padding:6px 13px; border-radius:6px; border:1.5px solid var(--border); background:transparent; color:var(--text-muted); cursor:pointer; font-family:'Rajdhani',sans-serif; font-size:0.83rem; font-weight:600; transition:all 0.2s; }
.nav-btn:hover,.nav-btn.active { background:var(--gold); color:var(--dark); border-color:var(--gold); }
.btn-logout { padding:5px 12px; border-radius:6px; border:1.5px solid rgba(192,57,43,0.4); background:rgba(192,57,43,0.1); color:#e74c3c; cursor:pointer; font-family:'Rajdhani',sans-serif; font-size:0.8rem; font-weight:600; transition:all 0.2s; }
.btn-logout:hover { background:var(--red); color:#fff; border-color:var(--red); }
.tab-content { display:none; }
.tab-content.active { display:block; }
.container { max-width:1200px; margin:0 auto; padding:18px 14px; }
.stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:10px; margin-bottom:18px; }
.stat-card { background:var(--card-bg); border:1px solid var(--border); border-radius:10px; padding:13px 14px; position:relative; overflow:hidden; backdrop-filter:blur(10px); }
.stat-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,var(--gold),var(--green-bright)); }
.stat-val { font-size:1.4rem; font-weight:700; font-family:'JetBrains Mono',monospace; color:var(--gold); }
.stat-lbl { font-size:0.72rem; color:var(--text-muted); margin-top:3px; }
.card { background:var(--card-bg); border:1px solid var(--border); border-radius:12px; padding:20px; margin-bottom:18px; backdrop-filter:blur(10px); }
.card-header { display:flex; align-items:center; gap:10px; border-bottom:1px solid var(--border); padding-bottom:11px; margin-bottom:16px; }
.card-header h2 { font-size:1.1rem; color:var(--gold); font-weight:700; }
.card-header .tl { font-family:'Tiro Telugu',serif; font-size:0.78rem; color:var(--text-muted); }
.form-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.form-grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; }
@media(max-width:680px){ .form-grid,.form-grid-3{ grid-template-columns:1fr; } header{ flex-direction:column; align-items:flex-start; } }
.form-group { display:flex; flex-direction:column; gap:4px; }
.form-group label { font-size:0.76rem; color:var(--gold-light); font-weight:600; }
.form-group input,.form-group textarea,.form-group select { padding:8px 11px; background:var(--input-bg); border:1.5px solid rgba(201,168,76,0.2); border-radius:7px; color:var(--text); font-family:'Rajdhani',sans-serif; font-size:0.93rem; outline:none; transition:border-color 0.2s; }
.form-group input:focus,.form-group textarea:focus,.form-group select:focus { border-color:var(--gold); box-shadow:0 0 0 3px rgba(201,168,76,0.1); }
.form-group textarea { resize:vertical; min-height:60px; }
.form-group select option { background:var(--dark2); }
.photo-box { border:2px dashed rgba(201,168,76,0.3); border-radius:8px; padding:14px; text-align:center; cursor:pointer; transition:all 0.2s; background:var(--input-bg); }
.photo-box:hover { border-color:var(--gold); }
.photo-box input { display:none; }
.photo-box img { max-width:100%; max-height:110px; border-radius:5px; object-fit:cover; }
.photo-box .ph { color:var(--text-muted); font-size:0.83rem; }
.photo-box .ph span { font-size:1.8rem; display:block; margin-bottom:3px; }
.btn { padding:8px 18px; border:none; border-radius:7px; cursor:pointer; font-family:'Rajdhani',sans-serif; font-size:0.93rem; font-weight:600; transition:all 0.15s; }
.btn:disabled { opacity:0.5; cursor:not-allowed; }
.btn-gold { background:linear-gradient(135deg,var(--gold),#a8832e); color:var(--dark); }
.btn-gold:hover:not(:disabled) { filter:brightness(1.1); }
.btn-green { background:var(--green); color:#fff; border:1px solid var(--green-light); }
.btn-green:hover:not(:disabled) { background:var(--green-light); }
.btn-red { background:var(--red); color:#fff; }
.btn-blue { background:var(--blue); color:#fff; }
.btn-ghost { background:transparent; color:var(--text-muted); border:1px solid var(--border); }
.btn-ghost:hover:not(:disabled) { border-color:var(--gold); color:var(--gold); }
.btn-sm { padding:4px 10px; font-size:0.78rem; border-radius:5px; }
.btn-row { display:flex; gap:8px; margin-top:14px; flex-wrap:wrap; }
.table-wrap { overflow-x:auto; border-radius:8px; }
table { width:100%; border-collapse:collapse; font-size:0.83rem; min-width:860px; }
thead tr { background:rgba(201,168,76,0.12); }
thead th { padding:10px 9px; text-align:left; color:var(--gold); font-weight:700; border-bottom:2px solid var(--border); white-space:nowrap; }
tbody tr { border-bottom:1px solid rgba(201,168,76,0.08); transition:background 0.15s; }
tbody tr:hover { background:rgba(201,168,76,0.05); }
tbody td { padding:8px 9px; vertical-align:middle; }
.badge { display:inline-block; padding:2px 8px; border-radius:20px; font-size:0.73rem; font-weight:600; cursor:pointer; }
.badge-paid { background:rgba(45,106,45,0.3); color:#6fcf6f; border:1px solid rgba(111,207,111,0.3); }
.badge-pending { background:rgba(230,126,34,0.2); color:#f39c12; border:1px solid rgba(243,156,18,0.3); }
.act-btns { display:flex; gap:4px; }
.search-bar { display:flex; gap:8px; margin-bottom:12px; flex-wrap:wrap; }
.search-bar input { flex:1; padding:7px 12px; background:var(--input-bg); border:1.5px solid var(--border); border-radius:7px; color:var(--text); font-family:'Rajdhani',sans-serif; font-size:0.92rem; outline:none; min-width:160px; }
.search-bar input:focus { border-color:var(--gold); }
.search-bar select { padding:7px 10px; background:var(--input-bg); border:1.5px solid var(--border); border-radius:7px; color:var(--text); font-family:'Rajdhani',sans-serif; outline:none; }
.empty { text-align:center; padding:36px; color:var(--text-muted); }
.empty .ico { font-size:2.8rem; opacity:0.4; }
.thumb { width:38px; height:38px; object-fit:cover; border-radius:5px; border:1px solid var(--border); cursor:pointer; }
.thumb-ph { width:38px; height:38px; background:var(--dark3); border-radius:5px; display:flex; align-items:center; justify-content:center; color:var(--text-muted); font-size:1rem; border:1px solid var(--border); }
.modal-bg { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.72); z-index:200; align-items:center; justify-content:center; backdrop-filter:blur(4px); }
.modal-bg.open { display:flex; }
.modal { background:linear-gradient(135deg,#162516,#0f1a0f); border:1px solid var(--gold); border-radius:14px; padding:24px; width:95%; max-width:580px; max-height:90vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,0.7); animation:su 0.22s ease; }
@keyframes su { from{transform:translateY(28px);opacity:0} to{transform:translateY(0);opacity:1} }
.modal-head { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
.modal-head h3 { color:var(--gold); font-size:1.1rem; }
.modal-x { background:none; border:none; color:var(--text-muted); cursor:pointer; font-size:1.4rem; padding:2px 6px; border-radius:4px; }
.modal-x:hover { color:var(--red); }
.receipt { background:#fff; color:#111; border-radius:10px; padding:22px; font-family:'Rajdhani',sans-serif; }
.rh { text-align:center; border-bottom:2px dashed #2e7d32; padding-bottom:12px; margin-bottom:12px; }
.rh .rl { font-size:1.9rem; } .rh .rt { font-size:1.2rem; font-weight:700; color:#2e7d32; }
.rh .rs { font-size:0.83rem; color:#555; font-family:'Tiro Telugu',serif; }
.rh .ri { font-size:0.73rem; color:#888; margin-top:3px; font-family:'JetBrains Mono',monospace; }
.rtbl { width:100%; border-collapse:collapse; font-size:0.88rem; margin-bottom:12px; }
.rtbl td { padding:5px 3px; border-bottom:1px solid #f0f0f0; }
.rtbl td:first-child { color:#555; font-weight:600; width:42%; }
.rphoto { width:100%; max-height:140px; object-fit:cover; border-radius:5px; margin:6px 0; border:1px solid #ddd; }
.rstat { text-align:center; padding:7px; border-radius:6px; font-weight:700; font-size:0.95rem; margin:8px 0; }
.rstat.paid { background:#e8f5e9; color:#2e7d32; } .rstat.pending { background:#fff3e0; color:#e65100; }
.rf { text-align:center; font-size:0.76rem; color:#aaa; border-top:2px dashed #2e7d32; padding-top:8px; margin-top:8px; }
.dli { background:rgba(15,26,15,0.6); border:1px solid var(--border); border-radius:8px; padding:12px 14px; margin-bottom:9px; display:grid; grid-template-columns:1fr auto; gap:10px; align-items:start; }
.ld { display:flex; gap:14px; flex-wrap:wrap; font-size:0.83rem; margin-top:5px; }
.ld-i { color:var(--text-muted); } .ld-i strong { color:var(--text); }
.vt th { background:rgba(201,168,76,0.12); color:var(--gold); padding:9px; text-align:left; border-bottom:2px solid var(--border); }
.vt td { padding:8px 9px; border-bottom:1px solid rgba(201,168,76,0.08); }
.toast { position:fixed; bottom:22px; right:22px; z-index:999; color:#fff; padding:11px 18px; border-radius:8px; font-weight:600; font-size:0.88rem; box-shadow:0 4px 20px rgba(0,0,0,0.4); display:none; max-width:300px; animation:ti 0.25s ease; }
.toast.show { display:block; }
@keyframes ti { from{transform:translateY(18px);opacity:0} to{transform:translateY(0);opacity:1} }
.imgv { position:fixed; inset:0; background:rgba(0,0,0,0.92); z-index:300; display:none; align-items:center; justify-content:center; }
.imgv.open { display:flex; }
.imgv img { max-width:90%; max-height:90vh; border-radius:8px; border:2px solid var(--gold); }
.imgv-x { position:fixed; top:18px; right:22px; font-size:1.9rem; color:#fff; cursor:pointer; background:none; border:none; }
.sec-lbl { font-size:0.7rem; text-transform:uppercase; letter-spacing:2px; color:var(--gold); margin:14px 0 8px; opacity:0.7; }
.spinner { display:inline-block; width:15px; height:15px; border:2px solid rgba(255,255,255,0.3); border-top-color:#fff; border-radius:50%; animation:sp 0.6s linear infinite; vertical-align:middle; margin-right:5px; }
@keyframes sp { to{transform:rotate(360deg)} }
.err-box { background:rgba(192,57,43,0.14); border:1px solid #c0392b; border-radius:8px; padding:11px 15px; color:#e74c3c; font-size:0.88rem; margin-bottom:14px; line-height:1.6; }
</style>
</head>
<body>

<header>
  <div class="header-brand">
    <div class="header-icon">🌾</div>
    <div>
      <div class="header-title">HARVESTER TRACKER</div>
      <div class="header-sub">హార్వెస్టర్ చెల్లింపు నిర్వాహణ వ్యవస్థ</div>
    </div>
  </div>
  <div class="header-right">
    <div style="display:flex;align-items:center;gap:6px">
      <span class="conn-dot" id="connDot"></span>
      <span class="conn-label" id="connLabel">Connecting…</span>
    </div>
    <div class="user-badge" id="userBadge" style="display:none">
      <span>👤</span>
      <div>
        <div class="uname" id="badgeName"></div>
        <div class="uvehicle" id="badgeVehicle"></div>
      </div>
    </div>
    <div class="header-nav">
      <button class="nav-btn active" onclick="showTab('dashboard',this)">📊 Dashboard</button>
      <button class="nav-btn" onclick="showTab('entries',this)">➕ New Entry</button>
      <button class="nav-btn" onclick="showTab('records',this)">📋 Records</button>
      <button class="nav-btn" onclick="showTab('daily',this)">📅 Daily Log</button>
      <button class="nav-btn" onclick="showTab('vehicle',this)">🚜 Vehicle</button>
    </div>
    <button class="btn-logout" onclick="doLogout()">🚪 Logout</button>
  </div>
</header>

<div class="toast" id="toast"></div>
<div class="imgv" id="imgv" onclick="closeImg()">
  <button class="imgv-x">✕</button>
  <img id="imgvImg" src="" alt="">
</div>

<!-- DASHBOARD -->
<div class="tab-content active" id="tab-dashboard">
<div class="container">
  <div id="dashErr"></div>
  <div class="stats-grid" id="statsGrid">
    <div class="stat-card" style="grid-column:1/-1;text-align:center;padding:30px">
      <div class="spinner"></div> Loading…
    </div>
  </div>
  <div class="card">
    <div class="card-header"><span style="font-size:1.2rem">📅</span>
      <h2>Today's Summary <span class="tl">/ నేటి సారాంశం</span></h2></div>
    <div class="stats-grid" id="todayStats"></div>
  </div>
  <div class="card">
    <div class="card-header"><span style="font-size:1.2rem">🔔</span>
      <h2>Pending Balances <span class="tl">/ పెండింగ్ బాకీలు</span></h2></div>
    <div id="pendingList"></div>
  </div>
</div>
</div>

<!-- NEW ENTRY -->
<div class="tab-content" id="tab-entries">
<div class="container">
<div class="card">
  <div class="card-header"><span style="font-size:1.2rem">➕</span>
    <h2>Add New Entry <span class="tl">/ కొత్త నమోదు జోడించు</span></h2></div>
  <div class="sec-lbl">Customer Information</div>
  <div class="form-grid">
    <div class="form-group"><label>Customer Name *</label><input type="text" id="i_name" placeholder="Customer full name"></div>
    <div class="form-group"><label>Phone</label><input type="tel" id="i_phone" placeholder="10-digit mobile"></div>
    <div class="form-group"><label>Village / Field Address</label><input type="text" id="i_address" placeholder="Village, Mandal, District"></div>
    <div class="form-group"><label>Date</label><input type="date" id="i_date"></div>
  </div>
  <div class="sec-lbl">Harvesting Details</div>
  <div class="form-grid-3">
    <div class="form-group"><label>Acres Cut</label><input type="number" id="i_acres" placeholder="e.g. 5.5" step="0.5" min="0" oninput="autoAmt()"></div>
    <div class="form-group"><label>Crop Type</label>
      <select id="i_crop">
        <option value="">-- Select --</option>
        <option>Paddy / వరి</option><option>Wheat / గోధుమ</option>
        <option>Sugarcane / చెరకు</option><option>Cotton / పత్తి</option>
        <option>Maize / మొక్కజొన్న</option><option>Soybean / సోయాబీన్</option>
        <option>Other / ఇతర</option>
      </select></div>
    <div class="form-group"><label>Rate per Acre ₹</label><input type="number" id="i_rate" placeholder="Rate" oninput="autoAmt()"></div>
  </div>
  <div class="sec-lbl">Payment Details</div>
  <div class="form-grid-3">
    <div class="form-group"><label>Total Amount ₹</label><input type="number" id="i_amount" placeholder="Auto-calculated" oninput="calcBal()"></div>
    <div class="form-group"><label>Collected ₹</label><input type="number" id="i_collected" placeholder="Amount paid now" oninput="calcBal()"></div>
    <div class="form-group"><label>Balance Due ₹</label><input type="number" id="i_balance" readonly style="color:var(--amber)"></div>
  </div>
  <div class="sec-lbl">Vehicle & Fuel</div>
  <div class="form-grid-3">
    <div class="form-group"><label>Vehicle Number</label><input type="text" id="i_vehicle" placeholder="AP 15 AA 1234"></div>
    <div class="form-group"><label>Starting Reading (km)</label><input type="number" id="i_rstart" placeholder="Odometer start"></div>
    <div class="form-group"><label>Ending Reading (km)</label><input type="number" id="i_rend" placeholder="Odometer end"></div>
    <div class="form-group"><label>Fuel Filled (Litres)</label><input type="number" id="i_fuell" placeholder="Litres" oninput="calcFuel()"></div>
    <div class="form-group"><label>Fuel Rate ₹/L</label><input type="number" id="i_fuelr" value="100" oninput="calcFuel()"></div>
    <div class="form-group"><label>Fuel Cost ₹</label><input type="number" id="i_fuelc" readonly style="color:var(--amber)"></div>
  </div>
  <div class="sec-lbl">Photo & Notes</div>
  <div class="form-grid">
    <div class="form-group"><label>Field / Customer Photo</label>
      <div class="photo-box" onclick="document.getElementById('i_photo').click()">
        <input type="file" id="i_photo" accept="image/*" onchange="prevPhoto(this)">
        <div id="i_ph"><div class="ph"><span>📷</span>Tap to upload photo</div></div>
        <img id="i_prev" src="" style="display:none" alt="">
      </div>
    </div>
    <div class="form-group"><label>Notes</label><textarea id="i_notes" placeholder="Any additional notes…"></textarea></div>
  </div>
  <div class="btn-row">
    <button class="btn btn-gold" id="saveBtn" onclick="saveEntry()">💾 Save Entry</button>
    <button class="btn btn-ghost" onclick="clearForm()">✕ Clear</button>
  </div>
</div>
</div>
</div>

<!-- RECORDS -->
<div class="tab-content" id="tab-records">
<div class="container">
<div class="card">
  <div class="card-header"><span style="font-size:1.2rem">📋</span>
    <h2>Payment Records <span class="tl">/ చెల్లింపు రికార్డులు</span></h2></div>
  <div class="search-bar">
    <input type="text" id="srch" placeholder="🔍 Search name, phone, village…" oninput="loadRecords()">
    <select id="fstat" onchange="loadRecords()">
      <option value="">All</option><option value="paid">Paid</option><option value="pending">Pending</option>
    </select>
    <button class="btn btn-ghost btn-sm" onclick="loadRecords()">🔄 Refresh</button>
    <button class="btn btn-ghost btn-sm" onclick="doExport()">📥 CSV</button>
    <button class="btn btn-ghost btn-sm" onclick="window.print()">🖨️ Print</button>
  </div>
  <div id="tblErr"></div>
  <div id="tblLoad" style="display:none;text-align:center;padding:28px;color:var(--text-muted)"><div class="spinner"></div> Loading…</div>
  <div class="table-wrap">
    <table>
      <thead><tr>
        <th>#</th><th>Photo</th><th>Customer</th><th>Phone</th><th>Address</th>
        <th>Acres</th><th>Crop</th><th>Amount ₹</th><th>Collected ₹</th><th>Balance ₹</th>
        <th>Vehicle</th><th>Fuel ₹</th><th>Date</th><th>Status</th><th>Actions</th>
      </tr></thead>
      <tbody id="tblBody"></tbody>
    </table>
    <div class="empty" id="tblEmpty" style="display:none"><div class="ico">🌾</div><div style="margin-top:8px">No records found</div></div>
  </div>
</div>
</div>
</div>

<!-- DAILY LOG -->
<div class="tab-content" id="tab-daily">
<div class="container">
<div class="card">
  <div class="card-header"><span style="font-size:1.2rem">📅</span>
    <h2>Daily Operations Log <span class="tl">/ రోజువారీ లాగ్</span></h2></div>
  <div class="form-grid-3" style="margin-bottom:14px">
    <div class="form-group"><label>Filter by Date</label><input type="date" id="dfDate" onchange="renderDaily()"></div>
    <div class="form-group"><label>Filter by Vehicle</label><input type="text" id="dfVeh" placeholder="Vehicle number" oninput="renderDaily()"></div>
    <div style="display:flex;align-items:flex-end">
      <button class="btn btn-ghost btn-sm" onclick="document.getElementById('dfDate').value='';document.getElementById('dfVeh').value='';renderDaily()">✕ Clear</button>
    </div>
  </div>
  <div id="dailyCont"></div>
</div>
</div>
</div>

<!-- VEHICLE -->
<div class="tab-content" id="tab-vehicle">
<div class="container">
<div class="card">
  <div class="card-header"><span style="font-size:1.2rem">🚜</span>
    <h2>Vehicle & Fuel Report <span class="tl">/ వాహనం & ఇంధన నివేదిక</span></h2></div>
  <div class="stats-grid" id="vStats"></div>
  <div class="table-wrap" style="margin-top:14px">
    <table class="vt">
      <thead><tr><th>Date</th><th>Vehicle No.</th><th>Start KM</th><th>End KM</th><th>Distance</th><th>Fuel (L)</th><th>Fuel Cost ₹</th><th>Acres</th><th>Customer</th></tr></thead>
      <tbody id="vBody"></tbody>
    </table>
  </div>
</div>
</div>
</div>

<!-- EDIT MODAL -->
<div class="modal-bg" id="editModal">
<div class="modal">
  <div class="modal-head"><h3>✏️ Edit Entry</h3><button class="modal-x" onclick="closeEdit()">✕</button></div>
  <div class="form-grid">
    <div class="form-group"><label>Name</label><input type="text" id="e_name"></div>
    <div class="form-group"><label>Phone</label><input type="tel" id="e_phone"></div>
    <div class="form-group"><label>Address</label><input type="text" id="e_address"></div>
    <div class="form-group"><label>Date</label><input type="date" id="e_date"></div>
    <div class="form-group"><label>Acres</label><input type="number" id="e_acres" step="0.5"></div>
    <div class="form-group"><label>Crop</label><input type="text" id="e_crop"></div>
    <div class="form-group"><label>Rate ₹/Acre</label><input type="number" id="e_rate"></div>
    <div class="form-group"><label>Amount ₹</label><input type="number" id="e_amount"></div>
    <div class="form-group"><label>Collected ₹</label><input type="number" id="e_collected"></div>
    <div class="form-group"><label>Balance ₹</label><input type="number" id="e_balance"></div>
    <div class="form-group"><label>Vehicle</label><input type="text" id="e_vehicle"></div>
    <div class="form-group"><label>Start KM</label><input type="number" id="e_rstart"></div>
    <div class="form-group"><label>End KM</label><input type="number" id="e_rend"></div>
    <div class="form-group"><label>Fuel (L)</label><input type="number" id="e_fuell"></div>
    <div class="form-group"><label>Fuel Cost ₹</label><input type="number" id="e_fuelc"></div>
  </div>
  <div class="form-group" style="margin-top:10px"><label>Notes</label><textarea id="e_notes"></textarea></div>
  <div class="btn-row">
    <button class="btn btn-gold" id="editSaveBtn" onclick="saveEdit()">💾 Update</button>
    <button class="btn btn-ghost" onclick="closeEdit()">Cancel</button>
  </div>
</div>
</div>

<!-- RECEIPT MODAL -->
<div class="modal-bg" id="rcptModal">
<div class="modal" style="max-width:460px">
  <div class="modal-head"><h3>🧾 Receipt</h3><button class="modal-x" onclick="closeRcpt()">✕</button></div>
  <div id="rcptContent"></div>
  <div class="btn-row" style="margin-top:12px">
    <button class="btn btn-green btn-sm" onclick="printRcpt()">🖨️ Print</button>
    <button class="btn btn-ghost btn-sm" onclick="closeRcpt()">Close</button>
  </div>
</div>
</div>

<script>
const API = 'api.php';
function getToken() { return sessionStorage.getItem('ht_token')||''; }

// Auth guard
(function(){ if(!getToken()) window.location.href='login.html'; })();

// User badge
(function(){
  const name=sessionStorage.getItem('ht_name')||'';
  const user=sessionStorage.getItem('ht_username')||'';
  if(name){
    document.getElementById('badgeName').textContent=name;
    document.getElementById('badgeVehicle').textContent='@'+user;
    document.getElementById('userBadge').style.display='flex';
  }
})();

async function doLogout(){
  try{ await call('logout','POST'); }catch(e){}
  sessionStorage.clear(); window.location.href='login.html';
}

let records=[], editId=null;
const today=()=>new Date().toISOString().split('T')[0];
const fmt=n=>(+(n||0)).toLocaleString('en-IN');
const imgSrc=p=>(!p?null:p);

function toast(msg,color='#2d6a2d'){
  const t=document.getElementById('toast');
  t.textContent=msg; t.style.background=color; t.classList.add('show');
  setTimeout(()=>t.classList.remove('show'),3500);
}

async function call(action,method='GET',body=null,params={}){
  let url=API+'?action='+encodeURIComponent(action);
  for(const[k,v] of Object.entries(params)) url+='&'+encodeURIComponent(k)+'='+encodeURIComponent(v);
  const opts={method,headers:{'X-Auth-Token':getToken()}};
  if(body){opts.headers['Content-Type']='application/json';opts.body=JSON.stringify(body);}
  let res;
  try{ res=await fetch(url,opts); }catch(e){ throw new Error('Network error — cannot reach server.'); }
  const text=await res.text();
  let data;
  try{ data=JSON.parse(text); }catch(e){ throw new Error('Server returned non-JSON:\n'+text.slice(0,300)); }
  if(!data.success&&data.error&&data.error.includes('login')){ sessionStorage.clear(); window.location.href='login.html'; return; }
  if(!data.success) throw new Error(data.error||'Unknown API error');
  return data;
}

async function checkConn(){
  const dot=document.getElementById('connDot'),lbl=document.getElementById('connLabel');
  try{ await call('health'); dot.className='conn-dot ok'; lbl.textContent='✅ Database Connected'; }
  catch(e){ dot.className='conn-dot err'; lbl.textContent='❌ '+e.message.split('\n')[0]; }
}

function showTab(name,btn){
  document.querySelectorAll('.tab-content').forEach(t=>t.classList.remove('active'));
  document.querySelectorAll('.nav-btn').forEach(b=>b.classList.remove('active'));
  document.getElementById('tab-'+name).classList.add('active');
  if(btn) btn.classList.add('active');
  if(name==='dashboard') renderDashboard();
  if(name==='records')   loadRecords();
  if(name==='daily')  { if(records.length===0) loadRecordsForLocal().then(renderDaily);   else renderDaily(); }
  if(name==='vehicle'){ if(records.length===0) loadRecordsForLocal().then(renderVehicle); else renderVehicle(); }
}
async function loadRecordsForLocal(){
  try{ const res=await call('records'); records=res.data||[]; }catch(e){}
}

function autoAmt(){
  const a=parseFloat(document.getElementById('i_acres').value)||0;
  const r=parseFloat(document.getElementById('i_rate').value)||0;
  if(a&&r){document.getElementById('i_amount').value=(a*r).toFixed(2);calcBal();}
}
function calcBal(){
  const a=parseFloat(document.getElementById('i_amount').value)||0;
  const c=parseFloat(document.getElementById('i_collected').value)||0;
  document.getElementById('i_balance').value=Math.max(0,a-c).toFixed(2);
}
function calcFuel(){
  const l=parseFloat(document.getElementById('i_fuell').value)||0;
  const r=parseFloat(document.getElementById('i_fuelr').value)||0;
  document.getElementById('i_fuelc').value=(l*r).toFixed(2);
}

function prevPhoto(inp){
  const f=inp.files[0]; if(!f) return;
  const fr=new FileReader();
  fr.onload=e=>{
    document.getElementById('i_ph').style.display='none';
    const img=document.getElementById('i_prev'); img.src=e.target.result; img.style.display='block';
  };
  fr.readAsDataURL(f);
}
function getPhotoB64(){
  return new Promise(res=>{
    const inp=document.getElementById('i_photo');
    if(!inp.files[0]){res(null);return;}
    const fr=new FileReader(); fr.onload=e=>res(e.target.result); fr.readAsDataURL(inp.files[0]);
  });
}

async function saveEntry(){
  const name=document.getElementById('i_name').value.trim();
  if(!name){toast('⚠️ Customer name is required!','#c0392b');return;}
  const btn=document.getElementById('saveBtn');
  btn.disabled=true; btn.innerHTML='<span class="spinner"></span> Saving…';
  try{
    const photo=await getPhotoB64();
    const body={
      name,
      phone:    document.getElementById('i_phone').value.trim(),
      address:  document.getElementById('i_address').value.trim(),
      date:     document.getElementById('i_date').value||today(),
      acres:    parseFloat(document.getElementById('i_acres').value)||0,
      crop:     document.getElementById('i_crop').value,
      rate:     parseFloat(document.getElementById('i_rate').value)||0,
      amount:   parseFloat(document.getElementById('i_amount').value)||0,
      collected:parseFloat(document.getElementById('i_collected').value)||0,
      balance:  parseFloat(document.getElementById('i_balance').value)||0,
      vehicle:  document.getElementById('i_vehicle').value.trim().toUpperCase(),
      readStart:parseFloat(document.getElementById('i_rstart').value)||0,
      readEnd:  parseFloat(document.getElementById('i_rend').value)||0,
      fuelL:    parseFloat(document.getElementById('i_fuell').value)||0,
      fuelRate: parseFloat(document.getElementById('i_fuelr').value)||0,
      fuelCost: parseFloat(document.getElementById('i_fuelc').value)||0,
      notes:    document.getElementById('i_notes').value.trim(),
      photo, paid:false,
    };
    const res=await call('records','POST',body);
    records.unshift(res.data); clearForm(); toast('✅ Entry saved!');
  }catch(e){ toast('❌ Save failed: '+e.message,'#c0392b'); }
  finally{ btn.disabled=false; btn.innerHTML='💾 Save Entry'; }
}

function clearForm(){
  ['i_name','i_phone','i_address','i_acres','i_rate','i_amount','i_collected','i_balance','i_vehicle','i_rstart','i_rend','i_fuell','i_fuelc','i_notes'].forEach(id=>{
    const el=document.getElementById(id); if(el) el.value='';
  });
  document.getElementById('i_date').value=today();
  document.getElementById('i_fuelr').value='100';
  document.getElementById('i_crop').value='';
  document.getElementById('i_photo').value='';
  document.getElementById('i_prev').style.display='none';
  document.getElementById('i_ph').style.display='block';
}

async function renderDashboard(){
  document.getElementById('dashErr').innerHTML='';
  try{
    const[sRes,rRes]=await Promise.all([call('stats'),call('records')]);
    records=rRes.data||[];
    const o=sRes.data.overall,td=sRes.data.today,pn=sRes.data.pending||[];
    document.getElementById('statsGrid').innerHTML=[
      ['Total Entries',o.total_entries,'📁','var(--gold)'],
      ['Total Amount ₹','₹'+fmt(o.total_amount),'💰','var(--gold)'],
      ['Collected ₹','₹'+fmt(o.total_collected),'✅','var(--green-bright)'],
      ['Balance Due ₹','₹'+fmt(o.total_balance),'⚠️','#f39c12'],
      ['Total Acres',(+o.total_acres).toFixed(1),'🌾','var(--gold)'],
      ['Total Fuel ₹','₹'+fmt(o.total_fuel_cost),'⛽','#e74c3c'],
      ['Paid / Total',o.paid_count+' / '+o.total_entries,'🎉','var(--green-bright)'],
    ].map(([l,v,i,c])=>`<div class="stat-card"><div style="font-size:1.3rem;margin-bottom:3px">${i}</div><div class="stat-val" style="color:${c}">${v}</div><div class="stat-lbl">${l}</div></div>`).join('');
    document.getElementById('todayStats').innerHTML=[
      ["Today's Entries",td.entry_count,'📋'],
      ['Acres Today',(+td.acres).toFixed(1)+' ac','🌾'],
      ['Collected Today ₹','₹'+fmt(td.collected),'💵'],
      ['Fuel Cost Today ₹','₹'+fmt(td.fuelCost),'⛽'],
    ].map(([l,v,i])=>`<div class="stat-card"><div style="font-size:1.2rem">${i}</div><div class="stat-val">${v}</div><div class="stat-lbl">${l}</div></div>`).join('');
    document.getElementById('pendingList').innerHTML=pn.length===0
      ?'<div class="empty"><div class="ico">✅</div><div style="margin-top:8px">All balances cleared!</div></div>'
      :pn.map(r=>`<div style="display:flex;justify-content:space-between;align-items:center;padding:9px 0;border-bottom:1px solid var(--border)"><div><strong>${r.name}</strong><span style="color:var(--text-muted);font-size:0.79rem;margin-left:7px">${r.phone||''}</span><div style="color:var(--text-muted);font-size:0.77rem">${r.address||''} · ${r.date}</div></div><div style="text-align:right"><div style="color:#f39c12;font-weight:700;font-family:'JetBrains Mono',monospace">₹${fmt(r.balance)}</div><button class="btn btn-green btn-sm" style="margin-top:4px" onclick="togglePaid(${r.id})">Mark Paid</button></div></div>`).join('');
  }catch(e){
    document.getElementById('statsGrid').innerHTML=`<div class="stat-card" style="grid-column:1/-1;text-align:center"><div style="color:#e74c3c">❌ Cannot load data</div></div>`;
    document.getElementById('dashErr').innerHTML=`<div class="err-box">❌ ${e.message}</div>`;
  }
}

async function loadRecords(){
  document.getElementById('tblLoad').style.display='block';
  document.getElementById('tblErr').innerHTML='';
  try{
    const s=document.getElementById('srch').value,f=document.getElementById('fstat').value;
    const params={}; if(s) params.search=s; if(f) params.status=f;
    const res=await call('records','GET',null,params);
    records=res.data||[]; paintTable();
  }catch(e){ document.getElementById('tblErr').innerHTML=`<div class="err-box">❌ ${e.message}</div>`; }
  finally{ document.getElementById('tblLoad').style.display='none'; }
}

function paintTable(){
  const r=records;
  document.getElementById('tblEmpty').style.display=r.length===0?'block':'none';
  document.getElementById('tblBody').innerHTML=r.map((row,i)=>{
    const src=imgSrc(row.photo);
    return `<tr>
      <td><strong>${i+1}</strong></td>
      <td>${src?`<img class="thumb" src="${src}" onclick="viewImg('${src}')">`:'<div class="thumb-ph">📷</div>'}</td>
      <td><strong>${row.name}</strong><div style="color:var(--text-muted);font-size:0.73rem">${row.notes||''}</div></td>
      <td style="font-family:'JetBrains Mono',monospace;font-size:0.8rem">${row.phone||'-'}</td>
      <td style="font-size:0.8rem">${row.address||'-'}</td>
      <td><strong>${row.acres||0}</strong><span style="color:var(--text-muted);font-size:0.73rem"> ac</span></td>
      <td style="font-size:0.8rem">${row.crop||'-'}</td>
      <td style="font-family:'JetBrains Mono',monospace">₹${fmt(row.amount)}</td>
      <td style="color:var(--green-bright);font-family:'JetBrains Mono',monospace">₹${fmt(row.collected)}</td>
      <td style="color:${row.balance>0?'#f39c12':'var(--green-bright)'};font-family:'JetBrains Mono',monospace;font-weight:700">₹${fmt(row.balance)}</td>
      <td style="font-size:0.78rem;font-family:'JetBrains Mono',monospace">${row.vehicle||'-'}</td>
      <td style="color:#e74c3c;font-family:'JetBrains Mono',monospace">₹${fmt(row.fuelCost)}</td>
      <td style="font-size:0.77rem;color:var(--text-muted)">${row.date||'-'}</td>
      <td><span class="badge ${row.paid?'badge-paid':'badge-pending'}" onclick="togglePaid(${row.id})">${row.paid?'✅ Paid':'⏳ Pending'}</span></td>
      <td><div class="act-btns">
        <button class="btn btn-green btn-sm" onclick="openRcpt(${row.id})">🧾</button>
        <button class="btn btn-blue btn-sm"  onclick="openEdit(${row.id})">✏️</button>
        <button class="btn btn-red btn-sm"   onclick="delEntry(${row.id})">🗑️</button>
      </div></td>
    </tr>`;
  }).join('');
}

async function togglePaid(id){
  try{
    const res=await call('toggle','POST',{},{id});
    const r=records.find(r=>r.id==id); if(r) r.paid=res.data.paid;
    paintTable(); toast(res.data.message||(res.data.paid?'✅ Marked as Paid':'↩️ Marked as Pending'));
    renderDashboard();
  }catch(e){ toast('❌ '+e.message,'#c0392b'); }
}

async function delEntry(id){
  if(!confirm('Delete this entry?\nతొలగించాలా?')) return;
  try{
    await call('record','DELETE',null,{id});
    records=records.filter(r=>r.id!=id); paintTable(); toast('🗑️ Deleted','#c0392b');
  }catch(e){ toast('❌ '+e.message,'#c0392b'); }
}

function openEdit(id){
  const r=records.find(r=>r.id==id); if(!r) return;
  editId=id;
  const map={e_name:r.name,e_phone:r.phone,e_address:r.address,e_date:r.date,e_acres:r.acres,e_crop:r.crop,e_rate:r.rate,e_amount:r.amount,e_collected:r.collected,e_balance:r.balance,e_vehicle:r.vehicle,e_rstart:r.readStart,e_rend:r.readEnd,e_fuell:r.fuelL,e_fuelc:r.fuelCost,e_notes:r.notes};
  for(const[k,v] of Object.entries(map)){const el=document.getElementById(k);if(el) el.value=v||'';}
  document.getElementById('editModal').classList.add('open');
}
async function saveEdit(){
  const btn=document.getElementById('editSaveBtn');
  btn.disabled=true; btn.innerHTML='<span class="spinner"></span> Updating…';
  try{
    const body={
      name:     document.getElementById('e_name').value.trim(),
      phone:    document.getElementById('e_phone').value.trim(),
      address:  document.getElementById('e_address').value.trim(),
      date:     document.getElementById('e_date').value,
      acres:    parseFloat(document.getElementById('e_acres').value)||0,
      crop:     document.getElementById('e_crop').value,
      rate:     parseFloat(document.getElementById('e_rate').value)||0,
      amount:   parseFloat(document.getElementById('e_amount').value)||0,
      collected:parseFloat(document.getElementById('e_collected').value)||0,
      balance:  parseFloat(document.getElementById('e_balance').value)||0,
      vehicle:  document.getElementById('e_vehicle').value.toUpperCase(),
      readStart:parseFloat(document.getElementById('e_rstart').value)||0,
      readEnd:  parseFloat(document.getElementById('e_rend').value)||0,
      fuelL:    parseFloat(document.getElementById('e_fuell').value)||0,
      fuelCost: parseFloat(document.getElementById('e_fuelc').value)||0,
      notes:    document.getElementById('e_notes').value,
    };
    const res=await call('record','PUT',body,{id:editId});
    const idx=records.findIndex(r=>r.id==editId);
    if(idx!==-1) records[idx]=res.data;
    closeEdit(); paintTable(); toast('✅ Entry updated!');
  }catch(e){ toast('❌ '+e.message,'#c0392b'); }
  finally{ btn.disabled=false; btn.innerHTML='💾 Update'; }
}
function closeEdit(){ document.getElementById('editModal').classList.remove('open'); editId=null; }

function openRcpt(id){
  const r=records.find(r=>r.id==id); if(!r) return;
  const dist=Math.max(0,(+(r.readEnd||0))-(+(r.readStart||0)));
  const src=imgSrc(r.photo);
  const opName=sessionStorage.getItem('ht_name')||'';
  document.getElementById('rcptContent').innerHTML=`
    <div class="receipt" id="printRcpt">
      <div class="rh">
        <div class="rl">🌾</div>
        <div class="rt">HARVESTER PAYMENT RECEIPT</div>
        <div class="rs">హార్వెస్టర్ చెల్లింపు రసీదు</div>
        <div class="ri">Receipt #${r.id} · ${r.date||''} · ${opName}</div>
      </div>
      ${src?`<img class="rphoto" src="${src}" alt="Field Photo">`:''}
      <table class="rtbl">
        <tr><td>👤 Customer</td><td>${r.name}</td></tr>
        <tr><td>📞 Phone</td><td>${r.phone||'-'}</td></tr>
        <tr><td>📍 Address</td><td>${r.address||'-'}</td></tr>
        <tr><td>📅 Date</td><td>${r.date||'-'}</td></tr>
        <tr><td>🌾 Acres</td><td>${r.acres||0} acres</td></tr>
        <tr><td>🌱 Crop</td><td>${r.crop||'-'}</td></tr>
        <tr><td>💰 Rate/Acre</td><td>₹${fmt(r.rate)}</td></tr>
        <tr style="background:#f9f9f9"><td><strong>💵 Total</strong></td><td><strong>₹${fmt(r.amount)}</strong></td></tr>
        <tr><td>✅ Collected</td><td>₹${fmt(r.collected)}</td></tr>
        <tr style="background:#fff3e0"><td><strong>⚠️ Balance</strong></td><td><strong style="color:#e65100">₹${fmt(r.balance)}</strong></td></tr>
        <tr><td>🚜 Vehicle</td><td>${r.vehicle||'-'}</td></tr>
        <tr><td>📐 Distance</td><td>${dist} km</td></tr>
        <tr><td>⛽ Fuel</td><td>${r.fuelL||0} L · ₹${fmt(r.fuelCost)}</td></tr>
        ${r.notes?`<tr><td>📝 Notes</td><td>${r.notes}</td></tr>`:''}
      </table>
      <div class="rstat ${r.paid?'paid':'pending'}">${r.paid?'✅ PAYMENT CONFIRMED':'⏳ PAYMENT PENDING'}</div>
      <div class="rf">Thank you! / ధన్యవాదాలు!<br>Generated: ${new Date().toLocaleString('en-IN')}</div>
    </div>`;
  document.getElementById('rcptModal').classList.add('open');
}
function printRcpt(){
  const c=document.getElementById('printRcpt').outerHTML;
  const w=window.open('','_blank');
  w.document.write(`<html><head><title>Receipt</title>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body{font-family:'Rajdhani',sans-serif;margin:20px;background:#fff;}.receipt{max-width:400px;margin:auto;}.rh{text-align:center;border-bottom:2px dashed #2e7d32;padding-bottom:12px;margin-bottom:12px;}.rl{font-size:1.9rem;}.rt{font-size:1.2rem;font-weight:700;color:#2e7d32;}.rs{font-size:0.83rem;color:#555;}.ri{font-size:0.73rem;color:#888;margin-top:3px;}.rtbl{width:100%;border-collapse:collapse;font-size:0.88rem;margin-bottom:12px;}.rtbl td{padding:5px 3px;border-bottom:1px solid #f0f0f0;}.rtbl td:first-child{color:#555;font-weight:600;width:42%;}.rphoto{width:100%;max-height:140px;object-fit:cover;border-radius:5px;margin:6px 0;}.rstat{text-align:center;padding:7px;border-radius:6px;font-weight:700;margin:8px 0;}.rstat.paid{background:#e8f5e9;color:#2e7d32;}.rstat.pending{background:#fff3e0;color:#e65100;}.rf{text-align:center;font-size:0.76rem;color:#aaa;border-top:2px dashed #2e7d32;padding-top:8px;margin-top:8px;}</style>
    </head><body>${c}</body></html>`);
  w.document.close(); w.print();
}
function closeRcpt(){ document.getElementById('rcptModal').classList.remove('open'); }

function renderDaily(){
  const df=document.getElementById('dfDate').value;
  const dvf=document.getElementById('dfVeh').value.toLowerCase();
  let rows=[...records];
  if(df)  rows=rows.filter(r=>r.date===df);
  if(dvf) rows=rows.filter(r=>(r.vehicle||'').toLowerCase().includes(dvf));
  const byDate={};
  rows.forEach(r=>{(byDate[r.date]=byDate[r.date]||[]).push(r);});
  const dates=Object.keys(byDate).sort((a,b)=>b.localeCompare(a));
  const el=document.getElementById('dailyCont');
  if(!dates.length){ el.innerHTML='<div class="empty"><div class="ico">📅</div><div style="margin-top:8px">No logs found</div></div>'; return; }
  el.innerHTML=dates.map(d=>{
    const dr=byDate[d];
    const tA=dr.reduce((s,r)=>s+(+(r.acres||0)),0);
    const tC=dr.reduce((s,r)=>s+(+(r.collected||0)),0);
    const tF=dr.reduce((s,r)=>s+(+(r.fuelCost||0)),0);
    const tB=dr.reduce((s,r)=>s+(+(r.balance||0)),0);
    return `<div style="margin-bottom:14px">
      <div style="color:var(--gold);font-weight:700;margin-bottom:7px;display:flex;gap:14px;flex-wrap:wrap;align-items:center">
        📅 ${d} <span style="color:var(--text-muted);font-size:0.78rem">${dr.length} entries</span>
        <span style="color:var(--green-bright);font-size:0.83rem">🌾 ${tA.toFixed(1)} ac</span>
        <span style="color:#6fcf6f;font-size:0.83rem">💵 ₹${fmt(tC)}</span>
        <span style="color:#e74c3c;font-size:0.83rem">⛽ ₹${fmt(tF)}</span>
        ${tB>0?`<span style="color:#f39c12;font-size:0.83rem">⚠️ ₹${fmt(tB)}</span>`:''}
      </div>
      ${dr.map(r=>{
        const src=imgSrc(r.photo);
        return `<div class="dli">
          <div>
            <div style="font-weight:700">${r.name} <span style="color:var(--text-muted);font-weight:400;font-size:0.8rem">${r.phone||''}</span></div>
            <div class="ld">
              <div class="ld-i">📍 <strong>${r.address||'-'}</strong></div>
              <div class="ld-i">🌾 <strong>${r.acres||0} ac</strong> ${r.crop||''}</div>
              <div class="ld-i">💰 <strong>₹${fmt(r.amount)}</strong></div>
              <div class="ld-i">✅ <strong>₹${fmt(r.collected)}</strong></div>
              ${r.balance>0?`<div class="ld-i" style="color:#f39c12">⚠️ <strong>₹${fmt(r.balance)}</strong></div>`:''}
              <div class="ld-i">🚜 <strong>${r.vehicle||'-'}</strong></div>
              <div class="ld-i">⛽ <strong>${r.fuelL||0}L ₹${fmt(r.fuelCost)}</strong></div>
            </div>
          </div>
          <div style="display:flex;flex-direction:column;gap:5px;align-items:flex-end">
            ${src?`<img class="thumb" src="${src}" onclick="viewImg('${src}')">`:''}
            <span class="badge ${r.paid?'badge-paid':'badge-pending'}">${r.paid?'✅ Paid':'⏳ Pending'}</span>
            <button class="btn btn-green btn-sm" onclick="openRcpt(${r.id})">🧾</button>
          </div>
        </div>`;
      }).join('')}
    </div>`;
  }).join('');
}

function renderVehicle(){
  const vMap={};
  records.forEach(r=>{
    const v=r.vehicle||'Unknown';
    if(!vMap[v]) vMap[v]={count:0,acres:0,fuel:0,fuelCost:0,dist:0};
    vMap[v].count++; vMap[v].acres+=(+(r.acres||0)); vMap[v].fuel+=(+(r.fuelL||0));
    vMap[v].fuelCost+=(+(r.fuelCost||0)); vMap[v].dist+=Math.max(0,(+(r.readEnd||0))-(+(r.readStart||0)));
  });
  document.getElementById('vStats').innerHTML=Object.entries(vMap).map(([v,d])=>`
    <div class="stat-card"><div style="font-size:1.1rem">🚜</div>
    <div class="stat-val" style="font-size:0.95rem">${v}</div>
    <div class="stat-lbl">Trips: <strong>${d.count}</strong> · Acres: <strong>${d.acres.toFixed(1)}</strong><br>Fuel: <strong>${d.fuel.toFixed(1)}L / ₹${fmt(d.fuelCost)}</strong><br>Distance: <strong>${d.dist} km</strong></div>
    </div>`).join('')||'<div class="stat-card"><div class="stat-lbl">No vehicle data yet</div></div>';
  document.getElementById('vBody').innerHTML=records
    .filter(r=>r.vehicle||r.readStart||r.readEnd)
    .sort((a,b)=>(b.date||'').localeCompare(a.date||''))
    .map(r=>{
      const dist=Math.max(0,(+(r.readEnd||0))-(+(r.readStart||0)));
      return `<tr>
        <td style="color:var(--text-muted);font-size:0.78rem">${r.date||'-'}</td>
        <td style="font-family:'JetBrains Mono',monospace;color:var(--gold)">${r.vehicle||'-'}</td>
        <td>${r.readStart||0}</td><td>${r.readEnd||0}</td>
        <td style="color:var(--green-bright)">${dist?dist+' km':'-'}</td>
        <td>${r.fuelL||0} L</td>
        <td style="color:#e74c3c">₹${fmt(r.fuelCost)}</td>
        <td>${r.acres||0} ac</td>
        <td><strong>${r.name}</strong></td>
      </tr>`;
    }).join('')||'<tr><td colspan="9" style="text-align:center;color:var(--text-muted);padding:20px">No vehicle data</td></tr>';
}

function viewImg(src){ document.getElementById('imgvImg').src=src; document.getElementById('imgv').classList.add('open'); }
function closeImg(){ document.getElementById('imgv').classList.remove('open'); }
function doExport(){ window.open('api.php?action=export&token='+encodeURIComponent(getToken()),'_blank'); }

['editModal','rcptModal'].forEach(id=>{
  document.getElementById(id).addEventListener('click',e=>{
    if(e.target.id===id) document.getElementById(id).classList.remove('open');
  });
});

document.getElementById('i_date').value=today();
checkConn();
renderDashboard();
</script>
</body>
</html>