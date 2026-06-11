console.log("dashboard carregado");

const TREINAMENTO_ID = "seguranca_info";

const dados = JSON.parse(
  localStorage.getItem(TREINAMENTO_ID)
);

console.log("dados:", dados);
console.log("respondidas:", dados?.respondidas);
console.log("total:", dados?.total);
console.log("concluido:", dados?.concluido);

console.log("Dados encontrados:", dados);

const status = document.getElementById(
  "status-seguranca"
);

const barra = document.getElementById(
  "barra-seguranca"
);

const botao = document.getElementById(
  "botao-seguranca"
);

if (!status || !barra || !botao) {
  console.error(
    "Não encontrei os elementos do card."
  );
} else {

  if (!dados) {

    status.textContent = "NÃO INICIADO";

    status.className =
      "status status-nao-iniciado";

    barra.style.width = "0%";

    botao.textContent =
      "INICIAR AGORA";

  } else {

    const porcentagem =
      (dados.respondidas / dados.total) * 100;

    barra.style.width =
      porcentagem + "%";

    if (dados.concluido) {

      status.textContent =
        "CONCLUÍDO";

      status.className =
        "status status-concluido";

      botao.textContent =
        "REVISAR";

    } else {

      status.textContent =
        "EM ANDAMENTO";

      status.className =
        "status status-andamento";

      botao.textContent =
        "CONTINUAR";

    }

  }

}

console.log(status);
console.log(barra);
console.log(botao);
