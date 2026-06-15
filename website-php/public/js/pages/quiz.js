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

    if (skipButton) skipButton.style.display = 'none';

    // Estado do Quiz
    let correctCount = 0;
    let incorrectCount = 0;
    let totalPontosGanhos = 0;
    const totalPontosPossiveis = quizData.reduce((acc, q) => acc + q.valor, 0);
    let toastTimer;
    let isAdvancing = false;

    // Calcula de onde começar baseado nas respostas que já vieram do banco
    let answeredCount = Object.keys(respostasUsuario).length;
    let currentQuestionIndex = answeredCount < quizData.length ? answeredCount : 0;

    // Pré-calcula os acertos para a tela final caso a pessoa já tenha respondido tudo
    quizData.forEach(q => {
        const userRespId = respostasUsuario[q.id_pergunta];
        if (userRespId) {
            const respObj = q.respostas.find(r => r.id_resposta == userRespId);
            if (respObj && respObj.solucao) {
                correctCount++;
                totalPontosGanhos += q.valor;
            } else {
                incorrectCount++;
            }
        }
    });

    userXp.textContent = `${totalPontosGanhos} PONTOS`;

    updateProgressUi(answeredCount);
    loadQuestion(currentQuestionIndex);

    // === FUNÇÕES DE INTERFACE ===
    function showToast(message) {
        toast.textContent = message;
        toast.classList.add("is-visible");
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toast.classList.remove("is-visible"), 2200);
    }

    function updateProgressUi(count) {
        progressStatus.textContent = `${count} / ${quizData.length} QUESTÕES`;
        progressFill.style.width = `${(count / quizData.length) * 100}%`;
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

        const letras = ['A', 'B', 'C', 'D', 'E'];

        // Verifica se essa pergunta já foi respondida no passado
        const alreadyAnsweredId = respostasUsuario[question.id_pergunta];

        question.respostas.forEach((resposta, idx) => {
            const btn = document.createElement('button');
            btn.className = 'answer';
            btn.type = 'button';
            btn.dataset.id = resposta.id_resposta;
            btn.dataset.solucao = resposta.solucao ? "1" : "0";

            btn.innerHTML = `
                <span class="answer-letter">${letras[idx] || '-'}</span>
                <span class="answer-text">${resposta.resposta}</span>
                <span class="choice-ring"></span>
            `;

            // MODO REVISÃO: Bloqueia interações e pinta de verde/vermelho
            if (alreadyAnsweredId) {
                btn.style.cursor = 'default';
                if (resposta.solucao) {
                    btn.classList.add("is-correct"); // Pinta a certa de verde
                }
                if (resposta.id_resposta == alreadyAnsweredId && !resposta.solucao) {
                    btn.classList.add("is-incorrect"); // Pinta a errada que o usuário escolheu de vermelho
                }
            } else {
                // MODO JOGO NORMAL
                btn.addEventListener('click', () => {
                    if (isAdvancing) return;
                    document.querySelectorAll('.answer').forEach(b => b.classList.remove('is-selected'));
                    btn.classList.add('is-selected');
                });
            }

            answersContainer.appendChild(btn);
        });

        // Configura o botão principal
        confirmButton.disabled = false;
        if (alreadyAnsweredId) {
            // Se já respondeu, o botão apenas avança
            confirmButton.textContent = "PRÓXIMA QUESTÃO ▸";
            confirmButton.dataset.action = "next";
        } else {
            // Se não respondeu, o botão salva
            confirmButton.textContent = "CONFIRMAR RESPOSTA";
            confirmButton.dataset.action = "save";
        }
    }

    function showCompletionStats() {
        quizContainer.style.display = 'none';
        resultCard.style.display = 'block';
        updateProgressUi(quizData.length);

        // NOVO: Mostra no formato "Pontos obtidos / Total possível"
        document.querySelector("#skipped-count").textContent = `${totalPontosGanhos} / ${totalPontosPossiveis}`;
        document.querySelector("#skipped-count").previousElementSibling.textContent = "Pontuação Obtida";

        document.querySelector("#correct-count").textContent = correctCount;
        document.querySelector("#incorrect-count").textContent = incorrectCount;

        showToast("Você chegou ao fim do questionário!");
    }

    // === COMUNICAÇÃO COM O LARAVEL ===
    async function salvarRespostaNoBanco(idResposta) {
        const question = quizData[currentQuestionIndex];
        const endpoint = `${urlBase}/quiz/${listaId}/pergunta/${question.id_pergunta}/responder`;

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ id_resposta: idResposta })
            });
            const data = await response.json();
            if (response.ok) {
                userXp.textContent = `${data.pontos_totais.toLocaleString('pt-BR')} PONTOS`;
            }
        } catch (error) {
            console.error("Erro ao salvar:", error);
        }
    }

    // === BOTÃO PRINCIPAL (SALVAR ou AVANÇAR) ===
    confirmButton.addEventListener("click", async () => {
        if (isAdvancing) return;

        // Se for Modo Revisão, apenas avança a página
        if (confirmButton.dataset.action === "next") {
            currentQuestionIndex++;
            loadQuestion(currentQuestionIndex);
            return;
        }

        // Se for Salvar
        const selected = document.querySelector(".answer.is-selected");
        if (!selected) {
            showToast("Selecione uma alternativa antes de confirmar.");
            return;
        }

        isAdvancing = true;
        confirmButton.disabled = true;
        confirmButton.textContent = "SALVANDO...";

        const isCorrect = selected.dataset.solucao === "1";
        const answerId = selected.dataset.id;
        const question = quizData[currentQuestionIndex];

        // Adiciona a resposta no array local para virar "Revisão" na hora
        respostasUsuario[question.id_pergunta] = answerId;

        if (isCorrect) {
            correctCount++;
            totalPontosGanhos += question.valor;
            selected.classList.add("is-correct");
            showToast("Resposta correta! Avançando...");
            userXp.textContent = `${totalPontosGanhos} PONTOS`;
        } else {
            incorrectCount++;
            selected.classList.add("is-incorrect");
            document.querySelector(`.answer[data-solucao="1"]`)?.classList.add("is-correct");
            showToast("Resposta incorreta.");
        }

        await salvarRespostaNoBanco(answerId);

        // Avança barra de progresso e carrega a próxima
        updateProgressUi(Object.keys(respostasUsuario).length);

        setTimeout(() => {
            currentQuestionIndex++;
            loadQuestion(currentQuestionIndex);
        }, 1200); // 1.2s para a pessoa conseguir ler a resposta certa
    });
});
