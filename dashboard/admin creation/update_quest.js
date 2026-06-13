function validateQuestionnaire(data) {
    if (!data.inicio || !data.fim) {
        return "Informe as datas de inicio e fim.";
    }

    const hoje = new Date();
    hoje.setHours(0, 0, 0, 0);

    const inicio = new Date(data.inicio);
    const fim = new Date(data.fim);

    if (inicio < hoje) {
        return "A data de inicio nao pode ser anterior a hoje.";
    }

    if (fim < inicio) {
        return "A data de fim nao pode ser anterior a data de inicio.";
    }

    return "";
}