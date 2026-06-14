const ENDPOINTS = ["/api/questionarios", "/api/listas"].map((path) => Admin.apiBase + path);
const QUESTIONNAIRES_KEY = "questionariosAdmin";
const QUESTIONS_KEY = "perguntasAdmin";
const CREATED_KEY = "perguntaCriada";
const form = Admin.$("#questionnaire-form");
const saveButton = Admin.$("#save-questionnaire");
const questionPicker = Admin.$("#question-bank-picker");
const bankList = Admin.$("#bank-question-list");
const selectedList = Admin.$("#selected-question-list");
const editing = JSON.parse(sessionStorage.getItem("questionarioEditando") || "null");
const fields = {
  title: Admin.$('input[name="titulo"]'),
  description: Admin.$('textarea[name="descricao"]'),
  start: Admin.$('input[name="data_inicio"]'),
  end: Admin.$('input[name="data_fim"]')
};

let selectedQuestions = editing?.perguntas || editing?.questions || [];

const questionText = (question) => question.pergunta || question.question || question.text || question.texto || "Pergunta sem texto";
const normalizeQuestion = (question, index) => ({ id: question.id || question.id_pergunta || `local-question-${index}`, text: question.text || questionText(question) });
const availableQuestions = () => {
  const questions = [...Admin.read(QUESTIONS_KEY)];
  const last = Admin.read(CREATED_KEY, null);
  const hasLast = last && questions.some((question) => String(question.id || question.id_pergunta) === String(last.id || last.id_pergunta));
  if (last && !hasLast) questions.push(last);
  return questions.map(normalizeQuestion);
};
const data = () => ({
  id: editing?.id || editing?.id_questionario || Date.now(),
  titulo: fields.title?.value.trim() || editing?.titulo || `Questionario ${new Date().toLocaleDateString("pt-BR")}`,
  descricao: fields.description?.value.trim() || editing?.descricao || "",
  inicio: fields.start?.value || "",
  fim: fields.end?.value || "",
  perguntas: selectedQuestions
});
const validate = (item) => !item.inicio
  ? "Informe a data de inicio."
  : !item.fim
    ? "Informe a data de fim."
    : item.fim < item.inicio
      ? "A data de fim nao pode ser anterior a data de inicio."
      : "";

function renderSelected() {
  selectedList.innerHTML = selectedQuestions.length ? selectedQuestions.map((question, index) => `
    <article class="question-item" data-question-id="${Admin.escape(question.id)}">
      <span class="drag-handle" aria-hidden="true"></span>
      <span class="question-number">${index + 1}</span>
      <p>${Admin.escape(question.text || questionText(question))}</p>
      <button class="icon-button remove-question" type="button" aria-label="Excluir questao ${index + 1}">${Admin.icon("delete")}</button>
    </article>
  `).join("") : '<article class="question-item"><span class="drag-handle" aria-hidden="true"></span><span class="question-number">0</span><p>Nenhuma questao selecionada.</p><span></span></article>';
}

function renderBank() {
  const selectedIds = selectedQuestions.map((question) => String(question.id));
  const questions = availableQuestions();
  bankList.innerHTML = questions.length ? questions.map((question) => {
    const selected = selectedIds.includes(String(question.id));
    return `
      <label class="bank-question-option ${selected ? "is-selected" : ""}">
        <input type="checkbox" value="${Admin.escape(question.id)}" ${selected ? "checked disabled" : ""}>
        <span>${Admin.escape(question.text)}</span>
        ${selected ? "<small>Selecionada</small>" : ""}
      </label>
    `;
  }).join("") : '<label class="bank-question-option is-empty"><input type="checkbox" disabled><span>Nenhuma pergunta disponivel no banco.</span></label>';
}

async function save(item) {
  const payload = {
    titulo: item.titulo,
    descricao: item.descricao,
    inicio: item.inicio,
    fim: item.fim,
    perguntas: item.perguntas
  };

  if (!Admin.apiBase && location.protocol === "file:") throw new Error("API indisponivel em arquivo local");

  for (const endpoint of ENDPOINTS) {
    try {
      return await Admin.request(`${endpoint}/${encodeURIComponent(item.id)}`, { method: "PUT", body: JSON.stringify(payload) });
    } catch (error) {
      console.warn(`Nao foi possivel atualizar ${endpoint}:`, error.message);
    }
  }

  throw new Error("API indisponivel");
}

function saveLocal(item) {
  const questionnaires = Admin.read(QUESTIONNAIRES_KEY);
  const index = questionnaires.findIndex((questionnaire) => String(questionnaire.id || questionnaire.id_questionario) === String(item.id));
  if (index >= 0) questionnaires[index] = item;
  else questionnaires.push(item);
  Admin.write(QUESTIONNAIRES_KEY, questionnaires);
}

async function submit() {
  const item = data();
  const error = validate(item);
  if (error) return Admin.toast(error, "error");

  saveButton.disabled = true;
  try {
    await save(item);
  } catch (error) {
    console.warn("API indisponivel. Salvando questionario no localStorage.", error.message);
    saveLocal(item);
  } finally {
    saveButton.disabled = false;
  }

  Admin.toast("Questionario atualizado com sucesso.");
  setTimeout(() => location.href = "./questionario.html", 600);
}

function loadQuestionnaire() {
  if (!editing) return;
  if (fields.title) fields.title.value = editing.titulo || fields.title.value || "";
  if (fields.description) fields.description.value = editing.descricao || "";
  if (fields.start) fields.start.value = editing.inicio || editing.data_inicio || "";
  if (fields.end) fields.end.value = editing.fim || editing.data_fim || "";
  selectedQuestions = selectedQuestions.map(normalizeQuestion);
}

loadQuestionnaire();
renderSelected();

form.addEventListener("submit", (event) => {
  event.preventDefault();
  submit();
});
saveButton.addEventListener("click", submit);
Admin.$("#add-question-from-bank")?.addEventListener("click", () => {
  questionPicker.hidden = !questionPicker.hidden;
  if (!questionPicker.hidden) renderBank();
});
Admin.$("#apply-question-bank")?.addEventListener("click", () => {
  const ids = Admin.$$('input[type="checkbox"]:checked:not(:disabled)', bankList).map((input) => input.value);
  const additions = availableQuestions().filter((question) => ids.includes(String(question.id)));
  selectedQuestions = [...selectedQuestions, ...additions];
  questionPicker.hidden = true;
  renderSelected();
  Admin.toast("Perguntas adicionadas.");
});
Admin.$("#close-question-bank")?.addEventListener("click", () => questionPicker.hidden = true);
selectedList.addEventListener("click", ({ target }) => {
  const button = target.closest(".remove-question");
  if (!button) return;
  selectedQuestions = selectedQuestions.filter((question) => String(question.id) !== button.closest(".question-item").dataset.questionId);
  renderSelected();
  if (!questionPicker.hidden) renderBank();
});
