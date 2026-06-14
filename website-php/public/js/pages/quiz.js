document.addEventListener("DOMContentLoaded", () => {
  const $ = (selector) => document.querySelector(selector);
  const state = { index: 0, skipped: 0, correct: 0, incorrect: 0, advancing: false };
  const ui = {
    toast: $("#toast"),
    confirm: $(".confirm-button"),
    skip: $(".skip-button"),
    title: $("#question-title"),
    progressText: $("#progress-text"),
    progressFill: $("#progress-fill"),
    answers: $("#answers-container"),
    result: $("#result-card"),
    quiz: $("#quiz-container"),
    xp: $("#user-xp")
  };
  let toastTimer;

  const toast = (message) => {
    ui.toast.textContent = message;
    ui.toast.classList.add("is-visible");
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => ui.toast.classList.remove("is-visible"), 2200);
  };
  const progress = () => {
    ui.progressText.textContent = `${state.index} / ${quizData.length} RESPONDIDAS`;
    ui.progressFill.style.width = `${(state.index / quizData.length) * 100}%`;
  };
  const finish = () => {
    ui.quiz.style.display = "none";
    ui.result.style.display = "block";
    progress();
    $("#skipped-count").textContent = state.skipped;
    $("#correct-count").textContent = state.correct;
    $("#incorrect-count").textContent = state.incorrect;
    toast("Quiz finalizado! Desempenho computado.");
  };
  const load = () => {
    if (state.index >= quizData.length) return finish();

    const letters = ["A", "B", "C", "D", "E"];
    const question = quizData[state.index];
    ui.title.textContent = question.pergunta;
    ui.answers.innerHTML = question.respostas.map((answer, index) => `
      <button class="answer" type="button" data-id="${answer.id_resposta}" data-solucao="${answer.solucao ? "1" : "0"}">
        <span class="answer-letter">${letters[index] || "-"}</span>
        <span class="answer-text">${answer.resposta}</span>
        <span class="choice-ring"></span>
      </button>
    `).join("");
    state.advancing = false;
    ui.confirm.disabled = false;
    ui.confirm.textContent = "CONFIRMAR RESPOSTA";
  };
  const saveAnswer = async (idResposta) => {
    const question = quizData[state.index];
    try {
      const response = await fetch(`${urlBase}/quiz/${listaId}/pergunta/${question.id_pergunta}/responder`, {
        method: "POST",
        headers: { "Content-Type": "application/json", Accept: "application/json", "X-CSRF-TOKEN": csrfToken },
        body: JSON.stringify({ id_resposta: idResposta })
      });
      const data = await response.json();
      if (response.ok) ui.xp.textContent = `${data.pontos_totais.toLocaleString("pt-BR")} pontos`;
    } catch (error) {
      console.error("Erro ao salvar no banco:", error);
    }
  };
  const next = () => {
    state.index += 1;
    progress();
    load();
  };

  ui.answers.addEventListener("click", ({ target }) => {
    const answer = target.closest(".answer");
    if (!answer || state.advancing) return;
    document.querySelectorAll(".answer").forEach((item) => item.classList.remove("is-selected"));
    answer.classList.add("is-selected");
  });
  ui.skip.addEventListener("click", () => {
    if (state.advancing) return;
    state.skipped += 1;
    next();
  });
  ui.confirm.addEventListener("click", async () => {
    if (state.advancing) return;

    const selected = $(".answer.is-selected");
    if (!selected) return toast("Selecione uma alternativa antes de confirmar.");

    state.advancing = true;
    ui.confirm.disabled = true;
    ui.confirm.textContent = "SALVANDO...";

    const correct = selected.dataset.solucao === "1";
    state[correct ? "correct" : "incorrect"] += 1;
    selected.classList.add(correct ? "is-correct" : "is-incorrect");
    if (!correct) $('.answer[data-solucao="1"]')?.classList.add("is-correct");
    toast(correct ? "Resposta correta! Avançando..." : "Resposta incorreta.");

    await saveAnswer(selected.dataset.id);
    setTimeout(next, 900);
  });

  progress();
  load();
});
