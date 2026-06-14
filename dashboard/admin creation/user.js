const endpoints = ["/api/funcionarios", "/api/usuarios", "/api/users"].map((path) => Admin.apiBase + path);
const STORAGE_KEY = "usuariosAdmin";
const tableBody = Admin.$("#users-table-body");
const searchInput = Admin.$('.search-field input[type="search"]');
const footerText = Admin.$(".table-footer span");

let users = [];
let apiEndpoint = "";

const initials = (name) => String(name || "?").trim().split(/\s+/).slice(0, 2).map((part) => part[0]?.toUpperCase()).join("") || "?";
const normalize = (user, index, source = "api") => {
  const name = user.nome_funcionario || user.nome || user.name || user.usuario || "Sem nome";
  const admin = Boolean(Number(user.admin)) || user.access === "Administrador" || user.tipo_acesso === "administrador";
  return {
    id: user.id_funcionario || user.id_usuario || user.id || `local-${index}`,
    name,
    username: user.username || user.usuario || user.login || user.email || "",
    role: user.titulo || user.role || user.cargo || `Pontos: ${user.pontos ?? 0}`,
    access: admin ? "Administrador" : "Usuario",
    avatar: user.avatar || initials(name),
    color: user.color || (admin ? "am" : "cs"),
    source
  };
};
const saveLocal = () => Admin.write(STORAGE_KEY, users.filter((user) => user.source === "local").map(({ name, username, role, access, avatar, color }) => ({ name, username, role, access, avatar, color })));
const edit = (user) => {
  sessionStorage.setItem("usuarioEditando", JSON.stringify(user));
  location.href = `./update_user.html?id=${encodeURIComponent(user.id)}&source=${encodeURIComponent(user.source)}`;
};
const remove = async (user) => {
  if (!confirm(`Deseja excluir ${user.name}?`)) return;
  if (user.source === "api" && apiEndpoint) await Admin.request(`${apiEndpoint}/${encodeURIComponent(user.id)}`, { method: "DELETE" });
  users = users.filter((item) => String(item.id) !== String(user.id));
  saveLocal();
  render();
};

function render() {
  const search = searchInput.value.trim().toLowerCase();
  const filtered = users.filter((user) => `${user.name} ${user.username} ${user.role} ${user.access}`.toLowerCase().includes(search));

  tableBody.innerHTML = filtered.length ? filtered.map((user) => `
    <tr data-user-id="${Admin.escape(user.id)}">
      <td><div class="user-cell"><span class="avatar ${Admin.escape(user.color)}">${Admin.escape(user.avatar)}</span><span class="text-ellipsis">${Admin.escape(user.name)}</span></div></td>
      <td><span class="text-ellipsis">${Admin.escape(user.username || "@sem-usuario")}</span></td>
      <td><span class="text-ellipsis">${Admin.escape(user.role)}</span></td>
      <td><span class="badge ${user.access === "Administrador" ? "admin" : "user"}">${Admin.escape(user.access)}</span></td>
      <td><div class="actions">
        <button class="icon-button edit-user" type="button" aria-label="Editar ${Admin.escape(user.name)}">${Admin.icon("edit")}</button>
        <button class="icon-button delete-user" type="button" aria-label="Excluir ${Admin.escape(user.name)}">${Admin.icon("delete")}</button>
      </div></td>
    </tr>
  `).join("") : '<tr><td colspan="5">Nenhum usuario encontrado.</td></tr>';

  Admin.footer(footerText, users.length, filtered.length, "usuarios");
}

async function load() {
  tableBody.innerHTML = '<tr><td colspan="5">Carregando usuarios...</td></tr>';
  const api = await Admin.firstAvailable(endpoints, ["funcionarios", "usuarios", "users"], normalize);
  apiEndpoint = api.endpoint;
  users = api.items.length ? api.items : Admin.read(STORAGE_KEY).map((user, index) => normalize(user, index, "local"));
  render();
}

searchInput.addEventListener("input", render);
tableBody.addEventListener("click", async ({ target }) => {
  const button = target.closest(".edit-user, .delete-user");
  const row = target.closest("tr[data-user-id]");
  const user = row && users.find((item) => String(item.id) === row.dataset.userId);
  if (!button || !user) return;
  if (button.matches(".edit-user")) return edit(user);

  button.disabled = true;
  try {
    await remove(user);
  } catch (error) {
    alert("Nao foi possivel excluir o usuario. Verifique se a API esta funcionando.");
    button.disabled = false;
    console.error(error);
  }
});

load();
