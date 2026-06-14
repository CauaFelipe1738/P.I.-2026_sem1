const THEMES_KEY = "temasPergunta";
const QUESTIONS_KEY = "perguntasAdmin";
const CREATED_KEY = "perguntaCriada";
const form = Admin.$(".question-form");
const fields = {
  text: Admin.$("#question-text"),
  theme: Admin.$("#question-theme"),
  image: Admin.$("#question-image"),
  score: Admin.$("#question-score"),
  modal: Admin.$("#theme-modal"),
  modalInput: Admin.$("#new-theme-name")
};

const normalize = (value) => value.trim().replace(/\s+/g, " ");
const themes = () => [...new Set(Admin.read(THEMES_KEY).map(normalize).filter(Boolean))];
const setInvalid = (field, invalid = true) => field?.classList.toggle("is-invalid", invalid);
const updateCounter = () => {
  const counter = fields.text?.closest(".textarea-wrap")?.querySelector("small");
  if (counter) counter.textContent = `${fields.text.value.length}/${fields.text.maxLength}`;
};

function renderThemes(selected = "") {
  if (!fields.theme) return;
  fields.theme.innerHTML = '<option value="">Selecione um tema</option>';
  themes().forEach((theme) => fields.theme.add(new Option(theme, theme, false, theme === selected)));
}

function toggleModal(open) {
  fields.modal?.classList.toggle("is-visible", open);
  fields.modal?.setAttribute("aria-hidden", String(!open));
  if (open) {
    fields.modalInput.value = "";
    setInvalid(fields.modalInput, false);
    setTimeout(() => fields.modalInput.focus(), 0);
  }
}

function saveTheme() {
  const theme = normalize(fields.modalInput.value);
  const existing = themes().find((item) => item.toLowerCase() === theme.toLowerCase());
  setInvalid(fields.modalInput, false);

  if (!theme) {
    setInvalid(fields.modalInput);
    return Admin.toast("Digite o nome do tema.", "error");
  }
  if (!existing) Admin.write(THEMES_KEY, [...themes(), theme]);
  renderThemes(existing || theme);
  toggleModal(false);
  Admin.toast(existing ? "Tema selecionado." : "Tema criado com sucesso.");
}

function getData() {
  return {
    id: Date.now(),
    question: fields.text.value.trim(),
    theme: fields.theme.value.trim(),
    image: fields.image.value.trim(),
    score: Number(fields.score.value),
    answers: []
  };
}

function validate(data) {
  form.querySelectorAll(".is-invalid").forEach((item) => setInvalid(item, false));
  if (!data.question) return setInvalid(fields.text), "Digite o texto da pergunta.";
  if (!data.theme) return setInvalid(fields.theme), "Digite o tema da pergunta.";
  if (!Number.isFinite(data.score) || data.score < 0) return setInvalid(fields.score), "Informe uma pontuação válida.";
  return "";
}

function saveQuestion() {
  const data = getData();
  const error = validate(data);
  if (error) return Admin.toast(error, "error");

  Admin.write(CREATED_KEY, data);
  Admin.write(QUESTIONS_KEY, [...Admin.read(QUESTIONS_KEY), data]);
  Admin.toast("Pergunta salva com sucesso.");
}

fields.text?.addEventListener("input", updateCounter);
Admin.$("#open-theme-modal")?.addEventListener("click", () => toggleModal(true));
Admin.$("#close-theme-modal")?.addEventListener("click", () => toggleModal(false));
Admin.$("#cancel-theme-modal")?.addEventListener("click", () => toggleModal(false));
Admin.$("#save-theme")?.addEventListener("click", saveTheme);
fields.modal?.addEventListener("click", ({ target }) => target === fields.modal && toggleModal(false));
fields.modalInput?.addEventListener("keydown", (event) => {
  if (event.key === "Enter") event.preventDefault(), saveTheme();
  if (event.key === "Escape") toggleModal(false);
});
Admin.$("#save-question")?.addEventListener("click", saveQuestion);

updateCounter();
renderThemes();
