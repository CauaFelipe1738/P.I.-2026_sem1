const API_BASE_URL = window.API_BASE_URL || "";
const RANKING_ENDPOINTS = [
    `${API_BASE_URL}/api/rankings`,
    `${API_BASE_URL}/api/ranking`,
    `${API_BASE_URL}/api/rank`
];
const STORAGE_KEY = "rankingsAdmin";

const tableBody = document.querySelector("#rankings-table-body");
const searchInput = document.querySelector('.search-field input[type="search"]');
const footerText = document.querySelector(".table-footer span");

let rankings = [];
let apiEndpoint = "";

function escapeHtml(value) {
    return String(value ?? "")
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}

function normalizeRanking(ranking, index, source = "api") {
    return {
        id: ranking.id_ranking || ranking.idRanking || ranking.id || `local-${index}`,
        qtdPessoas: Number(ranking.qtd_pessoas ?? ranking.qtdPessoas ?? ranking.quantidade ?? 0),
        titulo: ranking.titulo || ranking.title || "Sem titulo",
        sobre: ranking.sobre || ranking.descricao || ranking.description || "",
        source
    };
}

function toDatabasePayload(ranking) {
    return {
        id_ranking: ranking.id,
        qtd_pessoas: ranking.qtdPessoas,
        titulo: ranking.titulo,
        sobre: ranking.sobre
    };
}

function getStoredRankings() {
    const storedRankings = JSON.parse(localStorage.getItem(STORAGE_KEY) || "[]");
    return storedRankings.map((ranking, index) => normalizeRanking(ranking, index, "local"));
}

function saveStoredRankings(nextRankings) {
    const localRankings = nextRankings
        .filter((ranking) => ranking.source === "local")
        .map(toDatabasePayload);

    localStorage.setItem(STORAGE_KEY, JSON.stringify(localRankings));
}

function getEditIcon() {
    return `
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
            <path d="m16 4 4 4L8 20H4v-4L16 4Z"></path>
            <path d="m14 6 4 4"></path>
        </svg>
    `;
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

function updateFooter(total, showing) {
    footerText.textContent = total
        ? `Mostrando ${showing} de ${total} rankings`
        : "Nenhum ranking encontrado";
}

function renderRankings() {
    const search = searchInput.value.trim().toLowerCase();
    const filteredRankings = rankings.filter((ranking) => {
        const searchable = `${ranking.id} ${ranking.qtdPessoas} ${ranking.titulo} ${ranking.sobre}`.toLowerCase();
        return searchable.includes(search);
    });

    if (!filteredRankings.length) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="5">Nenhum ranking encontrado.</td>
            </tr>
        `;
        updateFooter(rankings.length, 0);
        return;
    }

    tableBody.innerHTML = filteredRankings.map((ranking) => `
        <tr data-ranking-id="${escapeHtml(ranking.id)}">
            <td>
                <div class="user-cell">${escapeHtml(ranking.id)}</div>
            </td>
            <td>${escapeHtml(ranking.qtdPessoas)}</td>
            <td><span class="badge admin">${escapeHtml(ranking.titulo)}</span></td>
            <td>${escapeHtml(ranking.sobre || "Sem descricao")}</td>
            <td>
                <div class="actions">
                    <button class="icon-button edit-ranking" type="button" aria-label="Editar ranking ${escapeHtml(ranking.titulo)}">
                        ${getEditIcon()}
                    </button>
                    <button class="icon-button delete-ranking" type="button" aria-label="Excluir ranking ${escapeHtml(ranking.titulo)}">
                        ${getDeleteIcon()}
                    </button>
                </div>
            </td>
        </tr>
    `).join("");

    updateFooter(rankings.length, filteredRankings.length);
}

async function requestJson(url, options = {}) {
    const response = await fetch(url, {
        ...options,
        headers: {
            "Accept": "application/json",
            "Content-Type": "application/json",
            ...options.headers
        }
    });

    if (!response.ok) {
        throw new Error(`Erro ${response.status}`);
    }

    return response.status === 204 ? null : response.json();
}

async function loadRankingsFromApi() {
    for (const endpoint of RANKING_ENDPOINTS) {
        try {
            const data = await requestJson(endpoint);
            const list = Array.isArray(data)
                ? data
                : data.rankings || data.ranking || data.rank || [];

            if (Array.isArray(list)) {
                apiEndpoint = endpoint;
                return list.map((ranking, index) => normalizeRanking(ranking, index, "api"));
            }
        } catch (error) {
            console.warn(`Nao foi possivel carregar ${endpoint}:`, error.message);
        }
    }

    return [];
}

async function loadRankings() {
    tableBody.innerHTML = `
        <tr>
            <td colspan="5">Carregando rankings...</td>
        </tr>
    `;

    const apiRankings = await loadRankingsFromApi();
    const storedRankings = getStoredRankings();

    rankings = apiRankings.length ? apiRankings : storedRankings;
    renderRankings();
}

async function updateRankingOnApi(ranking) {
    const payload = toDatabasePayload(ranking);
    const url = `${apiEndpoint}/${encodeURIComponent(ranking.id)}`;

    try {
        return await requestJson(url, {
            method: "PUT",
            body: JSON.stringify(payload)
        });
    } catch (error) {
        return requestJson(url, {
            method: "PATCH",
            body: JSON.stringify(payload)
        });
    }
}

async function editRanking(ranking) {
    const titulo = prompt("Titulo do ranking:", ranking.titulo);

    if (titulo === null) {
        return;
    }

    const qtdPessoasValue = prompt("Quantidade de pessoas:", String(ranking.qtdPessoas));

    if (qtdPessoasValue === null) {
        return;
    }

    const qtdPessoas = Number(qtdPessoasValue);

    if (!titulo.trim()) {
        alert("Digite o titulo do ranking.");
        return;
    }

    if (!Number.isInteger(qtdPessoas) || qtdPessoas < 0) {
        alert("Digite uma quantidade de pessoas valida.");
        return;
    }

    const sobre = prompt("Sobre o ranking:", ranking.sobre) ?? ranking.sobre;
    const editedRanking = {
        ...ranking,
        titulo: titulo.trim(),
        qtdPessoas,
        sobre: sobre.trim()
    };

    if (ranking.source === "api" && apiEndpoint) {
        await updateRankingOnApi(editedRanking);
    }

    rankings = rankings.map((item) => String(item.id) === String(ranking.id) ? editedRanking : item);
    saveStoredRankings(rankings);
    renderRankings();
}

async function deleteRanking(ranking) {
    if (!confirm(`Deseja excluir o ranking "${ranking.titulo}"?`)) {
        return;
    }

    if (ranking.source === "api" && apiEndpoint) {
        await requestJson(`${apiEndpoint}/${encodeURIComponent(ranking.id)}`, {
            method: "DELETE"
        });
    }

    rankings = rankings.filter((item) => String(item.id) !== String(ranking.id));
    saveStoredRankings(rankings);
    renderRankings();
}

searchInput.addEventListener("input", renderRankings);

tableBody.addEventListener("click", async (event) => {
    const editButton = event.target.closest(".edit-ranking");
    const deleteButton = event.target.closest(".delete-ranking");

    if (!editButton && !deleteButton) {
        return;
    }

    const row = event.target.closest("tr");
    const ranking = rankings.find((item) => String(item.id) === row.dataset.rankingId);

    if (!ranking) {
        return;
    }

    const actionButton = editButton || deleteButton;
    actionButton.disabled = true;

    try {
        if (editButton) {
            await editRanking(ranking);
            return;
        }

        await deleteRanking(ranking);
    } catch (error) {
        alert("Nao foi possivel concluir a acao. Verifique se a API esta funcionando.");
        console.error(error);
    } finally {
        actionButton.disabled = false;
    }
});

loadRankings();
