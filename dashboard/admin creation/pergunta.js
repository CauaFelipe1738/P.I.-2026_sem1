const endpoints = ["/api/perguntas", "/api/pergunta", "/api/questions"].map((path) => Admin.apiBase + path);
const CREATED_KEY = "perguntaCriada";
const STORAGE_KEY = "perguntasAdmin";
const THEMES_KEY = "temasPergunta";
const tableBody = Admin.$("#questions-table-body");
const searchInput = Admin.$('.search-field input[type="search"]');
const themeFilter = Admin.$("#question-theme-filter");
const footerText = Admin.$(".table-footer span");
const editSelectedButton = Admin.$("#edit-selected-question");
const deleteSelectedButton = Admin.$("#delete-selected-question");

let questions = [];
let apiEndpoint = "";
let selectedId = "";

const normalize = (question, index, source = "api") => ({
  id: question.id_pergunta || question.id || `local-${index}`,
  text: question.pergunta || question.question || question.texto || question.text || "Sem pergunta",
  area: question.nome_area || question.area || question.theme || question.tema || question.idf_area || "Sem area",
  value: question.valor ?? question.score ?? question.pontuacao ?? 0,
  image: question.image || question.imagem || "",
  answers: question.answers || question.respostas || [],
  source
});
const readLocal = () => {
  const list = Admin.read(STORAGE_KEY);
  const last = Admin.read(CREATED_KEY, null);
  return (last && !list.length ? [last] : list).map((item, index) => normalize(item, index, "local"));
};
const saveLocal = () => {
  const local = questions.filter((item) => item.source === "local").map((item) => ({
    id: item.id,
    question: item.text,
    theme: item.area,
    score: item.value,
    image: item.image,
    answers: item.answers
  }));
  Admin.write(STORAGE_KEY, local);
  if (!local.length) localStorage.removeItem(CREATED_KEY);
};
const selectedQuestion = () => questions.find((item) => String(item.id) === String(selectedId));
const updateActions = () => {
  const disabled = !selectedQuestion();
  if (editSelectedButton) editSelectedButton.disabled = disabled;
  if (deleteSelectedButton) deleteSelectedButton.disabled = disabled;
};
const edit = (question) => {
  sessionStorage.setItem("perguntaEditando", JSON.stringify(question));
  location.href = `./update_pergunta.html?id=${encodeURIComponent(question.id)}&source=${encodeURIComponent(question.source)}`;
};
const remove = async (question) => {
  if (!confirm(`Deseja excluir a pergunta ${question.id}?`)) return;
  if (question.source === "api" && apiEndpoint) await Admin.request(`${apiEndpoint}/${encodeURIComponent(question.id)}`, { method: "DELETE" });
  questions = questions.filter((item) => String(item.id) !== String(question.id));
  selectedId = "";
  saveLocal();
  renderThemes();
  render();
};

function renderThemes() {
  if (!themeFilter) return;
  const selected = themeFilter.value;
  const themes = [...new Set([...Admin.read(THEMES_KEY), ...questions.map((item) => item.area).filter(Boolean)])].sort((a, b) => a.localeCompare(b));
  themeFilter.innerHTML = '<option value="">Todos os temas</option>';
  themes.forEach((theme) => themeFilter.add(new Option(theme, theme, false, theme === selected)));
}

function render() {
  const search = searchInput.value.trim().toLowerCase();
  const theme = themeFilter?.value || "";
  const filtered = questions.filter((item) => `${item.id} ${item.text} ${item.area} ${item.value}`.toLowerCase().includes(search) && (!theme || item.area === theme));

  tableBody.innerHTML = filtered.length ? filtered.map((item) => `
    <tr data-question-id="${Admin.escape(item.id)}" class="${String(item.id) === String(selectedId) ? "is-selected" : ""}">
      <td><div class="user-cell">${Admin.escape(item.text)}</div></td>
      <td>${Admin.escape(item.area)}</td>
      <td><span class="badge admin">${Admin.escape(item.value)}</span></td>
      <td><div class="actions">
        <button class="icon-button edit-question" type="button" aria-label="Editar pergunta ${Admin.escape(item.id)}">${Admin.icon("edit")}</button>
        <button class="icon-button delete-question" type="button" aria-label="Excluir pergunta ${Admin.escape(item.id)}">${Admin.icon("delete")}</button>
      </div></td>
    </tr>
  `).join("") : '<tr><td colspan="4">Nenhuma pergunta encontrada.</td></tr>';

  if (!filtered.some((item) => String(item.id) === String(selectedId))) selectedId = "";
  Admin.footer(footerText, questions.length, filtered.length, "perguntas");
  updateActions();
}

async function load() {
  tableBody.innerHTML = '<tr><td colspan="4">Carregando perguntas...</td></tr>';
  const api = await Admin.firstAvailable(endpoints, ["perguntas", "questions"], normalize);
  apiEndpoint = api.endpoint;
  questions = api.items.length ? api.items : readLocal();
  renderThemes();
  render();
}

searchInput.addEventListener("input", render);
themeFilter?.addEventListener("change", render);
tableBody.addEventListener("click", async ({ target }) => {
  const row = target.closest("tr[data-question-id]");
  const question = row && questions.find((item) => String(item.id) === row.dataset.questionId);
  const button = target.closest(".edit-question, .delete-question");

  if (row) {
    selectedId = row.dataset.questionId;
    render();
  }
  if (!button || !question) return;
  if (button.matches(".edit-question")) return edit(question);

  button.disabled = true;
  try {
    await remove(question);
  } catch (error) {
    alert("Nao foi possivel excluir a pergunta. Verifique se a API esta funcionando.");
    button.disabled = false;
    console.error(error);
  }
});
editSelectedButton?.addEventListener("click", () => selectedQuestion() && edit(selectedQuestion()));
deleteSelectedButton?.addEventListener("click", async () => selectedQuestion() && remove(selectedQuestion()));

load();
