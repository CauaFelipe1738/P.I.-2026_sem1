const answers = Array.from(document.querySelectorAll(".answer"));
const toast = document.querySelector("#toast");
const timer = document.querySelector("#timer");
const confirmButton = document.querySelector(".confirm-button");
const skipButton = document.querySelector(".skip-button");
const questionTitle = document.querySelector("#question-title");
const progressStatus = document.querySelector(".progress-meta span:last-child");
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
      "Protocolo SSL/TLS 1.2 Legado",
      "Algoritmo de Signal (Double Ratchet)",
      "Arquitetura de Túnel SSH Nível 3",
      "Cifragem de Fluxo RC4 Modificada"
    ],
    correctOption: "B"
  },
  {
    title: "Qual camada do modelo OSI é responsável por garantir entrega confiável de dados entre hosts?",
    options: [
      "Camada de Aplicação",
      "Camada de Sessão",
      "Camada de Transporte",
      "Camada de Enlace"
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

function showToast(message) {
  toast.textContent = message;
  toast.classList.add("is-visible");
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => {
    toast.classList.remove("is-visible");
  }, 2600);
}

function setSelected(answer) {
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

function loadQuestion(index) {
  const question = questions[index];
  questionTitle.textContent = question.title;
  answers.forEach((answer, idx) => {
    answer.querySelector(".answer-text").textContent = question.options[idx];
    answer.dataset.option = String.fromCharCode(65 + idx);
    answer.classList.remove("is-selected");
    answer.setAttribute("aria-checked", "false");
  });
  progressStatus.textContent = `${index + 1} / ${questions.length} QUESTÕES`;
  progressFill.style.width = `${((index + 1) / questions.length) * 100}%`;
}

function updateStatsUi() {
  skippedCountEl.textContent = skippedCount;
  correctCountEl.textContent = correctCount;
  incorrectCountEl.textContent = incorrectCount;
}

function showCompletionStats() {
  completionBanner.hidden = false;
  completionSubtitle.textContent = "Você terminou o quiz — confira seu desempenho final.";
  confirmButton.disabled = true;
  skipButton.disabled = true;
}

function advanceQuestion() {
  currentQuestionIndex += 1;
  if (currentQuestionIndex >= questions.length) {
    showCompletionStats();
    return;
  }
  loadQuestion(currentQuestionIndex);
}

timer.textContent = formatTime(secondsLeft);

answers.forEach((answer) => {
  answer.addEventListener("click", () => setSelected(answer));
});

skipButton.addEventListener("click", () => {
  skippedCount += 1;
  updateStatsUi();
  showToast("Questão pulada. O laboratório avançaria para o próximo desafio.");
  setTimeout(advanceQuestion, 900);
});

confirmButton.addEventListener("click", () => {
  const selected = document.querySelector(".answer.is-selected");
  if (!selected) {
    showToast("Por favor, selecione uma alternativa antes de confirmar.");
    return;
  }

  const correctOption = questions[currentQuestionIndex].correctOption;
  if (selected.dataset.option === correctOption) {
    correctCount += 1;
  } else {
    incorrectCount += 1;
  }

  updateStatsUi();
  showToast(`Resposta ${selected.dataset.option} confirmada! Avançando para a próxima questão...`);
  setTimeout(advanceQuestion, 900);
});

updateStatsUi();
loadQuestion(currentQuestionIndex);

setInterval(() => {
  secondsLeft = Math.max(0, secondsLeft - 1);
  timer.textContent = formatTime(secondsLeft);
}, 1000);










