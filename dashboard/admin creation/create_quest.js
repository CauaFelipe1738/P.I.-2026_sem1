const ENDPOINTS = ["/api/questionarios", "/api/listas"].map((path) => Admin.apiBase + path);
const QUESTIONNAIRES_KEY = "questionariosAdmin";
const QUESTIONS_KEY = "perguntasAdmin";
const CREATED_KEY = "perguntaCriada";
const form = Admin.$("#questionnaire-form");
const saveButton = Admin.$("#save-questionnaire");
const questionPicker = Admin.$("#question-bank-picker");
const bankList = Admin.$("#bank-question-list");
const selectedList = Admin.$("#selected-question-list");
const fields = {
  title: Admin.$('input[name="titulo"]'),
  description: Admin.$('textarea[name="descricao"]'),
  start: Admin.$('input[name="data_inicio"]'),
  end: Admin.$('input[name="data_fim"]'),
  shuffle: Admin.$('input[name="embaralhar"]'),
  showResults: Admin.$('input[name="exibir_resultados"]')
};

let selectedQuestions = [];

const questionText = (question) => question.pergunta || question.question || question.text || question.texto || "Pergunta sem texto";
const availableQuestions = () => {
  const questions = [...Admin.read(QUESTIONS_KEY)];
  const last = Admin.read(CREATED_KEY, null);
  const hasLast = last && questions.some((question) => String(question.id || question.id_pergunta) === String(last.id || last.id_pergunta));
  if (last && !hasLast) questions.push(last);
  return questions.map((question, index) => ({ id: question.id || question.id_pergunta || `local-question-${index}`, text: questionText(question) }));
};
const data = () => ({
  id: Date.now(),
  titulo: fields.title?.value.trim() || `Questionario ${new Date().toLocaleDateString("pt-BR")}`,
  descricao: fields.description?.value.trim() || "",
  inicio: fields.start?.value || "",
  fim: fields.end?.value || "",
  perguntas: selectedQuestions,
  embaralhar: Boolean(fields.shuffle?.checked),
  exibir_resultados: Boolean(fields.showResults?.checked)
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
      <p>${Admin.escape(question.text)}</p>
      <button class="icon-button remove-question" type="button" aria-label="Excluir questao ${index + 1}">${Admin.icon("delete")}</button>
    </article>
  `).join("") : '<article class="question-item"><span class="drag-handle" aria-hidden="true"></span><span class="question-number">0</span><p>Nenhuma questao selecionada.</p><span></span></article>';
}

function renderBank() {
  const questions = availableQuestions();
  bankList.innerHTML = questions.length ? questions.map((question) => `
    <label class="bank-question-option">
      <input type="checkbox" value="${Admin.escape(question.id)}" ${selectedQuestions.some((item) => String(item.id) === String(question.id)) ? "checked" : ""}>
      <span>${Admin.escape(question.text)}</span>
    </label>
  `).join("") : '<label class="bank-question-option"><input type="checkbox" disabled><span>Nenhuma pergunta disponivel no banco.</span></label>';
}

async function save(data) {
  if (!Admin.apiBase && location.protocol === "file:") throw new Error("API indisponivel em arquivo local");
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
    return await Admin.request(ENDPOINTS[0], { method: "POST", body: JSON.stringify(payload) });
  } catch {
    return Admin.request(ENDPOINTS[1], { method: "POST", body: JSON.stringify(payload) });
  }
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
    Admin.write(QUESTIONNAIRES_KEY, [...Admin.read(QUESTIONNAIRES_KEY), item]);
  } finally {
    saveButton.disabled = false;
  }

  Admin.toast("Questionario salvo com sucesso.");
  setTimeout(() => location.href = "./questionario.html", 600);
}

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
  const ids = Admin.$$('input[type="checkbox"]:checked', bankList).map((input) => input.value);
  selectedQuestions = availableQuestions().filter((question) => ids.includes(String(question.id)));
  questionPicker.hidden = true;
  renderSelected();
  Admin.toast("Perguntas selecionadas adicionadas.");
});
Admin.$("#close-question-bank")?.addEventListener("click", () => questionPicker.hidden = true);
selectedList.addEventListener("click", ({ target }) => {
  const button = target.closest(".remove-question");
  if (!button) return;
  selectedQuestions = selectedQuestions.filter((question) => String(question.id) !== button.closest(".question-item").dataset.questionId);
  renderSelected();
  if (!questionPicker.hidden) renderBank();
});

renderSelected();
