const THEMES_KEY = "temasPergunta";
const QUESTIONS_KEY = "perguntasAdmin";
const CREATED_KEY = "perguntaCriada";
const form = Admin.$(".question-form");
const letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
const answerList = Admin.$(".answer-list");
const imageInput = Admin.$("#question-image");
const imageName = Admin.$("#image-file-name");
const fields = {
  text: Admin.$("#question-text"),
  theme: Admin.$("#question-theme"),
  score: Admin.$("#question-score")
};
const editing = JSON.parse(sessionStorage.getItem("perguntaEditando") || "null");

const normalize = (value) => value.trim().replace(/\s+/g, " ");
const themes = () => [...new Set(Admin.read(THEMES_KEY).map(normalize).filter(Boolean))];
const updateCounter = () => {
  const counter = fields.text?.closest(".textarea-wrap")?.querySelector("small");
  if (counter) counter.textContent = `${fields.text.value.length}/${fields.text.maxLength}`;
};
const setInvalid = (field, invalid = true) => field?.classList.toggle("is-invalid", invalid);
const answerOptions = () => Admin.$$(".answer-option", answerList);

function renderThemes(selected = "") {
  fields.theme.innerHTML = '<option value="">Selecione um tema</option>';
  const options = [...new Set([...themes(), selected].map(normalize).filter(Boolean))];
  options.forEach((theme) => fields.theme.add(new Option(theme, theme, false, theme === selected)));
}

function refreshAnswers() {
  answerOptions().forEach((option, index) => {
    const letter = letters[index] || index + 1;
    Admin.$(".answer-letter", option).textContent = letter;
    Admin.$('input[type="radio"]', option).setAttribute("aria-label", `Marcar alternativa ${letter} como correta`);
    Admin.$(".delete-answer", option).setAttribute("aria-label", `Excluir alternativa ${letter}`);
  });
}

function setCorrect(selected) {
  answerOptions().forEach((option) => {
    const correct = Admin.$('input[type="radio"]', option) === selected;
    option.classList.toggle("correct", correct);
    Admin.$(".correct-badge", option)?.remove();
    if (correct) option.insertBefore(Object.assign(document.createElement("span"), { className: "correct-badge", textContent: "Resposta correta" }), Admin.$(".delete-answer", option));
  });
}

function editable(text) {
  text.contentEditable = "true";
  text.setAttribute("role", "textbox");
  text.setAttribute("aria-label", "Texto da alternativa");
  text.addEventListener("keydown", (event) => event.key === "Enter" && (event.preventDefault(), text.blur()));
}

function addAnswer(text = "Nova resposta") {
  if (answerOptions().length >= letters.length) return Admin.toast("Limite de alternativas atingido.", "error");

  const option = document.createElement("div");
  option.className = "answer-option";
  option.innerHTML = `<span class="answer-letter"></span><input type="radio" name="correct-answer"><span class="answer-text">${Admin.escape(text)}</span><button class="delete-answer" type="button">${Admin.icon("delete")}</button>`;
  answerList.appendChild(option);
  editable(Admin.$(".answer-text", option));
  refreshAnswers();
  Admin.$(".answer-text", option).focus();
}

function getData() {
  return {
    id: editing?.id || editing?.id_pergunta || Date.now(),
    source: editing?.source || "local",
    question: fields.text.value.trim(),
    theme: fields.theme.value.trim(),
    image: imageInput.files[0]?.name || editing?.image || editing?.imagem || "",
    score: Number(fields.score.value),
    answers: answerOptions().map((option, index) => ({
      letter: letters[index] || String(index + 1),
      text: Admin.$(".answer-text", option).textContent.trim(),
      correct: Admin.$('input[type="radio"]', option).checked
    }))
  };
}

function validate(data) {
  form.querySelectorAll(".is-invalid").forEach((item) => setInvalid(item, false));
  const empty = data.answers.findIndex((answer) => !answer.text);
  if (!data.question) return setInvalid(fields.text), "Digite o texto da pergunta.";
  if (!data.theme) return setInvalid(fields.theme), "Digite o tema da pergunta.";
  if (!Number.isFinite(data.score) || data.score < 0) return setInvalid(fields.score), "Informe uma pontuação válida.";
  if (data.answers.length < 2) return "Adicione pelo menos duas respostas.";
  if (empty !== -1) return setInvalid(Admin.$$(".answer-text", answerList)[empty]), "Preencha todas as alternativas.";
  if (!data.answers.some((answer) => answer.correct)) return "Marque uma resposta correta.";
  return "";
}

function saveQuestion() {
  const data = getData();
  const error = validate(data);
  if (error) return Admin.toast(error, "error");

  const questions = Admin.read(QUESTIONS_KEY);
  const index = questions.findIndex((item) => String(item.id || item.id_pergunta) === String(data.id));
  const stored = { id: data.id, question: data.question, theme: data.theme, image: data.image, score: data.score, answers: data.answers };
  if (index >= 0) questions[index] = stored;
  else questions.push(stored);
  Admin.write(QUESTIONS_KEY, questions);
  Admin.write(CREATED_KEY, data);
  Admin.toast("Pergunta salva com sucesso.");
  setTimeout(() => location.href = "./pergunta.html", 600);
}

function loadQuestion() {
  if (!editing) return;
  fields.text.value = editing.text || editing.question || editing.pergunta || "";
  fields.score.value = editing.value ?? editing.score ?? editing.valor ?? "";
  renderThemes(editing.area || editing.theme || editing.tema || "");
  if (imageName) imageName.textContent = editing.image || editing.imagem || "Nenhuma imagem selecionada";
  const answers = editing.answers || editing.respostas || [];
  answerList.innerHTML = "";
  if (answers.length) {
    answers.forEach((answer) => addAnswer(answer.text || answer.resposta || answer.answer || "Nova resposta"));
    const correctIndex = answers.findIndex((answer) => answer.correct || answer.correta);
    if (correctIndex >= 0) {
      const radio = Admin.$$('input[type="radio"]', answerList)[correctIndex];
      radio.checked = true;
      setCorrect(radio);
    }
  } else {
    addAnswer();
    addAnswer();
  }
  updateCounter();
}

fields.text?.addEventListener("input", updateCounter);
imageInput?.addEventListener("change", () => imageName.textContent = imageInput.files[0]?.name || "Nenhuma imagem selecionada");
answerList.addEventListener("change", ({ target }) => target.matches('input[type="radio"]') && setCorrect(target));
answerList.addEventListener("click", ({ target }) => {
  const button = target.closest(".delete-answer");
  if (!button) return;
  if (answerOptions().length <= 2) return Admin.toast("A pergunta precisa ter pelo menos duas respostas.", "error");

  const option = button.closest(".answer-option");
  const wasCorrect = Admin.$('input[type="radio"]', option).checked;
  option.remove();
  if (wasCorrect) {
    const first = Admin.$('input[type="radio"]', answerList);
    first.checked = true;
    setCorrect(first);
  }
  refreshAnswers();
  Admin.toast("Resposta removida.");
});
Admin.$(".add-answer")?.addEventListener("click", () => addAnswer());
Admin.$("#save-question")?.addEventListener("click", saveQuestion);

updateCounter();
renderThemes();
loadQuestion();
if (!answerOptions().length) {
  addAnswer();
  addAnswer();
}
