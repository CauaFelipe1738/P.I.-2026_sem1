const API_BASE_URL = window.API_BASE_URL || "";
const USERS_ENDPOINTS = [
    `${API_BASE_URL}/api/funcionarios`,
    `${API_BASE_URL}/api/usuarios`,
    `${API_BASE_URL}/api/users`
];
const STORAGE_KEY = "usuariosAdmin";

const tableBody = document.querySelector("#users-table-body");
const searchInput = document.querySelector('.search-field input[type="search"]');
const footerText = document.querySelector(".table-footer span");

let users = [];
let apiEndpoint = "";

function getInitials(name) {
    return String(name || "?")
        .trim()
        .split(/\s+/)
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0].toUpperCase())
        .join("") || "?";
}

function normalizeUser(user, index, source = "api") {
    const name = user.nome_funcionario || user.nome || user.name || user.usuario || "Sem nome";
    const isAdmin = Boolean(Number(user.admin)) || user.access === "Administrador" || user.tipo_acesso === "administrador";

    return {
        id: user.id_funcionario || user.id_usuario || user.id || `local-${index}`,
        name,
        role: user.titulo || user.role || user.cargo || `Pontos: ${user.pontos ?? 0}`,
        access: isAdmin ? "Administrador" : "Usuario",
        avatar: user.avatar || getInitials(name),
        color: user.color || (isAdmin ? "am" : "cs"),
        source
    };
}

function getStoredUsers() {
    const storedUsers = JSON.parse(localStorage.getItem(STORAGE_KEY) || "[]");
    return storedUsers.map((user, index) => normalizeUser(user, index, "local"));
}

function saveStoredUsers(nextUsers) {
    const localUsers = nextUsers
        .filter((user) => user.source === "local")
        .map((user) => ({
            name: user.name,
            role: user.role,
            access: user.access,
            avatar: user.avatar,
            color: user.color
        }));

    localStorage.setItem(STORAGE_KEY, JSON.stringify(localUsers));
}

function escapeHtml(value) {
    return String(value)
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}

function getDeleteIcon() {
    return `
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
            <path d="M3 6h18"></path>
            <path d="M8 6V4h8v2"></path>
            <path d="M19 6 18 20H6L5 6"></path>
            <path d="M10 11v5"></path>
            <path d="M14 11v5"></path>
        </svg>
    `;
}

function getEditIcon() {
    return `
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
            <path d="m16 4 4 4L8 20H4v-4L16 4Z"></path>
            <path d="m14 6 4 4"></path>
        </svg>
    `;
}

function updateFooter(total, showing) {
    footerText.textContent = total
        ? `Mostrando ${showing} de ${total} usuarios`
        : "Nenhum usuario encontrado";
}

function editUser(user) {
    sessionStorage.setItem("usuarioEditando", JSON.stringify(user));
    window.location.href = `./update_user.html?id=${encodeURIComponent(user.id)}&source=${encodeURIComponent(user.source)}`;
}

function renderUsers() {
    const search = searchInput.value.trim().toLowerCase();
    const filteredUsers = users.filter((user) => {
        const searchable = `${user.name} ${user.role} ${user.access}`.toLowerCase();
        return searchable.includes(search);
    });

    if (!filteredUsers.length) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="4">Nenhum usuario encontrado.</td>
            </tr>
        `;
        updateFooter(users.length, 0);
        return;
    }

    tableBody.innerHTML = filteredUsers.map((user) => {
        const accessClass = user.access === "Administrador" ? "admin" : "user";

        return `
            <tr data-user-id="${escapeHtml(user.id)}">
                <td>
                    <div class="user-cell">
                        <span class="avatar ${escapeHtml(user.color)}">${escapeHtml(user.avatar)}</span>
                        ${escapeHtml(user.name)}
                    </div>
                </td>
                <td>${escapeHtml(user.role)}</td>
                <td><span class="badge ${accessClass}">${escapeHtml(user.access)}</span></td>
                <td>
                    <div class="actions">
                        <button class="icon-button edit-user" type="button" aria-label="Editar ${escapeHtml(user.name)}">
                            ${getEditIcon()}
                        </button>
                        <button class="icon-button delete-user" type="button" aria-label="Excluir ${escapeHtml(user.name)}">
                            ${getDeleteIcon()}
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join("");

    updateFooter(users.length, filteredUsers.length);
}

async function requestJson(url, options = {}) {
    const response = await fetch(url, {
        ...options,
        headers: {
            "Accept": "application/json",
            ...options.headers
        }
    });

    if (!response.ok) {
        throw new Error(`Erro ${response.status}`);
    }

    return response.status === 204 ? null : response.json();
}

async function loadUsersFromApi() {
    for (const endpoint of USERS_ENDPOINTS) {
        try {
            const data = await requestJson(endpoint);
            const list = Array.isArray(data) ? data : data.funcionarios || data.usuarios || data.users || [];

            if (Array.isArray(list)) {
                apiEndpoint = endpoint;
                return list.map((user, index) => normalizeUser(user, index, "api"));
            }
        } catch (error) {
            console.warn(`Nao foi possivel carregar ${endpoint}:`, error.message);
        }
    }

    return [];
}

async function loadUsers() {
    tableBody.innerHTML = `
        <tr>
            <td colspan="4">Carregando usuarios...</td>
        </tr>
    `;

    const apiUsers = await loadUsersFromApi();
    const storedUsers = getStoredUsers();

    users = apiUsers.length ? apiUsers : storedUsers;
    renderUsers();
}

async function deleteUser(user) {
    if (!confirm(`Deseja excluir ${user.name}?`)) {
        return;
    }

    if (user.source === "api" && apiEndpoint) {
        await requestJson(`${apiEndpoint}/${encodeURIComponent(user.id)}`, {
            method: "DELETE"
        });
    }

    users = users.filter((item) => item.id !== user.id);
    saveStoredUsers(users);
    renderUsers();
}

searchInput.addEventListener("input", renderUsers);

tableBody.addEventListener("click", async (event) => {
    const editButton = event.target.closest(".edit-user");
    const deleteButton = event.target.closest(".delete-user");

    if (!editButton && !deleteButton) {
        return;
    }

    const row = event.target.closest("tr");
    const user = users.find((item) => String(item.id) === row.dataset.userId);

    if (!user) {
        return;
    }

    if (editButton) {
        editUser(user);
        return;
    }

    deleteButton.disabled = true;

    try {
        await deleteUser(user);
    } catch (error) {
        alert("Nao foi possivel excluir o usuario. Verifique se a API esta funcionando.");
        deleteButton.disabled = false;
        console.error(error);
    }
});

loadUsers();
