console.log("dashboard carregado");

function carregarDadosTreinamento(treinamentoId) {
  const dadosSalvos = localStorage.getItem(treinamentoId);

  if (!dadosSalvos) {
    return null;
  }

  return JSON.parse(dadosSalvos);
}

function atualizarCardTreinamento(treinamentoId, sufixoElemento) {
  const dados = carregarDadosTreinamento(treinamentoId);
  const status = document.getElementById(`status-${sufixoElemento}`);
  const barra = document.getElementById(`barra-${sufixoElemento}`);
  const botao = document.getElementById(`botao-${sufixoElemento}`);

  if (!status || !barra || !botao) {
    console.error("Nao encontrei os elementos do card.", sufixoElemento);
    return;
  }

  if (!dados) {
    status.textContent = "NAO INICIADO";
    status.className = "status status-nao-iniciado";
    barra.style.width = "0%";
    botao.textContent = "INICIAR AGORA";
    return;
  }

  const porcentagem = (dados.respondidas / dados.total) * 100;

  barra.style.width = porcentagem + "%";

  if (dados.concluido) {
    status.textContent = "CONCLUIDO";
    status.className = "status status-concluido";
    botao.textContent = "REVISAR";
  } else {
    status.textContent = "EM ANDAMENTO";
    status.className = "status status-andamento";
    botao.textContent = "CONTINUAR";
  }
}

atualizarCardTreinamento("seguranca_info", "seguranca");
atualizarCardTreinamento("compliance", "compliance");
