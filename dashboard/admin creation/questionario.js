const API_BASE_URL = window.API_BASE_URL || "";
const QUESTIONNAIRE_ENDPOINTS = [
    `${API_BASE_URL}/api/questionarios`,
    `${API_BASE_URL}/api/listas`,
    `${API_BASE_URL}/api/quest`,
    `${API_BASE_URL}/api/questionnaires`
];
const QUESTIONNAIRES_STORAGE_KEY = "questionariosAdmin";
const UPDATE_QUESTIONNAIRE_PAGE = "./update_quest.html";

const tableBody = document.querySelector("#questionnaires-table-body");
const searchInput = document.querySelector('.search-field input[type="search"]');
const footerText = document.querySelector(".table-footer span");

let questionnaires = [];
let apiEndpoint = "";

function escapeHtml(value) {
    return String(value ?? "")
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}

function normalizeQuestionnaire(questionnaire, index, source = "api") {
    return {
        id: questionnaire.id_lista || questionnaire.id_questionario || questionnaire.id || `local-${index}`,
        startDate: questionnaire.inicio || questionnaire.data_inicio || questionnaire.startDate || questionnaire.dataInicio || "",
        endDate: questionnaire.fim || questionnaire.data_fim || questionnaire.endDate || questionnaire.dataFim || "",
        source
    };
}

function formatDate(value) {
    if (!value) {
        return "Sem data";
    }

    const dateOnly = String(value).slice(0, 10);
    const [year, month, day] = dateOnly.split("-");

    if (!year || !month || !day) {
        return value;
    }

    return `${day}/${month}/${year}`;
}

function getStoredQuestionnaires() {
    const storedQuestionnaires = JSON.parse(localStorage.getItem(QUESTIONNAIRES_STORAGE_KEY) || "[]");
    return storedQuestionnaires.map((questionnaire, index) => normalizeQuestionnaire(questionnaire, index, "local"));
}

function saveStoredQuestionnaires(nextQuestionnaires) {
    const localQuestionnaires = nextQuestionnaires
        .filter((questionnaire) => questionnaire.source === "local")
        .map((questionnaire) => ({
            id: questionnaire.id,
            inicio: questionnaire.startDate,
            fim: questionnaire.endDate
        }));

    localStorage.setItem(QUESTIONNAIRES_STORAGE_KEY, JSON.stringify(localQuestionnaires));
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
        ? `Mostrando ${showing} de ${total} questionarios`
        : "Nenhum questionario encontrado";
}

function renderQuestionnaires() {
    const search = searchInput.value.trim().toLowerCase();
    const filteredQuestionnaires = questionnaires.filter((questionnaire) => {
        const searchable = `${questionnaire.id} ${questionnaire.startDate} ${questionnaire.endDate}`.toLowerCase();
        return searchable.includes(search);
    });

    if (!filteredQuestionnaires.length) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="4">Nenhum questionario encontrado.</td>
            </tr>
        `;
        updateFooter(questionnaires.length, 0);
        return;
    }

    tableBody.innerHTML = filteredQuestionnaires.map((questionnaire) => `
        <tr data-questionnaire-id="${escapeHtml(questionnaire.id)}">
            <td>
                <div class="user-cell">${escapeHtml(questionnaire.id)}</div>
            </td>
            <td>${escapeHtml(formatDate(questionnaire.startDate))}</td>
            <td>${escapeHtml(formatDate(questionnaire.endDate))}</td>
            <td>
                <div class="actions">
                    <button class="icon-button edit-questionnaire" type="button" aria-label="Editar questionario ${escapeHtml(questionnaire.id)}">
                        ${getEditIcon()}
                    </button>
                    <button class="icon-button delete-questionnaire" type="button" aria-label="Excluir questionario ${escapeHtml(questionnaire.id)}">
                        ${getDeleteIcon()}
                    </button>
                </div>
            </td>
        </tr>
    `).join("");

    updateFooter(questionnaires.length, filteredQuestionnaires.length);
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

async function loadQuestionnairesFromApi() {
    for (const endpoint of QUESTIONNAIRE_ENDPOINTS) {
        try {
            const data = await requestJson(endpoint);
            const list = Array.isArray(data)
                ? data
                : data.questionarios || data.listas || data.quest || data.questionnaires || [];

            if (Array.isArray(list)) {
                apiEndpoint = endpoint;
                return list.map((questionnaire, index) => normalizeQuestionnaire(questionnaire, index, "api"));
            }
        } catch (error) {
            console.warn(`Nao foi possivel carregar ${endpoint}:`, error.message);
        }
    }

    return [];
}

async function loadQuestionnaires() {
    tableBody.innerHTML = `
        <tr>
            <td colspan="4">Carregando questionarios...</td>
        </tr>
    `;

    const apiQuestionnaires = await loadQuestionnairesFromApi();
    const storedQuestionnaires = getStoredQuestionnaires();

    questionnaires = apiQuestionnaires.length ? apiQuestionnaires : storedQuestionnaires;
    renderQuestionnaires();
}

function editQuestionnaire(questionnaire) {
    sessionStorage.setItem("questionarioEditando", JSON.stringify(questionnaire));
    window.location.href = `${UPDATE_QUESTIONNAIRE_PAGE}?id=${encodeURIComponent(questionnaire.id)}&source=${encodeURIComponent(questionnaire.source)}`;
}

async function deleteQuestionnaire(questionnaire) {
    if (!confirm(`Deseja excluir o questionario ${questionnaire.id}?`)) {
        return;
    }

    if (questionnaire.source === "api" && apiEndpoint) {
        await requestJson(`${apiEndpoint}/${encodeURIComponent(questionnaire.id)}`, {
            method: "DELETE"
        });
    }

    questionnaires = questionnaires.filter((item) => item.id !== questionnaire.id);
    saveStoredQuestionnaires(questionnaires);
    renderQuestionnaires();
}

searchInput.addEventListener("input", renderQuestionnaires);

tableBody.addEventListener("click", async (event) => {
    const editButton = event.target.closest(".edit-questionnaire");
    const deleteButton = event.target.closest(".delete-questionnaire");

    if (!editButton && !deleteButton) {
        return;
    }

    const row = event.target.closest("tr");
    const questionnaire = questionnaires.find((item) => String(item.id) === row.dataset.questionnaireId);

    if (!questionnaire) {
        return;
    }

    if (editButton) {
        editQuestionnaire(questionnaire);
        return;
    }

    deleteButton.disabled = true;

    try {
        await deleteQuestionnaire(questionnaire);
    } catch (error) {
        alert("Nao foi possivel excluir o questionario. Verifique se a API esta funcionando.");
        deleteButton.disabled = false;
        console.error(error);
    }
});

loadQuestionnaires();
