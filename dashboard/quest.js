const answers = Array.from(document.querySelectorAll(".answer"));
const toast = document.querySelector("#toast");
const timer = document.querySelector("#timer");
const confirmButton = document.querySelector(".confirm-button");
const skipButton = document.querySelector(".skip-button");
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
    title: "Qual protocolo de criptografia é considerado o padrão ouro para comunicações seguras ponta-a-ponta em redes descentralizadas?",
    options: [
      "Protocolo SSL/TLS 1.2 legado",
      "Algoritmo de Signal (Double Ratchet)",
      "Arquitetura de túnel SSH nível 3",
      "Cifragem de fluxo RC4 modificada"
    ],
    correctOption: "B"
  },
  {
    title: "Qual camada do modelo OSI é responsável por garantir entrega confiável de dados entre hosts?",
    options: [
      "Camada de aplicação",
      "Camada de sessão",
      "Camada de transporte",
      "Camada de enlace"
    ],
    correctOption: "C"
  },
  {
    title: "Qual técnica de autenticação multifator utiliza algo que o usuário sabe e algo que ele possui?",
    options: [
      "Senha e PIN",
      "Cartão de crédito e biometria",
      "Token de hardware e senha",
      "Reconhecimento facial e endereço de e-mail"
    ],
    correctOption: "C"
  }
];

let currentQuestionIndex = 0;
let skippedCount = 0;
let correctCount = 0;
let incorrectCount = 0;
let secondsLeft = 1800;
let toastTimer;
let isAdvancing = false;

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

function formatTime(totalSeconds) {
  const minutes = Math.floor(totalSeconds / 60);
  const seconds = totalSeconds % 60;
  return `${String(minutes).padStart(2, "0")}:${String(seconds).padStart(2, "0")}`;
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
  progressStatus.textContent = `${index + 1} / ${questions.length} QUESTÕES`;
  progressFill.style.width = `${((index + 1) / questions.length) * 100}%`;
  confirmButton.disabled = false;
  skipButton.disabled = false;
  isAdvancing = false;
}

function updateStatsUi() {
  skippedCountEl.textContent = skippedCount;
  correctCountEl.textContent = correctCount;
  incorrectCountEl.textContent = incorrectCount;
}

function showCompletionStats() {
  isAdvancing = true;
  completionBanner.hidden = false;
  completionTitle.textContent = "Quiz finalizado";
  completionSubtitle.textContent = `Você acertou ${correctCount}, errou ${incorrectCount} e pulou ${skippedCount}.`;
  questionTitle.textContent = "Resultado final";
  document.querySelector(".answers").hidden = true;
  document.querySelector(".action-row").hidden = true;
  progressStatus.textContent = `${questions.length} / ${questions.length} QUESTÕES`;
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
  skipButton.disabled = true;
  setTimeout(advanceQuestion, 900);
}

timer.textContent = formatTime(secondsLeft);

answers.forEach((answer) => {
  answer.addEventListener("click", () => setSelected(answer));
});

skipButton.addEventListener("click", () => {
  if (isAdvancing) return;

  skippedCount += 1;
  updateStatsUi();
  showToast("Questão pulada. Avançando para a próxima pergunta...");
  scheduleAdvance();
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
    selected.classList.add("is-correct");
    showToast("Resposta correta! Avançando para a próxima pergunta...");
  } else {
    incorrectCount += 1;
    selected.classList.add("is-incorrect");
    correctAnswer.classList.add("is-correct");
    showToast(`Resposta incorreta. A correta era ${correctOption}.`);
  }

  updateStatsUi();
  scheduleAdvance();
});

updateStatsUi();
loadQuestion(currentQuestionIndex);

setInterval(() => {
  secondsLeft = Math.max(0, secondsLeft - 1);
  timer.textContent = formatTime(secondsLeft);
}, 1000);
