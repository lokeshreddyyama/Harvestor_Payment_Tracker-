const app = {
    records: [],
    
    init() {
        if (!ApiService.getToken()) {
            window.location.href = 'login.html';
            return;
        }

        // Profile init
        const name = sessionStorage.getItem('ht_name') || 'Operator';
        const user = sessionStorage.getItem('ht_username') || 'admin';
        document.getElementById('uName').innerText = name;
        document.getElementById('uTag').innerText = '@' + user;
        document.getElementById('uAvatar').innerText = name.charAt(0).toUpperCase();

        document.getElementById('i_date').valueAsDate = new Date();
        document.getElementById('dailyDate').valueAsDate = new Date();

        this.switchView('dashboard');
    },

    toast(msg, type='success') {
        const container = document.getElementById('toastContainer');
        const t = document.createElement('div');
        t.className = `toast ${type}`;
        t.innerHTML = `<span style="font-size:1.2rem">${type==='success'?'✅':'❌'}</span><span>${msg}</span>`;
        container.appendChild(t);
        setTimeout(() => {
            t.classList.add('fade-out');
            setTimeout(() => t.remove(), 400);
        }, 3000);
    },

    switchView(viewId) {
        document.querySelectorAll('.view').forEach(v => v.classList.remove('active'));
        document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
        
        const viewEl = document.getElementById('view-' + viewId);
        if(viewEl) viewEl.classList.add('active');
        
        event.currentTarget?.classList.add('active');
        
        const titleMap = { 'dashboard':'Dashboard', 'entry':'New Record', 'records':'Payment Records', 'daily':'Daily Logs' };
        document.getElementById('pageTitle').innerText = titleMap[viewId] || 'Tracker';

        if (window.innerWidth <= 900) {
            document.getElementById('sidebar').classList.remove('open');
        }

        if (viewId === 'dashboard') Object.keys(this.records).length === 0 ? this.loadDashboard(true) : this.loadDashboard(false);
        if (viewId === 'records') this.loadRecords();
        if (viewId === 'daily') this.records.length === 0 ? this.loadRecords().then(()=>this.renderDaily()) : this.renderDaily();
    },

    fmt(num) {
        return Number(num || 0).toLocaleString('en-IN', { maximumFractionDigits: 2 });
    },

    async loadDashboard(fetchFromApi = true) {
        try {
            if (fetchFromApi) {
                const [sRes, rRes] = await Promise.all([ApiService.getStats(), ApiService.getRecords()]);
                this.records = rRes.data || [];
                this.stats = sRes.data;
            }

            const { overall, today, pending } = this.stats;

            document.getElementById('mainStats').innerHTML = `
                <div class="glass-card stat-card gold">
                    <div class="stat-title">Total Cash Collected</div>
                    <div class="stat-value gold">₹${this.fmt(overall.total_collected)}</div>
                </div>
                <div class="glass-card stat-card red">
                    <div class="stat-title">Total Balance Due</div>
                    <div class="stat-value" style="color:var(--danger-color)">₹${this.fmt(overall.total_balance)}</div>
                </div>
                <div class="glass-card stat-card green">
                    <div class="stat-title">Total Acres Harvested</div>
                    <div class="stat-value green">${this.fmt(overall.total_acres)} <span style="font-size:1rem;color:var(--text-muted)">ac</span></div>
                </div>
                <div class="glass-card stat-card">
                    <div class="stat-title">Fuel Expenses</div>
                    <div class="stat-value">₹${this.fmt(overall.total_fuel_cost)}</div>
                </div>
            `;

            document.getElementById('todayStats').innerHTML = `
                <div style="display:flex; justify-content:space-between; border-bottom:1px solid var(--border-color); padding-bottom:8px">
                    <span class="text-muted">Fields Cut:</span> <strong class="text-main">${today.entry_count}</strong>
                </div>
                <div style="display:flex; justify-content:space-between; border-bottom:1px solid var(--border-color); padding-bottom:8px">
                    <span class="text-muted">Total Acres:</span> <strong class="text-green">${this.fmt(today.acres)}</strong>
                </div>
                <div style="display:flex; justify-content:space-between; border-bottom:1px solid var(--border-color); padding-bottom:8px">
                    <span class="text-muted">Cash In:</span> <strong class="text-gold">₹${this.fmt(today.collected)}</strong>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span class="text-muted">Fuel Out:</span> <strong class="text-danger">₹${this.fmt(today.fuelCost)}</strong>
                </div>
            `;

            const pList = document.getElementById('pendingList');
            if (pending.length === 0) {
                pList.innerHTML = `<div class="text-muted" style="text-align:center; padding: 20px;">🎉 All balances cleared!</div>`;
            } else {
                pList.innerHTML = pending.map(p => `
                    <div style="display:flex; justify-content:space-between; align-items:center; background:rgba(0,0,0,0.2); padding:12px; border-radius:var(--radius-sm); border:1px solid var(--border-color)">
                        <div>
                            <div style="font-weight:600; color:var(--text-main)">${p.name}</div>
                            <div style="font-size:0.8rem; color:var(--text-muted)">${p.address || p.phone || 'No Contact'}</div>
                        </div>
                        <div style="text-align:right">
                            <div style="color:var(--danger-color); font-weight:700; font-family:var(--font-heading)">₹${this.fmt(p.balance)}</div>
                            <button class="btn btn-success" style="padding:4px 8px; font-size:0.75rem; margin-top:4px" onclick="app.markPaid(${p.id})">Mark Paid ✓</button>
                        </div>
                    </div>
                `).join('');
            }

        } catch (e) {
            this.toast(e.message, 'error');
        }
    },

    async loadRecords(force = false) {
        try {
            const tbody = document.getElementById('tblBody');
            if(force) tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding: 40px;"><div class="loader" style="margin: 0 auto;"></div></td></tr>`;
            
            const res = await ApiService.getRecords();
            this.records = res.data || [];
            this.filterTable();
        } catch (e) {
            this.toast(e.message, 'error');
        }
    },

    filterTable() {
        const s = document.getElementById('srch').value.toLowerCase();
        const f = document.getElementById('fstat').value;

        const filtered = this.records.filter(r => {
            const matchesSearch = !s || (r.name?.toLowerCase().includes(s) || r.phone?.includes(s) || r.address?.toLowerCase().includes(s));
            const matchesStatus = !f || (f === 'paid' ? r.paid : !r.paid);
            return matchesSearch && matchesStatus;
        });

        const tbody = document.getElementById('tblBody');
        if (filtered.length === 0) {
            tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding: 40px; color:var(--text-muted)">No matching records found.</td></tr>`;
            return;
        }

        tbody.innerHTML = filtered.map((r, i) => `
            <tr>
                <td class="tabular-nums" style="color:var(--text-muted)">${i + 1}</td>
                <td>
                    ${r.photo ? `<img src="/${r.photo}" style="width:40px;height:40px;border-radius:var(--radius-sm);object-fit:cover;cursor:pointer;border:1px solid var(--border-color)" onclick="app.viewImg('/${r.photo}')">` : '<div style="width:40px;height:40px;border-radius:var(--radius-sm);background:rgba(0,0,0,0.3);display:flex;align-items:center;justify-content:center;color:var(--text-muted)">📷</div>'}
                </td>
                <td>
                    <div style="font-weight:600;color:var(--text-main)">${r.name}</div>
                    <div style="font-size:0.8rem;color:var(--text-muted)">${r.address || '-'} <br/> ${r.date || ''}</div>
                </td>
                <td>
                    <div style="color:var(--primary-gold);font-weight:600">${r.acres || 0} <span style="font-size:0.8rem;color:var(--text-muted)">ac</span></div>
                    <div style="font-size:0.8rem;color:var(--text-muted)">${r.crop || '-'}</div>
                </td>
                <td class="tabular-nums">
                    <div><span class="text-muted">Tot:</span> ₹${this.fmt(r.amount)}</div>
                    <div><span class="text-muted">Bal:</span> <span style="color:${r.balance > 0 ? 'var(--danger-color)' : 'var(--primary-green)'};font-weight:700">₹${this.fmt(r.balance)}</span></div>
                </td>
                <td class="tabular-nums">
                    <div style="font-size:0.8rem"><span class="text-muted">Veh:</span> ${r.vehicle || '-'}</div>
                    <div style="font-size:0.8rem"><span class="text-muted">Fuel:</span> ₹${this.fmt(r.fuelCost)}</div>
                </td>
                <td>
                    <span class="badge ${r.paid ? 'badge-success' : 'badge-warning'}" style="cursor:pointer" onclick="app.markPaid(${r.id})">
                        ${r.paid ? '✅ Paid' : '⚠️ Pending'}
                    </span>
                </td>
                <td class="action-cell">
                    <button class="btn btn-danger" style="padding:6px 12px; font-size:0.8rem" onclick="app.delRecord(${r.id})">Delete</button>
                </td>
            </tr>
        `).join('');
    },

    async markPaid(id) {
        try {
            const res = await ApiService.togglePaid(id);
            this.toast(res.data.message);
            this.loadDashboard(true);
            this.loadRecords(false);
        } catch (e) {
            this.toast(e.message, 'error');
        }
    },

    async delRecord(id) {
        if (!confirm('Are you sure you want to permanently delete this record?')) return;
        try {
            await ApiService.deleteRecord(id);
            this.toast('Record deleted successfully');
            this.loadDashboard(true);
            this.loadRecords(false);
        } catch (e) {
            this.toast(e.message, 'error');
        }
    },

    calcAmount() {
        const acres = parseFloat(document.getElementById('i_acres').value) || 0;
        const rate = parseFloat(document.getElementById('i_rate').value) || 0;
        const total = acres * rate;
        document.getElementById('i_amount').value = total.toFixed(2);
        
        const collected = parseFloat(document.getElementById('i_collected').value) || 0;
        document.getElementById('i_balance').value = Math.max(0, total - collected).toFixed(2);
    },

    calcFuel() {
        const lts = parseFloat(document.getElementById('i_fuell').value) || 0;
        const rate = parseFloat(document.getElementById('i_fuelr').value) || 0;
        document.getElementById('i_fuelc').value = (lts * rate).toFixed(2);
    },

    previewPhoto(input) {
        const file = input.files[0];
        if (!file) return;
        const fr = new FileReader();
        fr.onload = (e) => {
            document.getElementById('photoBox').style.display = 'none';
            const img = document.getElementById('photoPreview');
            img.src = e.target.result;
            img.style.display = 'block';
        };
        fr.readAsDataURL(file);
    },

    async handleEntrySubmit(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSaveEntry');
        const originalHtml = btn.innerHTML;

        try {
            btn.innerHTML = '<div class="loader"></div> Saving...';
            btn.disabled = true;

            const photoInput = document.getElementById('i_photo');
            let photoB64 = null;
            if (photoInput.files[0]) {
                photoB64 = await new Promise((res) => {
                    const fr = new FileReader();
                    fr.onload = e => res(e.target.result);
                    fr.readAsDataURL(photoInput.files[0]);
                });
            }

            const data = {
                name: document.getElementById('i_name').value.trim(),
                phone: document.getElementById('i_phone').value.trim(),
                address: document.getElementById('i_address').value.trim(),
                date: document.getElementById('i_date').value,
                acres: parseFloat(document.getElementById('i_acres').value) || 0,
                crop: document.getElementById('i_crop').value,
                rate: parseFloat(document.getElementById('i_rate').value) || 0,
                amount: parseFloat(document.getElementById('i_amount').value) || 0,
                collected: parseFloat(document.getElementById('i_collected').value) || 0,
                balance: parseFloat(document.getElementById('i_balance').value) || 0,
                vehicle: document.getElementById('i_vehicle').value.trim(),
                readStart: parseFloat(document.getElementById('i_rstart').value) || 0,
                readEnd: parseFloat(document.getElementById('i_rend').value) || 0,
                fuelL: parseFloat(document.getElementById('i_fuell').value) || 0,
                fuelRate: parseFloat(document.getElementById('i_fuelr').value) || 0,
                fuelCost: parseFloat(document.getElementById('i_fuelc').value) || 0,
                notes: document.getElementById('i_notes').value.trim(),
                photo: photoB64
            };

            await ApiService.createRecord(data);
            this.toast('Entry Saved Successfully!');
            this.clearForm();
            this.loadDashboard(true);
            
            setTimeout(() => this.switchView('records'), 800);

        } catch (err) {
            this.toast(err.message, 'error');
        } finally {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        }
    },

    clearForm() {
        document.getElementById('entryForm').reset();
        document.getElementById('i_date').valueAsDate = new Date();
        document.getElementById('i_fuelr').value = 100;
        document.getElementById('photoBox').style.display = 'block';
        document.getElementById('photoPreview').style.display = 'none';
        document.getElementById('photoPreview').src = '';
    },

    viewImg(src) {
        document.getElementById('imgModalSrc').src = src;
        document.getElementById('imgModal').classList.add('open');
    },

    renderDaily() {
        const tgtDate = document.getElementById('dailyDate').value;
        const dailyRecords = this.records.filter(r => r.date === tgtDate);
        
        const container = document.getElementById('dailyContent');
        if (dailyRecords.length === 0) {
            container.innerHTML = `<div class="text-muted" style="text-align:center; padding: 40px; background:var(--bg-card); border-radius:var(--radius-md)">No records found for ${tgtDate}</div>`;
            return;
        }

        const totals = dailyRecords.reduce((acc, r) => {
            acc.acres += (r.acres || 0);
            acc.collected += (r.collected || 0);
            acc.fuel += (r.fuelCost || 0);
            return acc;
        }, { acres: 0, collected: 0, fuel: 0 });

        let html = `
            <div style="display:flex; justify-content:space-between; margin-bottom: 24px; padding-bottom: 16px; border-bottom:1px solid var(--border-color)">
                <div class="stat-value gold">₹${this.fmt(totals.collected)} <div class="stat-title" style="margin:0">Total Collected</div></div>
                <div class="stat-value green">${this.fmt(totals.acres)} <div class="stat-title" style="margin:0">Acres</div></div>
                <div class="stat-value" style="color:var(--danger-color)">₹${this.fmt(totals.fuel)} <div class="stat-title" style="margin:0">Fuel Expense</div></div>
            </div>
            <div style="display:flex; flex-direction:column; gap:16px;">
        `;

        html += dailyRecords.map(r => `
            <div class="glass-card" style="padding:16px; border-left: 4px solid ${r.paid ? 'var(--primary-green)' : 'var(--danger-color)'}">
                <div style="display:flex; justify-content:space-between; margin-bottom:8px">
                    <strong style="color:var(--primary-gold); font-size:1.1rem">${r.name}</strong>
                    <span class="badge ${r.paid?'badge-success':'badge-warning'}">${r.paid?'Paid':'Pending'}</span>
                </div>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:8px; font-size:0.9rem; color:var(--text-muted)">
                    <div><span style="color:var(--text-main)">Acres:</span> ${r.acres} (${r.crop || '-'})</div>
                    <div><span style="color:var(--text-main)">Amount:</span> ₹${this.fmt(r.amount)}</div>
                    <div><span style="color:var(--text-main)">Collected:</span> <span class="text-green">₹${this.fmt(r.collected)}</span></div>
                    <div><span style="color:var(--text-main)">Balance:</span> <span class="text-danger">₹${this.fmt(r.balance)}</span></div>
                </div>
            </div>
        `).join('');
        html += `</div>`;

        container.innerHTML = html;
    }
};

window.onload = () => app.init();
