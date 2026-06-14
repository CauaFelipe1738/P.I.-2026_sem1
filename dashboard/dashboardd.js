const pages = {
  seguranca: "./questionario.html",
  compliance: "./compliance.html"
};

function getProgress(trainingId) {
  try {
    return JSON.parse(localStorage.getItem(trainingId));
  } catch {
    return null;
  }
}

function atualizarCardTreinamento(trainingId, suffix) {
  const dados = getProgress(trainingId);
  const status = document.getElementById(`status-${suffix}`);
  const barra = document.getElementById(`barra-${suffix}`);
  const botao = document.getElementById(`botao-${suffix}`);
  const link = botao?.closest("a");

  if (!status || !barra || !botao) return;

  const page = pages[suffix];
  const respondidas = dados?.respondidas || 0;
  const total = dados?.total || 1;
  const concluido = Boolean(dados?.concluido);

  barra.style.width = dados ? `${(respondidas / total) * 100}%` : "0%";
  status.textContent = !dados ? "NAO INICIADO" : concluido ? "CONCLUIDO" : "EM ANDAMENTO";
  status.className = `status ${!dados ? "status-nao-iniciado" : concluido ? "status-concluido" : "status-andamento"}`;
  botao.textContent = !dados ? "INICIAR AGORA" : concluido ? "REVISAR" : "CONTINUAR";
  if (link) link.href = concluido ? `${page}?review=1` : page;
}

atualizarCardTreinamento("seguranca_info", "seguranca");
atualizarCardTreinamento("compliance", "compliance");
