const THEMES_KEY = "temasPergunta";
const QUESTIONS_KEY = "perguntasAdmin";
const CREATED_KEY = "perguntaCriada";
const form = Admin.$(".question-form");
const fields = {
  text: Admin.$("#question-text"),
  theme: Admin.$("#question-theme"),
  image: Admin.$("#question-image"),
  score: Admin.$("#question-score")
};

const normalize = (value) => value.trim().replace(/\s+/g, " ");
const themes = () => [...new Set(Admin.read(THEMES_KEY).map(normalize).filter(Boolean))];
const themeFromUrl = () => normalize(new URLSearchParams(location.search).get("theme") || "");
const setInvalid = (field, invalid = true) => field?.classList.toggle("is-invalid", invalid);
const updateCounter = () => {
  const counter = fields.text?.closest(".textarea-wrap")?.querySelector("small");
  if (counter) counter.textContent = `${fields.text.value.length}/${fields.text.maxLength}`;
};

function renderThemes(selected = "") {
  if (!fields.theme) return;
  fields.theme.innerHTML = '<option value="">Selecione um tema</option>';
  const options = [...new Set([...themes(), selected].map(normalize).filter(Boolean))];
  options.forEach((theme) => fields.theme.add(new Option(theme, theme, false, theme === selected)));
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
  setTimeout(() => location.href = "./pergunta.html", 600);
}

fields.text?.addEventListener("input", updateCounter);
Admin.$("#save-question")?.addEventListener("click", saveQuestion);

updateCounter();
renderThemes(themeFromUrl());
