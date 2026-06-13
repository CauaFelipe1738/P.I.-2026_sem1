const API_BASE_URL = window.API_BASE_URL || "";
const QUESTIONNAIRES_ENDPOINT = `${API_BASE_URL}/api/questionarios`;
const FALLBACK_QUESTIONNAIRES_ENDPOINT = `${API_BASE_URL}/api/listas`;
const QUESTIONNAIRES_STORAGE_KEY = "questionariosAdmin";
const QUESTIONS_STORAGE_KEY = "perguntasAdmin";
const CREATED_QUESTION_KEY = "perguntaCriada";

const form = document.querySelector("#questionnaire-form");
const saveButton = document.querySelector("#save-questionnaire");
const addQuestionButton = document.querySelector("#add-question-from-bank");
const questionBankPicker = document.querySelector("#question-bank-picker");
const bankQuestionList = document.querySelector("#bank-question-list");
const applyQuestionBankButton = document.querySelector("#apply-question-bank");
const closeQuestionBankButton = document.querySelector("#close-question-bank");
const questionList = document.querySelector("#selected-question-list");
const titleInput = document.querySelector('input[name="titulo"]');
const descriptionInput = document.querySelector('textarea[name="descricao"]');
const startDateInput = document.querySelector('input[name="data_inicio"]');
const endDateInput = document.querySelector('input[name="data_fim"]');
const shuffleInput = document.querySelector('input[name="embaralhar"]');
const showResultsInput = document.querySelector('input[name="exibir_resultados"]');

let selectedQuestions = [];

function escapeHtml(value) {
    return String(value ?? "")
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}

function showMessage(message, type = "success") {
    let toast = document.querySelector(".page-toast");

    if (!toast) {
        toast = document.createElement("div");
        toast.className = "page-toast";
        document.body.appendChild(toast);
    }

    toast.textContent = message;
    toast.style.position = "fixed";
    toast.style.right = "24px";
    toast.style.bottom = "24px";
    toast.style.zIndex = "20";
    toast.style.padding = "14px 18px";
    toast.style.borderRadius = "8px";
    toast.style.color = "#fff";
    toast.style.background = type === "error" ? "#b42318" : "#147cff";
    toast.style.boxShadow = "0 14px 40px rgba(0, 0, 0, 0.35)";

    clearTimeout(showMessage.timer);
    showMessage.timer = setTimeout(() => {
        toast.remove();
    }, 2600);
}

function getQuestionText(question) {
    return question.pergunta || question.question || question.text || question.texto || "Pergunta sem texto";
}

function getAvailableQuestions() {
    const savedQuestions = JSON.parse(localStorage.getItem(QUESTIONS_STORAGE_KEY) || "[]");
    const lastCreatedQuestion = JSON.parse(localStorage.getItem(CREATED_QUESTION_KEY) || "null");
    const questions = Array.isArray(savedQuestions) ? [...savedQuestions] : [];

    const hasLastCreated = lastCreatedQuestion && questions.some((question) => {
        const questionId = question.id || question.id_pergunta;
        const lastQuestionId = lastCreatedQuestion.id || lastCreatedQuestion.id_pergunta;
        return questionId && lastQuestionId && String(questionId) === String(lastQuestionId);
    });

    if (lastCreatedQuestion && !hasLastCreated) {
        questions.push(lastCreatedQuestion);
    }

    return questions.map((question, index) => ({
        id: question.id || question.id_pergunta || `local-question-${index}`,
        text: getQuestionText(question)
    }));
}

function renderSelectedQuestions() {
    if (!questionList) {
        return;
    }

    if (!selectedQuestions.length) {
        questionList.innerHTML = `
            <article class="question-item">
                <span class="drag-handle" aria-hidden="true"></span>
                <span class="question-number">0</span>
                <p>Nenhuma questao selecionada.</p>
                <span></span>
            </article>
        `;
        return;
    }

    questionList.innerHTML = selectedQuestions.map((question, index) => `
        <article class="question-item" data-question-id="${escapeHtml(question.id)}">
            <span class="drag-handle" aria-hidden="true"></span>
            <span class="question-number">${index + 1}</span>
            <p>${escapeHtml(question.text)}</p>
            <button class="icon-button remove-question" type="button" aria-label="Excluir questao ${index + 1}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                    <path d="M3 6h18"></path>
                    <path d="M8 6V4h8v2"></path>
                    <path d="M19 6 18 20H6L5 6"></path>
                    <path d="M10 11v5"></path>
                    <path d="M14 11v5"></path>
                </svg>
            </button>
        </article>
    `).join("");
}

function renderQuestionBank() {
    if (!bankQuestionList) {
        return;
    }

    const availableQuestions = getAvailableQuestions();

    if (!availableQuestions.length) {
        bankQuestionList.innerHTML = `
            <label class="bank-question-option">
                <input type="checkbox" disabled>
                <span>Nenhuma pergunta disponivel no banco.</span>
            </label>
        `;
        return;
    }

    bankQuestionList.innerHTML = availableQuestions.map((question) => {
        const checked = selectedQuestions.some((selectedQuestion) => String(selectedQuestion.id) === String(question.id));

        return `
            <label class="bank-question-option">
                <input type="checkbox" value="${escapeHtml(question.id)}" ${checked ? "checked" : ""}>
                <span>${escapeHtml(question.text)}</span>
            </label>
        `;
    }).join("");
}

function toggleQuestionBank() {
    if (!questionBankPicker) {
        return;
    }

    const willOpen = questionBankPicker.hidden;

    questionBankPicker.hidden = !willOpen;

    if (willOpen) {
        renderQuestionBank();
    }
}

function applyQuestionBankSelection() {
    if (!bankQuestionList || !questionBankPicker) {
        return;
    }

    const availableQuestions = getAvailableQuestions();
    const selectedIds = [...bankQuestionList.querySelectorAll('input[type="checkbox"]:checked')]
        .map((input) => input.value);

    selectedQuestions = availableQuestions.filter((question) => selectedIds.includes(String(question.id)));
    renderSelectedQuestions();
    questionBankPicker.hidden = true;
    showMessage("Perguntas selecionadas adicionadas.");
}

function getQuestionnaireData() {
    return {
        id: Date.now(),
        titulo: titleInput?.value.trim() || `Questionario ${new Date().toLocaleDateString("pt-BR")}`,
        descricao: descriptionInput?.value.trim() || "",
        inicio: startDateInput?.value || "",
        fim: endDateInput?.value || "",
        perguntas: selectedQuestions,
        embaralhar: shuffleInput?.checked || false,
        exibir_resultados: showResultsInput?.checked || false
    };
}

function validateQuestionnaire(data) {
    if (titleInput && !data.titulo) {
        return "Digite o titulo do questionario.";
    }

    if (!data.inicio) {
        return "Informe a data de inicio.";
    }

    if (!data.fim) {
        return "Informe a data de fim.";
    }

    if (data.fim < data.inicio) {
        return "A data de fim nao pode ser anterior a data de inicio.";
    }

    return "";
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

async function saveQuestionnaireToApi(data) {
    if (!API_BASE_URL && window.location.protocol === "file:") {
        throw new Error("API indisponivel em arquivo local");
    }

    const payload = {
        titulo: data.titulo,
        descricao: data.descricao,
        inicio: data.inicio,
        fim: data.fim,
        perguntas: data.perguntas,
        embaralhar: data.embaralhar,
        exibir_resultados: data.exibir_resultados
    };

    try {
        return await requestJson(QUESTIONNAIRES_ENDPOINT, {
            method: "POST",
            body: JSON.stringify(payload)
        });
    } catch (error) {
        return requestJson(FALLBACK_QUESTIONNAIRES_ENDPOINT, {
            method: "POST",
            body: JSON.stringify(payload)
        });
    }
}

function saveQuestionnaireLocally(data) {
    const questionnaires = JSON.parse(localStorage.getItem(QUESTIONNAIRES_STORAGE_KEY) || "[]");

    questionnaires.push(data);
    localStorage.setItem(QUESTIONNAIRES_STORAGE_KEY, JSON.stringify(questionnaires));
}

async function saveQuestionnaire() {
    const data = getQuestionnaireData();
    const error = validateQuestionnaire(data);

    if (error) {
        showMessage(error, "error");
        return;
    }

    saveButton.disabled = true;

    try {
        await saveQuestionnaireToApi(data);
    } catch (error) {
        console.warn("API indisponivel. Salvando questionario no localStorage.", error.message);
        saveQuestionnaireLocally(data);
    } finally {
        saveButton.disabled = false;
    }

    showMessage("Questionario salvo com sucesso.");
    setTimeout(() => {
        window.location.href = "./questionario.html";
    }, 600);
}

form.addEventListener("submit", (event) => {
    event.preventDefault();
    saveQuestionnaire();
});

saveButton.addEventListener("click", saveQuestionnaire);
if (addQuestionButton) {
    addQuestionButton.addEventListener("click", toggleQuestionBank);
}

if (applyQuestionBankButton) {
    applyQuestionBankButton.addEventListener("click", applyQuestionBankSelection);
}

if (closeQuestionBankButton && questionBankPicker) {
    closeQuestionBankButton.addEventListener("click", () => {
        questionBankPicker.hidden = true;
    });
}

if (questionList) {
    questionList.addEventListener("click", (event) => {
        const removeButton = event.target.closest(".remove-question");

        if (!removeButton) {
            return;
        }

        const item = removeButton.closest(".question-item");
        selectedQuestions = selectedQuestions.filter((question) => String(question.id) !== item.dataset.questionId);
        renderSelectedQuestions();

        if (questionBankPicker && !questionBankPicker.hidden) {
            renderQuestionBank();
        }
    });
}

renderSelectedQuestions();
