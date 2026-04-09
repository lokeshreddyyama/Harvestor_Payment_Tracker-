const API_BASE = '/api';

const ApiService = {
  getToken() {
    return sessionStorage.getItem('ht_token') || localStorage.getItem('ht_token') || '';
  },

  setToken(token, remember = false) {
    if (remember) {
      localStorage.setItem('ht_token', token);
    } else {
      sessionStorage.setItem('ht_token', token);
    }
  },

  clearAuth() {
    sessionStorage.removeItem('ht_token');
    sessionStorage.removeItem('ht_name');
    sessionStorage.removeItem('ht_username');
    localStorage.removeItem('ht_token');
  },

  async request(endpoint, options = {}) {
    const url = `${API_BASE}${endpoint}`;
    
    const headers = {
      'X-Auth-Token': this.getToken(),
      ...options.headers,
    };

    if (options.body && !(options.body instanceof FormData)) {
      headers['Content-Type'] = 'application/json';
      options.body = JSON.stringify(options.body);
    }

    try {
      const response = await fetch(url, { ...options, headers });
      const data = await response.json();

      if (!response.ok || !data.success) {
        if (response.status === 401 || (data.error && data.error.toLowerCase().includes('login'))) {
          this.clearAuth();
          window.location.href = 'login.html';
          return null;
        }
        throw new Error(data.error || 'An unexpected error occurred.');
      }
      return data;
    } catch (error) {
      console.error('API Error:', error);
      throw error;
    }
  },

  // Auth Endpoints
  login(credentials) {
    return this.request('/auth/login', { method: 'POST', body: credentials });
  },
  
  register(userData) {
    return this.request('/auth/register', { method: 'POST', body: userData });
  },

  logout() {
    return this.request('/logout', { method: 'POST' });
  },

  checkUsername(username) {
    return this.request(`/public/check_user?username=${encodeURIComponent(username)}`);
  },

  // Data Endpoints
  getStats() {
    return this.request('/stats');
  },

  getRecords(search = '', status = '') {
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (status) params.append('status', status);
    const qs = params.toString();
    return this.request(`/records${qs ? '?' + qs : ''}`);
  },

  createRecord(recordData) {
    return this.request('/records', { method: 'POST', body: recordData });
  },

  updateRecord(id, recordData) {
    return this.request(`/record?id=${id}`, { method: 'PUT', body: recordData });
  },

  deleteRecord(id) {
    return this.request(`/record?id=${id}`, { method: 'DELETE' });
  },

  togglePaid(id) {
    return this.request('/toggle', { method: 'POST', body: { id } });
  },
  
  getExportUrl() {
    return `${API_BASE}/export?token=${encodeURIComponent(this.getToken())}`;
  }
};

window.ApiService = ApiService;
