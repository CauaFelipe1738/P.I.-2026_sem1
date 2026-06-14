const endpoints = ["/api/rankings", "/api/ranking", "/api/rank"].map((path) => Admin.apiBase + path);
const STORAGE_KEY = "rankingsAdmin";
const tableBody = Admin.$("#rankings-table-body");
const searchInput = Admin.$('.search-field input[type="search"]');
const footerText = Admin.$(".table-footer span");

let rankings = [];
let apiEndpoint = "";

const normalize = (ranking, index, source = "api") => ({
  id: ranking.id_ranking || ranking.idRanking || ranking.id || `local-${index}`,
  qtdPessoas: Number(ranking.qtd_pessoas ?? ranking.qtdPessoas ?? ranking.quantidade ?? 0),
  titulo: ranking.titulo || ranking.title || "Sem titulo",
  sobre: ranking.sobre || ranking.descricao || ranking.description || "",
  source
});
const payload = ({ id, qtdPessoas, titulo, sobre }) => ({ id_ranking: id, qtd_pessoas: qtdPessoas, titulo, sobre });
const saveLocal = () => Admin.write(STORAGE_KEY, rankings.filter((item) => item.source === "local").map(payload));
const edit = (ranking) => {
  sessionStorage.setItem("rankingEditando", JSON.stringify(ranking));
  location.href = `./uptade_ranking.html?id=${encodeURIComponent(ranking.id)}&source=${encodeURIComponent(ranking.source)}`;
};
const remove = async (ranking) => {
  if (!confirm(`Deseja excluir o ranking "${ranking.titulo}"?`)) return;
  if (ranking.source === "api" && apiEndpoint) await Admin.request(`${apiEndpoint}/${encodeURIComponent(ranking.id)}`, { method: "DELETE" });
  rankings = rankings.filter((item) => String(item.id) !== String(ranking.id));
  saveLocal();
  render();
};

function render() {
  const search = searchInput.value.trim().toLowerCase();
  const filtered = rankings.filter((item) => `${item.id} ${item.qtdPessoas} ${item.titulo} ${item.sobre}`.toLowerCase().includes(search));

  tableBody.innerHTML = filtered.length ? filtered.map((ranking) => `
    <tr data-ranking-id="${Admin.escape(ranking.id)}">
      <td><div class="user-cell">${Admin.escape(ranking.id)}</div></td>
      <td>${Admin.escape(ranking.qtdPessoas)}</td>
      <td><span class="badge admin">${Admin.escape(ranking.titulo)}</span></td>
      <td>${Admin.escape(ranking.sobre || "Sem descricao")}</td>
      <td><div class="actions">
        <button class="icon-button edit-ranking" type="button" aria-label="Editar ranking ${Admin.escape(ranking.titulo)}">${Admin.icon("edit")}</button>
        <button class="icon-button delete-ranking" type="button" aria-label="Excluir ranking ${Admin.escape(ranking.titulo)}">${Admin.icon("delete")}</button>
      </div></td>
    </tr>
  `).join("") : '<tr><td colspan="5">Nenhum ranking encontrado.</td></tr>';

  Admin.footer(footerText, rankings.length, filtered.length, "rankings");
}

async function load() {
  tableBody.innerHTML = '<tr><td colspan="5">Carregando rankings...</td></tr>';
  const api = await Admin.firstAvailable(endpoints, ["rankings", "ranking", "rank"], normalize);
  apiEndpoint = api.endpoint;
  rankings = api.items.length ? api.items : Admin.read(STORAGE_KEY).map((item, index) => normalize(item, index, "local"));
  render();
}

searchInput.addEventListener("input", render);
tableBody.addEventListener("click", async ({ target }) => {
  const button = target.closest(".edit-ranking, .delete-ranking");
  const row = target.closest("tr[data-ranking-id]");
  const ranking = row && rankings.find((item) => String(item.id) === row.dataset.rankingId);
  if (!button || !ranking) return;
  if (button.matches(".edit-ranking")) return edit(ranking);

  button.disabled = true;
  try {
    await remove(ranking);
  } catch (error) {
    alert("Nao foi possivel concluir a acao. Verifique se a API esta funcionando.");
    button.disabled = false;
    console.error(error);
  }
});

load();
