const endpoints = ["/api/questionarios", "/api/listas", "/api/quest", "/api/questionnaires"].map((path) => Admin.apiBase + path);
const STORAGE_KEY = "questionariosAdmin";
const tableBody = Admin.$("#questionnaires-table-body");
const searchInput = Admin.$('.search-field input[type="search"]');
const footerText = Admin.$(".table-footer span");

let questionnaires = [];
let apiEndpoint = "";

const normalize = (item, index, source = "api") => ({
  id: item.id_lista || item.id_questionario || item.id || `local-${index}`,
  startDate: item.inicio || item.data_inicio || item.startDate || item.dataInicio || "",
  endDate: item.fim || item.data_fim || item.endDate || item.dataFim || "",
  source
});
const formatDate = (value) => {
  const [year, month, day] = String(value || "").slice(0, 10).split("-");
  return year && month && day ? `${day}/${month}/${year}` : value || "Sem data";
};
const saveLocal = () => Admin.write(STORAGE_KEY, questionnaires.filter((item) => item.source === "local").map(({ id, startDate, endDate }) => ({ id, inicio: startDate, fim: endDate })));
const edit = (questionnaire) => {
  sessionStorage.setItem("questionarioEditando", JSON.stringify(questionnaire));
  location.href = `./update_quest.html?id=${encodeURIComponent(questionnaire.id)}&source=${encodeURIComponent(questionnaire.source)}`;
};
const remove = async (questionnaire) => {
  if (!confirm(`Deseja excluir o questionario ${questionnaire.id}?`)) return;
  if (questionnaire.source === "api" && apiEndpoint) await Admin.request(`${apiEndpoint}/${encodeURIComponent(questionnaire.id)}`, { method: "DELETE" });
  questionnaires = questionnaires.filter((item) => String(item.id) !== String(questionnaire.id));
  saveLocal();
  render();
};

function render() {
  const search = searchInput.value.trim().toLowerCase();
  const filtered = questionnaires.filter((item) => `${item.id} ${item.startDate} ${item.endDate}`.toLowerCase().includes(search));

  tableBody.innerHTML = filtered.length ? filtered.map((item) => `
    <tr data-questionnaire-id="${Admin.escape(item.id)}">
      <td><div class="user-cell">${Admin.escape(item.id)}</div></td>
      <td>${Admin.escape(formatDate(item.startDate))}</td>
      <td>${Admin.escape(formatDate(item.endDate))}</td>
      <td><div class="actions">
        <button class="icon-button edit-questionnaire" type="button" aria-label="Editar questionario ${Admin.escape(item.id)}">${Admin.icon("edit")}</button>
        <button class="icon-button delete-questionnaire" type="button" aria-label="Excluir questionario ${Admin.escape(item.id)}">${Admin.icon("delete")}</button>
      </div></td>
    </tr>
  `).join("") : '<tr><td colspan="4">Nenhum questionario encontrado.</td></tr>';

  Admin.footer(footerText, questionnaires.length, filtered.length, "questionarios");
}

async function load() {
  tableBody.innerHTML = '<tr><td colspan="4">Carregando questionarios...</td></tr>';
  const api = await Admin.firstAvailable(endpoints, ["questionarios", "listas", "quest", "questionnaires"], normalize);
  apiEndpoint = api.endpoint;
  questionnaires = api.items.length ? api.items : Admin.read(STORAGE_KEY).map((item, index) => normalize(item, index, "local"));
  render();
}

searchInput.addEventListener("input", render);
tableBody.addEventListener("click", async ({ target }) => {
  const button = target.closest(".edit-questionnaire, .delete-questionnaire");
  const row = target.closest("tr[data-questionnaire-id]");
  const questionnaire = row && questionnaires.find((item) => String(item.id) === row.dataset.questionnaireId);
  if (!button || !questionnaire) return;
  if (button.matches(".edit-questionnaire")) return edit(questionnaire);

  button.disabled = true;
  try {
    await remove(questionnaire);
  } catch (error) {
    alert("Nao foi possivel excluir o questionario. Verifique se a API esta funcionando.");
    button.disabled = false;
    console.error(error);
  }
});

load();
