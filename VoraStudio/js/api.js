/* ══════════════════════════════════════════════════════════════
   api.js — Client API amb Master Token (JWT sense login)
   ══════════════════════════════════════════════════════════════
   En carregar la pàgina, fa fetch automàtic a /api/public/token
   i guarda el JWT a sessionStorage. Navegant dins el mateix tab
   es reutilitza el token emmagatzemat. Si rep 401, renova
   automàticament.
   ══════════════════════════════════════════════════════════════ */

const API = {
  base: 'https://voracms.voradata.cat',
  token: null,

  init() {
    const stored = sessionStorage.getItem('cms_token');
    if (stored) this.token = stored;
  },

  async fetchToken() {
    const res = await fetch(this.base + '/api/public/token');
    if (!res.ok) throw new Error('Domini no autoritzat');
    this.token = (await res.json()).token;
    sessionStorage.setItem('cms_token', this.token);
  },

  async request(path, options = {}) {
    if (!this.token) await this.fetchToken();

    const headers = { ...options.headers };
    headers['Authorization'] = 'Bearer ' + this.token;

    const res = await fetch(this.base + path, { ...options, headers });

    if (res.status === 401) {
      this.token = null;
      sessionStorage.removeItem('cms_token');
      return this.request(path, options);
    }

    return res.json();
  },

  get(path) {
    return this.request(path);
  },

  post(path, body) {
    return this.request(path, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(body),
    });
  },
};

API.init();

if (!API.token) {
  API.fetchToken().catch(() => {});
}
