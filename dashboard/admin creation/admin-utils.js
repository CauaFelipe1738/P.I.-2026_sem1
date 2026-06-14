const Admin = {
  $: (selector, root = document) => root.querySelector(selector),
  $$: (selector, root = document) => [...root.querySelectorAll(selector)],
  apiBase: window.API_BASE_URL || "",
  escape: (value) => String(value ?? "")
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;"),
  read(key, fallback = []) {
    try {
      const value = JSON.parse(localStorage.getItem(key) || JSON.stringify(fallback));
      return Array.isArray(fallback) && !Array.isArray(value) ? fallback : value;
    } catch {
      return fallback;
    }
  },
  write: (key, value) => localStorage.setItem(key, JSON.stringify(value)),
  icon: (name) => ({
    edit: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="m16 4 4 4L8 20H4v-4L16 4Z"></path><path d="m14 6 4 4"></path></svg>',
    delete: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 6h18"></path><path d="M8 6V4h8v2"></path><path d="M19 6 18 20H6L5 6"></path><path d="M10 11v5"></path><path d="M14 11v5"></path></svg>'
  }[name] || ""),
  footer(el, total, showing, label) {
    if (el) el.textContent = total ? `Mostrando ${showing} de ${total} ${label}` : `Nenhum ${label.slice(0, -1)} encontrado`;
  },
  async request(url, options = {}) {
    const response = await fetch(url, {
      ...options,
      headers: { Accept: "application/json", "Content-Type": "application/json", ...options.headers }
    });
    if (!response.ok) throw new Error(`Erro ${response.status}`);
    return response.status === 204 ? null : response.json();
  },
  async firstAvailable(endpoints, listKeys, normalize) {
    for (const endpoint of endpoints) {
      try {
        const data = await Admin.request(endpoint);
        const listKey = listKeys.find((key) => Array.isArray(data?.[key]));
        const list = Array.isArray(data) ? data : listKey ? data[listKey] : [];
        if (Array.isArray(list)) return { endpoint, items: list.map((item, index) => normalize(item, index, "api")) };
      } catch (error) {
        console.warn(`Nao foi possivel carregar ${endpoint}:`, error.message);
      }
    }
    return { endpoint: "", items: [] };
  },
  toast(message, type = "success") {
    let toast = Admin.$(".page-toast");
    if (!toast) {
      toast = document.createElement("div");
      toast.className = "page-toast";
      document.body.appendChild(toast);
    }
    toast.textContent = message;
    toast.classList.toggle("is-error", type === "error");
    toast.classList.add("is-visible");
    clearTimeout(Admin.toastTimer);
    Admin.toastTimer = setTimeout(() => toast.classList.remove("is-visible"), 2600);
  }
};
