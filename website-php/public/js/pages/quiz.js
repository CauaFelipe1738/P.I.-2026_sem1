document.addEventListener('DOMContentLoaded', () => {
    // === ELEMENTOS DA TELA ===
    const toast = document.querySelector("#toast");
    const confirmButton = document.querySelector(".confirm-button");
    const skipButton = document.querySelector(".skip-button");
    const questionTitle = document.querySelector("#question-title");
    const progressStatus = document.querySelector("#progress-text");
    const progressFill = document.querySelector("#progress-fill");
    const answersContainer = document.querySelector("#answers-container");
    const resultCard = document.querySelector("#result-card");
    const quizContainer = document.querySelector("#quiz-container");
    const userXp = document.querySelector("#user-xp");

    // Contadores e controle de estado
    let currentQuestionIndex = 0;
    let skippedCount = 0;
    let correctCount = 0;
    let incorrectCount = 0;
    let toastTimer;
    let isAdvancing = false;

    // Inicializa a barra de progresso com 0 respondidas e carrega a primeira pergunta
    updateProgressUi(0);
    loadQuestion(currentQuestionIndex);

    // === FUNÇÕES DE INTERFACE ===
    function showToast(message) {
        toast.textContent = message;
        toast.classList.add("is-visible");
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => {
            toast.classList.remove("is-visible");
        }, 2200);
    }

    // Alinha o texto e a barra com base no número de questões já processadas
    function updateProgressUi(index) {
        progressStatus.textContent = `${index} / ${quizData.length} RESPONDIDAS`;
        progressFill.style.width = `${(index / quizData.length) * 100}%`;
    }

    function loadQuestion(index) {
        if (index >= quizData.length) {
            showCompletionStats();
            return;
        }

        const question = quizData[index];
        questionTitle.textContent = question.pergunta;
        answersContainer.innerHTML = '';
        isAdvancing = false;
        confirmButton.disabled = false;
        confirmButton.textContent = "CONFIRMAR RESPOSTA";

        const letras = ['A', 'B', 'C', 'D', 'E'];

        question.respostas.forEach((resposta, idx) => {
            const btn = document.createElement('button');
            btn.className = 'answer';
            btn.type = 'button';
            btn.dataset.id = resposta.id_resposta;

            // CORREÇÃO DO BUG CRÍTICO: Força o valor a ser a string "1" ou "0"
            // Isso evita o problema do interpretador ler como "true"/"false" texto
            btn.dataset.solucao = resposta.solucao ? "1" : "0";

            btn.innerHTML = `
                <span class="answer-letter">${letras[idx] || '-'}</span>
                <span class="answer-text">${resposta.resposta}</span>
                <span class="choice-ring"></span>
            `;

            btn.addEventListener('click', () => {
                if (isAdvancing) return;
                document.querySelectorAll('.answer').forEach(b => b.classList.remove('is-selected'));
                btn.classList.add('is-selected');
            });

            answersContainer.appendChild(btn);
        });
    }

    function showCompletionStats() {
        quizContainer.style.display = 'none';
        resultCard.style.display = 'block';
        updateProgressUi(quizData.length); // Barra em 100%

        // Alimenta cada contador individualmente com as variáveis do estado do JS
        document.querySelector("#skipped-count").textContent = skippedCount;
        document.querySelector("#correct-count").textContent = correctCount;
        document.querySelector("#incorrect-count").textContent = incorrectCount;

        showToast("Quiz finalizado! Desempenho computado.");
    }

    // === COMUNICAÇÃO COM O LARAVEL ===
    async function salvarRespostaNoBanco(idResposta) {
        const question = quizData[currentQuestionIndex];
        const endpoint = `${urlBase}/quiz/${listaId}/pergunta/${question.id_pergunta}/responder`;

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ id_resposta: idResposta })
            });

            const data = await response.json();
            if (response.ok) {
                // Trocado de 'XP' para 'pontos' em tempo real conforme solicitado
                userXp.textContent = `${data.pontos_totais.toLocaleString('pt-BR')} pontos`;
            }
        } catch (error) {
            console.error("Erro ao salvar no banco:", error);
        }
    }

    // === BOTÕES DE AÇÃO ===
    skipButton.addEventListener("click", () => {
        if (isAdvancing) return;
        skippedCount++;
        currentQuestionIndex++;
        updateProgressUi(currentQuestionIndex); // Avança a barra imediatamente ao pular
        loadQuestion(currentQuestionIndex);
    });

    confirmButton.addEventListener("click", async () => {
        if (isAdvancing) return;

        const selected = document.querySelector(".answer.is-selected");
        if (!selected) {
            showToast("Selecione uma alternativa antes de confirmar.");
            return;
        }

        isAdvancing = true;
        confirmButton.disabled = true;
        confirmButton.textContent = "SALVANDO...";

        // Agora comparamos string com string ("1" === "1"), funciona perfeitamente!
        const isCorrect = selected.dataset.solucao === "1";
        const answerId = selected.dataset.id;

        // Feedback visual imediato para o usuário
        if (isCorrect) {
            correctCount++;
            selected.classList.add("is-correct");
            showToast("Resposta correta! Avançando...");
        } else {
            incorrectCount++;
            selected.classList.add("is-incorrect");
            // Destaca a alternativa correta em verde para aprendizado do colaborador
            const correctElement = document.querySelector(`.answer[data-solucao="1"]`);
            if (correctElement) correctElement.classList.add("is-correct");
            showToast("Resposta incorreta.");
        }

        // Dispara a procedure do seu grupo no banco de dados em segundo plano
        await salvarRespostaNoBanco(answerId);

        // Aguarda a animação visual por 900ms antes de mover a barra e carregar a próxima
        setTimeout(() => {
            currentQuestionIndex++;
            updateProgressUi(currentQuestionIndex);
            loadQuestion(currentQuestionIndex);
        }, 900);
    });
});
