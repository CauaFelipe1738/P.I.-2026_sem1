const answers = Array.from(document.querySelectorAll(".answer"));
const toast = document.querySelector("#toast");
const confirmButton = document.querySelector(".confirm-button");
const questionTitle = document.querySelector("#question-title");
const progressStatus = document.querySelector(".progress-meta span");
const progressFill = document.querySelector(".progress-fill");
const completionBanner = document.querySelector("#completion-banner");
const completionTitle = document.querySelector("#completion-title");
const completionSubtitle = document.querySelector("#completion-subtitle");
const skippedCountEl = document.querySelector("#skipped-count");
const correctCountEl = document.querySelector("#correct-count");
const incorrectCountEl = document.querySelector("#incorrect-count");

const questions = [
  {
    title: "O que significa agir em compliance dentro de uma empresa?",
    options: [
      "Seguir apenas ordens verbais do gestor",
      "Cumprir leis, normas internas e padroes eticos",
      "Priorizar resultados mesmo ignorando regras",
      "Evitar registrar decisoes importantes"
    ],
    correctOption: "B"
  },
  {
    title: "Qual atitude deve ser tomada ao identificar uma possivel irregularidade?",
    options: [
      "Ignorar caso nao envolva diretamente sua area",
      "Comentar informalmente com colegas",
      "Usar os canais internos apropriados para relatar",
      "Apagar evidencias para evitar conflitos"
    ],
    correctOption: "C"
  },
  {
    title: "Por que conflitos de interesse devem ser comunicados?",
    options: [
      "Para garantir transparencia e proteger a tomada de decisao",
      "Para permitir vantagens pessoais em negociacoes",
      "Para reduzir a necessidade de controles internos",
      "Para evitar que regras sejam documentadas"
    ],
    correctOption: "A"
  }
];

let currentQuestionIndex = 0;
let skippedCount = 0;
let correctCount = 0;
let incorrectCount = 0;

const TREINAMENTO_ID = "compliance";

let toastTimer;
let isAdvancing = false;

function salvarProgresso() {
  const respondidas = correctCount + incorrectCount + skippedCount;

  localStorage.setItem(
    TREINAMENTO_ID,
    JSON.stringify({
      respondidas,
      total: questions.length,
      concluido: respondidas === questions.length
    })
  );
}

function showToast(message) {
  toast.textContent = message;
  toast.classList.add("is-visible");
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => {
    toast.classList.remove("is-visible");
  }, 2200);
}

function setSelected(answer) {
  if (isAdvancing) return;

  answers.forEach((item) => {
    const isCurrent = item === answer;
    item.classList.toggle("is-selected", isCurrent);
    item.setAttribute("aria-checked", String(isCurrent));
  });
}

function clearSelectedAnswer() {
  answers.forEach((answer) => {
    answer.classList.remove("is-selected", "is-correct", "is-incorrect");
    answer.setAttribute("aria-checked", "false");
  });
}

function loadQuestion(index) {
  const question = questions[index];
  questionTitle.textContent = question.title;

  answers.forEach((answer, idx) => {
    answer.querySelector(".answer-text").textContent = question.options[idx];
    answer.dataset.option = String.fromCharCode(65 + idx);
  });

  clearSelectedAnswer();
  progressStatus.textContent = `${index + 1} / ${questions.length} QUESTOES`;
  progressFill.style.width = `${((index + 1) / questions.length) * 100}%`;
  confirmButton.disabled = false;
  isAdvancing = false;
}

function updateStatsUi() {
  skippedCountEl.textContent = skippedCount;
  correctCountEl.textContent = correctCount;
  incorrectCountEl.textContent = incorrectCount;
}

function showCompletionStats() {
  localStorage.setItem(
    TREINAMENTO_ID,
    JSON.stringify({
      respondidas: questions.length,
      total: questions.length,
      concluido: true
    })
  );

  isAdvancing = true;
  completionBanner.hidden = false;
  completionTitle.textContent = "Quiz finalizado";
  completionSubtitle.textContent = `Voce acertou ${correctCount}, errou ${incorrectCount} e pulou ${skippedCount}.`;
  questionTitle.textContent = "Resultado final";
  document.querySelector(".answers").hidden = true;
  document.querySelector(".action-row").hidden = true;
  progressStatus.textContent = `${questions.length} / ${questions.length} QUESTOES`;
  progressFill.style.width = "100%";
  showToast("Quiz finalizado! Veja seu desempenho no painel.");
}

function advanceQuestion() {
  currentQuestionIndex += 1;

  if (currentQuestionIndex >= questions.length) {
    showCompletionStats();
    return;
  }

  loadQuestion(currentQuestionIndex);
}

function scheduleAdvance() {
  isAdvancing = true;
  confirmButton.disabled = true;

  setTimeout(advanceQuestion, 900);
}

answers.forEach((answer) => {
  answer.addEventListener("click", () => setSelected(answer));
});

confirmButton.addEventListener("click", () => {
  if (isAdvancing) return;

  const selected = document.querySelector(".answer.is-selected");
  if (!selected) {
    showToast("Selecione uma alternativa antes de confirmar.");
    return;
  }

  const correctOption = questions[currentQuestionIndex].correctOption;
  const correctAnswer = answers.find((answer) => answer.dataset.option === correctOption);

  if (selected.dataset.option === correctOption) {
    correctCount += 1;
    salvarProgresso();
    selected.classList.add("is-correct");
    showToast("Resposta correta! Avancando para a proxima pergunta...");
  } else {
    incorrectCount += 1;
    salvarProgresso();
    selected.classList.add("is-incorrect");
    correctAnswer.classList.add("is-correct");
    showToast(`Resposta incorreta. A correta era ${correctOption}.`);
  }

  updateStatsUi();
  scheduleAdvance();
});

updateStatsUi();
loadQuestion(currentQuestionIndex);
