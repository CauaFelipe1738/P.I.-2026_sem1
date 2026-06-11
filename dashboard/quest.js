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
const answersContainer = document.querySelector(".answers");
const actionRow = document.querySelector(".action-row");
const resultCard = document.querySelector(".result-card");
const isReviewMode = new URLSearchParams(window.location.search).get("review") === "1";


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

const TREINAMENTO_ID = "seguranca_info";

let toastTimer;
let isAdvancing = false;
let isReviewing = false;

function salvarProgresso() {

  const respondidas =
    correctCount +
    incorrectCount +
    skippedCount;

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
  if (isAdvancing || isReviewing) return;

  answers.forEach((item) => {
    const isCurrent = item === answer;
    item.classList.toggle("is-selected", isCurrent);
    item.setAttribute("aria-checked", String(isCurrent));
  });
}

function clearSelectedAnswer() {
  answers.forEach((answer) => {
    answer.classList.remove("is-selected", "is-correct", "is-incorrect", "is-review-correct");
    answer.setAttribute("aria-checked", "false");
    answer.removeAttribute("aria-disabled");
  });
}

function loadQuestion(index, reviewMode = false) {
  const question = questions[index];
  questionTitle.textContent = question.title;

  answers.forEach((answer, idx) => {
    answer.querySelector(".answer-text").textContent = question.options[idx];
    answer.dataset.option = String.fromCharCode(65 + idx);
  });

  clearSelectedAnswer();
  progressStatus.textContent = `${index + 1} / ${questions.length} QUESTÕES`;
  progressFill.style.width = `${((index + 1) / questions.length) * 100}%`;
  isReviewing = reviewMode;
  confirmButton.disabled = false;
  isAdvancing = false;

  if (reviewMode) {
    const correctAnswer = answers.find((answer) => answer.dataset.option === question.correctOption);

    answers.forEach((answer) => {
      answer.setAttribute("aria-disabled", "true");
      answer.setAttribute("aria-checked", String(answer.dataset.option === question.correctOption));
    });

    correctAnswer?.classList.add("is-correct", "is-review-correct");
    confirmButton.textContent = index === questions.length - 1 ? "VOLTAR AO DASHBOARD" : "PROXIMA QUESTAO";
  } else {
    confirmButton.textContent = "CONFIRMAR RESPOSTA";
  }
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
  completionSubtitle.textContent = `Você acertou ${correctCount}, errou ${incorrectCount} e pulou ${skippedCount}.`;
  questionTitle.textContent = "Resultado final";
  answersContainer.hidden = true;
  actionRow.hidden = true;
  resultCard.classList.add("is-complete");
  progressStatus.textContent = `${questions.length} / ${questions.length} QUESTÕES`;
  progressFill.style.width = "100%";
  showToast("Quiz finalizado! Veja seu desempenho no painel.");
}

function loadReviewQuestion(index) {
  answersContainer.hidden = false;
  actionRow.hidden = false;
  completionBanner.hidden = false;
  completionTitle.textContent = "Revisao das respostas";
  completionSubtitle.textContent = "A alternativa correta aparece marcada ao lado da opcao.";
  resultCard.classList.add("is-complete");
  loadQuestion(index, true);
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
  if (isReviewing) {
    if (currentQuestionIndex >= questions.length - 1) {
      window.location.href = "./dashboard.html";
      return;
    }

    currentQuestionIndex += 1;
    loadReviewQuestion(currentQuestionIndex);
    return;
  }

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
    showToast("Resposta correta! Avançando para a próxima pergunta...");
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
if (isReviewMode) {
  loadReviewQuestion(currentQuestionIndex);
} else {
  loadQuestion(currentQuestionIndex);
}

