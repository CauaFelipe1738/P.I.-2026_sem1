function createQuiz({ trainingId, questions }) {
  const $ = (selector) => document.querySelector(selector);
  const answers = [...document.querySelectorAll(".answer")];
  const state = { index: 0, skipped: 0, correct: 0, incorrect: 0, advancing: false, reviewing: false };
  const reviewMode = new URLSearchParams(location.search).get("review") === "1";
  const ui = {
    toast: $("#toast"),
    confirm: $(".confirm-button"),
    title: $("#question-title"),
    progressText: $(".progress-meta span"),
    progressFill: $(".progress-fill"),
    banner: $("#completion-banner"),
    completionTitle: $("#completion-title"),
    completionSubtitle: $("#completion-subtitle"),
    skipped: $("#skipped-count"),
    correct: $("#correct-count"),
    incorrect: $("#incorrect-count"),
    answers: $(".answers"),
    actions: $(".action-row"),
    result: $(".result-card")
  };

  let toastTimer;
  const answered = () => state.skipped + state.correct + state.incorrect;
  const save = (done = answered() === questions.length) => localStorage.setItem(trainingId, JSON.stringify({
    respondidas: done ? questions.length : answered(),
    total: questions.length,
    concluido: done
  }));
  const toast = (message) => {
    ui.toast.textContent = message;
    ui.toast.classList.add("is-visible");
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => ui.toast.classList.remove("is-visible"), 2200);
  };
  const updateStats = () => {
    ui.skipped.textContent = state.skipped;
    ui.correct.textContent = state.correct;
    ui.incorrect.textContent = state.incorrect;
  };
  const setProgress = () => {
    ui.progressText.textContent = `${state.index + 1} / ${questions.length} QUESTÕES`;
    ui.progressFill.style.width = `${((state.index + 1) / questions.length) * 100}%`;
  };
  const clearAnswers = () => answers.forEach((answer) => {
    answer.classList.remove("is-selected", "is-correct", "is-incorrect", "is-review-correct");
    answer.setAttribute("aria-checked", "false");
    answer.removeAttribute("aria-disabled");
  });
  const setSelected = (selected) => {
    if (state.advancing || state.reviewing) return;
    answers.forEach((answer) => {
      const active = answer === selected;
      answer.classList.toggle("is-selected", active);
      answer.setAttribute("aria-checked", String(active));
    });
  };
  const loadQuestion = (review = false) => {
    const question = questions[state.index];
    state.reviewing = review;
    state.advancing = false;
    ui.confirm.disabled = false;
    ui.title.textContent = question.title;
    clearAnswers();
    setProgress();

    answers.forEach((answer, index) => {
      answer.dataset.option = String.fromCharCode(65 + index);
      answer.querySelector(".answer-text").textContent = question.options[index];
    });

    if (!review) {
      ui.confirm.textContent = "CONFIRMAR RESPOSTA";
      return;
    }

    answers.forEach((answer) => {
      const correct = answer.dataset.option === question.correctOption;
      answer.setAttribute("aria-disabled", "true");
      answer.setAttribute("aria-checked", String(correct));
      answer.classList.toggle("is-correct", correct);
      answer.classList.toggle("is-review-correct", correct);
    });
    ui.confirm.textContent = state.index === questions.length - 1 ? "VOLTAR AO DASHBOARD" : "PROXIMA QUESTAO";
  };
  const showCompletion = () => {
    save(true);
    state.advancing = true;
    ui.banner.hidden = false;
    ui.completionTitle.textContent = "Quiz finalizado";
    ui.completionSubtitle.textContent = `Você acertou ${state.correct}, errou ${state.incorrect} e pulou ${state.skipped}.`;
    ui.title.textContent = "Resultado final";
    ui.answers.hidden = true;
    ui.actions.hidden = true;
    ui.result.classList.add("is-complete");
    ui.progressText.textContent = `${questions.length} / ${questions.length} QUESTÕES`;
    ui.progressFill.style.width = "100%";
    toast("Quiz finalizado! Veja seu desempenho no painel.");
  };
  const advance = () => {
    state.index += 1;
    state.index >= questions.length ? showCompletion() : loadQuestion();
  };
  const loadReview = () => {
    ui.answers.hidden = false;
    ui.actions.hidden = false;
    ui.banner.hidden = false;
    ui.completionTitle.textContent = "Revisao das respostas";
    ui.completionSubtitle.textContent = "A alternativa correta aparece marcada ao lado da opcao.";
    ui.result.classList.add("is-complete");
    loadQuestion(true);
  };

  answers.forEach((answer) => answer.addEventListener("click", () => setSelected(answer)));
  ui.confirm.addEventListener("click", () => {
    if (state.reviewing) {
      if (state.index >= questions.length - 1) return void (location.href = "./dashboard.html");
      state.index += 1;
      loadReview();
      return;
    }
    if (state.advancing) return;

    const selected = $(".answer.is-selected");
    if (!selected) return toast("Selecione uma alternativa antes de confirmar.");

    const { correctOption } = questions[state.index];
    const isCorrect = selected.dataset.option === correctOption;
    state[isCorrect ? "correct" : "incorrect"] += 1;
    save();
    selected.classList.add(isCorrect ? "is-correct" : "is-incorrect");

    if (isCorrect) {
      toast("Resposta correta! Avançando para a próxima pergunta...");
    } else {
      answers.find((answer) => answer.dataset.option === correctOption)?.classList.add("is-correct");
      toast(`Resposta incorreta. A correta era ${correctOption}.`);
    }

    updateStats();
    state.advancing = true;
    ui.confirm.disabled = true;
    setTimeout(advance, 900);
  });

  updateStats();
  reviewMode ? loadReview() : loadQuestion();
}
