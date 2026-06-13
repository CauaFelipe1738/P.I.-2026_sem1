const API_BASE_URL = window.API_BASE_URL || "";
const QUESTIONS_ENDPOINTS = [
    `${API_BASE_URL}/api/perguntas`,
    `${API_BASE_URL}/api/pergunta`,
    `${API_BASE_URL}/api/questions`
];
const CREATED_QUESTION_KEY = "perguntaCriada";
const QUESTIONS_STORAGE_KEY = "perguntasAdmin";
const THEMES_STORAGE_KEY = "temasPergunta";
const UPDATE_QUESTION_PAGE = "./update_pergunta.html";

const tableBody = document.querySelector("#questions-table-body");
const searchInput = document.querySelector('.search-field input[type="search"]');
const themeFilter = document.querySelector("#question-theme-filter");
const footerText = document.querySelector(".table-footer span");
const editSelectedButton = document.querySelector("#edit-selected-question");
const deleteSelectedButton = document.querySelector("#delete-selected-question");

let questions = [];
let apiEndpoint = "";
let selectedQuestionId = "";

function escapeHtml(value) {
    return String(value ?? "")
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}

function normalizeQuestion(question, index, source = "api") {
    const text = question.pergunta || question.question || question.texto || question.text || "Sem pergunta";
    const area = question.nome_area || question.area || question.theme || question.tema || question.idf_area || "Sem area";
    const value = question.valor ?? question.score ?? question.pontuacao ?? 0;

    return {
        id: question.id_pergunta || question.id || `local-${index}`,
        text,
        area,
        value,
        image: question.image || question.imagem || "",
        answers: question.answers || question.respostas || [],
        source
    };
}

function getStoredQuestions() {
    const savedQuestions = JSON.parse(localStorage.getItem(QUESTIONS_STORAGE_KEY) || "[]");
    const lastCreated = JSON.parse(localStorage.getItem(CREATED_QUESTION_KEY) || "null");
    const list = Array.isArray(savedQuestions) ? savedQuestions : [];

    if (lastCreated && !list.length) {
        list.push(lastCreated);
    }

    return list.map((question, index) => normalizeQuestion(question, index, "local"));
}

function saveStoredQuestions(nextQuestions) {
    const localQuestions = nextQuestions
        .filter((question) => question.source === "local")
        .map((question) => ({
            id: question.id,
            question: question.text,
            theme: question.area,
            score: question.value,
            image: question.image,
            answers: question.answers
        }));

    localStorage.setItem(QUESTIONS_STORAGE_KEY, JSON.stringify(localQuestions));

    if (!localQuestions.length) {
        localStorage.removeItem(CREATED_QUESTION_KEY);
    }
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
        ? `Mostrando ${showing} de ${total} perguntas`
        : "Nenhuma pergunta encontrada";
}

function getStoredThemes() {
    try {
        const storedThemes = JSON.parse(localStorage.getItem(THEMES_STORAGE_KEY) || "[]");
        return Array.isArray(storedThemes) ? storedThemes : [];
    } catch (error) {
        return [];
    }
}

function renderThemeFilter() {
    if (!themeFilter) {
        return;
    }

    const selectedTheme = themeFilter.value;
    const questionThemes = questions.map((question) => question.area).filter(Boolean);
    const themes = [...new Set([...getStoredThemes(), ...questionThemes])].sort((first, second) => first.localeCompare(second));

    themeFilter.innerHTML = '<option value="">Todos os temas</option>';

    themes.forEach((theme) => {
        const option = document.createElement("option");
        option.value = theme;
        option.textContent = theme;
        option.selected = theme === selectedTheme;
        themeFilter.appendChild(option);
    });
}

function getSelectedQuestion() {
    return questions.find((question) => String(question.id) === String(selectedQuestionId));
}

function updateSelectedActions() {
    const hasSelection = Boolean(getSelectedQuestion());

    if (editSelectedButton) {
        editSelectedButton.disabled = !hasSelection;
    }

    if (deleteSelectedButton) {
        deleteSelectedButton.disabled = !hasSelection;
    }
}

function renderQuestions() {
    const search = searchInput.value.trim().toLowerCase();
    const selectedTheme = themeFilter ? themeFilter.value : "";
    const filteredQuestions = questions.filter((question) => {
        const searchable = `${question.id} ${question.text} ${question.area} ${question.value}`.toLowerCase();
        const matchesSearch = searchable.includes(search);
        const matchesTheme = !selectedTheme || question.area === selectedTheme;

        return matchesSearch && matchesTheme;
    });

    if (!filteredQuestions.length) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="4">Nenhuma pergunta encontrada.</td>
            </tr>
        `;
        selectedQuestionId = "";
        updateFooter(questions.length, 0);
        updateSelectedActions();
        return;
    }

    tableBody.innerHTML = filteredQuestions.map((question) => `
        <tr data-question-id="${escapeHtml(question.id)}" class="${String(question.id) === String(selectedQuestionId) ? "is-selected" : ""}">
            <td>
                <div class="user-cell">
                    ${escapeHtml(question.text)}
                </div>
            </td>
            <td>${escapeHtml(question.area)}</td>
            <td><span class="badge admin">${escapeHtml(question.value)}</span></td>
            <td>
                <div class="actions">
                    <button class="icon-button edit-question" type="button" aria-label="Editar pergunta ${escapeHtml(question.id)}">
                        ${getEditIcon()}
                    </button>
                    <button class="icon-button delete-question" type="button" aria-label="Excluir pergunta ${escapeHtml(question.id)}">
                        ${getDeleteIcon()}
                    </button>
                </div>
            </td>
        </tr>
    `).join("");

    updateFooter(questions.length, filteredQuestions.length);
    updateSelectedActions();
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

async function loadQuestionsFromApi() {
    for (const endpoint of QUESTIONS_ENDPOINTS) {
        try {
            const data = await requestJson(endpoint);
            const list = Array.isArray(data) ? data : data.perguntas || data.questions || [];

            if (Array.isArray(list)) {
                apiEndpoint = endpoint;
                return list.map((question, index) => normalizeQuestion(question, index, "api"));
            }
        } catch (error) {
            console.warn(`Nao foi possivel carregar ${endpoint}:`, error.message);
        }
    }

    return [];
}

async function loadQuestions() {
    tableBody.innerHTML = `
        <tr>
            <td colspan="4">Carregando perguntas...</td>
        </tr>
    `;

    const apiQuestions = await loadQuestionsFromApi();
    const storedQuestions = getStoredQuestions();

    questions = apiQuestions.length ? apiQuestions : storedQuestions;
    renderThemeFilter();
    renderQuestions();
}

function editQuestion(question) {
    sessionStorage.setItem("perguntaEditando", JSON.stringify(question));
    window.location.href = `${UPDATE_QUESTION_PAGE}?id=${encodeURIComponent(question.id)}&source=${encodeURIComponent(question.source)}`;
}

async function deleteQuestion(question) {
    if (!confirm(`Deseja excluir a pergunta ${question.id}?`)) {
        return;
    }

    if (question.source === "api" && apiEndpoint) {
        await requestJson(`${apiEndpoint}/${encodeURIComponent(question.id)}`, {
            method: "DELETE"
        });
    }

    questions = questions.filter((item) => item.id !== question.id);
    selectedQuestionId = "";
    saveStoredQuestions(questions);
    renderQuestions();
}

searchInput.addEventListener("input", renderQuestions);

if (themeFilter) {
    themeFilter.addEventListener("change", renderQuestions);
}

tableBody.addEventListener("click", async (event) => {
    const editButton = event.target.closest(".edit-question");
    const deleteButton = event.target.closest(".delete-question");
    const row = event.target.closest("tr[data-question-id]");

    if (row) {
        selectedQuestionId = row.dataset.questionId;
        renderQuestions();
    }

    if (!editButton && !deleteButton) {
        return;
    }

    const question = questions.find((item) => String(item.id) === row.dataset.questionId);

    if (!question) {
        return;
    }

    if (editButton) {
        editQuestion(question);
        return;
    }

    deleteButton.disabled = true;

    try {
        await deleteQuestion(question);
    } catch (error) {
        alert("Nao foi possivel excluir a pergunta. Verifique se a API esta funcionando.");
        deleteButton.disabled = false;
        console.error(error);
    }
});

if (editSelectedButton) {
    editSelectedButton.addEventListener("click", () => {
        const question = getSelectedQuestion();

        if (question) {
            editQuestion(question);
        }
    });
}

if (deleteSelectedButton) {
    deleteSelectedButton.addEventListener("click", async () => {
        const question = getSelectedQuestion();

        if (!question) {
            return;
        }

        deleteSelectedButton.disabled = true;

        try {
            await deleteQuestion(question);
        } catch (error) {
            alert("Nao foi possivel excluir a pergunta. Verifique se a API esta funcionando.");
            deleteSelectedButton.disabled = false;
            console.error(error);
        }
    });
}

loadQuestions();
