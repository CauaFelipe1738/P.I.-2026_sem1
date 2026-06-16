document.addEventListener('DOMContentLoaded', () => {
    // 1. BLINDAGEM DE VARIÁVEIS (Evita que o JS quebre se o PHP mandar algo nulo)
    const data = typeof quizData !== 'undefined' ? quizData : [];
    const hist = typeof respostasUsuario !== 'undefined' ? respostasUsuario : {};
    const exp = typeof isExpired !== 'undefined' ? isExpired : false;

    // 2. ELEMENTOS DO DOM
    const confirmButton = document.querySelector(".confirm-button");
    const questionTitle = document.querySelector("#question-title");
    const progressStatus = document.querySelector("#progress-text");
    const progressFill = document.querySelector("#progress-fill");
    const answersContainer = document.querySelector("#answers-container");
    const resultCard = document.querySelector("#result-card");
    const quizContainer = document.querySelector("#quiz-container");
    const userXp = document.querySelector("#user-xp");
    const imageWrap = document.querySelector("#question-image-wrap");
    const quizImg = document.querySelector("#quiz-question-image");

    if (!questionTitle || !answersContainer) return; // Aborta se a tela HTML estiver errada

    // 3. ESTADO DO QUIZ
    let correctCount = 0;
    let incorrectCount = 0;
    let totalPontosGanhos = 0;
    let isAdvancing = false;
    let toastTimer; // Variável do Toast que não pode sumir!

    const totalPontosPossiveis = data.reduce((acc, q) => acc + Number(q.valor || 0), 0);
    const answeredCount = Object.keys(hist).length;

    const firstUnansweredIndex = data.findIndex(q => !hist[q.id_pergunta]);
    const isFullyAnswered = answeredCount >= data.length;
    let currentQuestionIndex = (isFullyAnswered || exp) ? 0 : (firstUnansweredIndex !== -1 ? firstUnansweredIndex : 0);

    // 4. PROCESSA O HISTÓRICO DO BANCO
    data.forEach(q => {
        const userRespId = hist[q.id_pergunta];
        if (userRespId) {
            const respostas = q.respostas || [];
            const respObj = respostas.find(r => r.id_resposta == userRespId);
            if (respObj && respObj.solucao) {
                correctCount++;
                totalPontosGanhos += Number(q.valor || 0);
            } else {
                incorrectCount++;
            }
        }
    });

    if (userXp) userXp.textContent = `${totalPontosGanhos} PONTOS`;
    updateProgressUi(answeredCount);
    loadQuestion(currentQuestionIndex);

    // 5. FUNÇÕES DE INTERFACE (UI)
    function showToast(message) {
        const toast = document.querySelector("#toast");
        if (!toast) return;
        toast.textContent = message;
        toast.classList.add("is-visible");
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toast.classList.remove("is-visible"), 2200);
    }

    function updateProgressUi(count) {
        if (progressStatus) progressStatus.textContent = `${count} / ${data.length} RESPONDIDAS`;
        if (progressFill) progressFill.style.width = `${(count / data.length) * 100}%`;
    }

    function loadQuestion(index) {
        if (index >= data.length) {
            showCompletionStats();
            return;
        }

        const question = data[index];
        questionTitle.textContent = question.pergunta;
        answersContainer.innerHTML = '';
        isAdvancing = false;

        // Lógica de Imagem
        if (question.imagem && question.imagem.trim() !== "") {
            quizImg.src = question.imagem;
            imageWrap.style.display = "block";
        } else {
            quizImg.src = "";
            imageWrap.style.display = "none";
        }

        const letras = ['A', 'B', 'C', 'D', 'E'];
        const historicalAnswerId = hist[question.id_pergunta];
        const isLocked = historicalAnswerId || exp;
        const respostas = question.respostas || [];

        respostas.forEach((resposta, idx) => {
            const btn = document.createElement('button');
            btn.className = 'answer';
            btn.type = 'button';
            btn.dataset.id = resposta.id_resposta;
            btn.dataset.solucao = resposta.solucao ? "1" : "0";

            btn.innerHTML = `<span class="answer-letter">${letras[idx] || '-'}</span><span class="answer-text">${resposta.resposta}</span><span class="choice-ring"></span>`;

            if (isLocked) {
                btn.style.cursor = 'default';
                if (resposta.solucao) btn.classList.add("is-correct");
                if (historicalAnswerId && resposta.id_resposta == historicalAnswerId && !resposta.solucao) btn.classList.add("is-incorrect");
            } else {
                btn.addEventListener('click', () => {
                    if (isAdvancing) return;
                    document.querySelectorAll('.answer').forEach(b => b.classList.remove('is-selected'));
                    btn.classList.add('is-selected');
                });
            }
            answersContainer.appendChild(btn);
        });

        confirmButton.disabled = false;
        if (isLocked) {
            confirmButton.textContent = "PRÓXIMA QUESTÃO ▸";
            confirmButton.dataset.action = "next";
        } else {
            confirmButton.textContent = "CONFIRMAR RESPOSTA";
            confirmButton.dataset.action = "save";
        }
    }

    function showCompletionStats() {
        quizContainer.style.display = 'none';
        resultCard.style.display = 'block';
        updateProgressUi(data.length);

        const skippedCountEl = document.querySelector("#skipped-count");
        if (skippedCountEl) {
            skippedCountEl.textContent = `${totalPontosGanhos} / ${totalPontosPossiveis}`;
            if (skippedCountEl.previousElementSibling) skippedCountEl.previousElementSibling.textContent = "Pontuação Obtida";
        }

        const correctCountEl = document.querySelector("#correct-count");
        if (correctCountEl) correctCountEl.textContent = correctCount;

        const incorrectCountEl = document.querySelector("#incorrect-count");
        if (incorrectCountEl) incorrectCountEl.textContent = incorrectCount;
    }

    // 6. FUNÇÃO DE SALVAR NO BANCO (COM PROTEÇÕES)
    async function salvarRespostaNoBanco(idResposta) {
        try {
            const question = data[currentQuestionIndex];
            if (!question) throw new Error("A pergunta sumiu da memória do JS.");

            // Limpa barras duplas na URL (evita erros 404)
            const cleanUrlBase = urlBase.replace(/\/+$/, '');
            const endpoint = `${cleanUrlBase}/quiz/${listaId}/pergunta/${question.id_pergunta}/responder`;

            // Cronômetro para não travar a tela se o banco cair
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 8000);

            const resposta = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ id_resposta: idResposta }),
                signal: controller.signal
            });

            clearTimeout(timeoutId);

            if (!resposta.ok) {
                const erroData = await resposta.json().catch(() => ({}));
                console.error("Laravel recusou:", erroData);
                alert("O banco recusou salvar! Erro: " + (erroData.error || erroData.message || "Erro de Servidor (500)"));
                return false;
            }

            return true;
        } catch (error) {
            console.error("Erro Crítico no Fetch:", error);
            if (error.name === 'AbortError') {
                alert("O servidor demorou muito. O Banco de Dados travou!");
            } else {
                alert("Falha na rede ou Rota inexistente. Verifique o console (F12).");
            }
            return false;
        }
    }

    // 7. EVENTO DO BOTÃO PRINCIPAL
    confirmButton.addEventListener("click", async () => {
        if (isAdvancing) return;

        if (confirmButton.dataset.action === "next") {
            currentQuestionIndex++;
            loadQuestion(currentQuestionIndex);
            return;
        }

        const selected = document.querySelector(".answer.is-selected");
        if (!selected) return alert("Selecione uma alternativa antes de confirmar.");

        isAdvancing = true;
        confirmButton.disabled = true;
        confirmButton.textContent = "SALVANDO...";

        const answerId = selected.dataset.id;

        try {
            const salvouComSucesso = await salvarRespostaNoBanco(answerId);

            if (!salvouComSucesso) {
                confirmButton.disabled = false;
                confirmButton.textContent = "TENTAR NOVAMENTE";
                isAdvancing = false;
                return;
            }

            const isCorrect = selected.dataset.solucao === "1";
            const question = data[currentQuestionIndex];
            hist[question.id_pergunta] = answerId;

            if (isCorrect) {
                correctCount++;
                totalPontosGanhos += Number(question.valor || 0);
                selected.classList.add("is-correct");
                if (userXp) userXp.textContent = `${totalPontosGanhos} PONTOS`;
                showToast("Resposta salva!");
            } else {
                incorrectCount++;
                selected.classList.add("is-incorrect");
                const correctEl = document.querySelector(`.answer[data-solucao="1"]`);
                if (correctEl) correctEl.classList.add("is-correct");
                showToast("Resposta salva!");
            }

            updateProgressUi(Object.keys(hist).length);

            setTimeout(() => {
                currentQuestionIndex++;
                loadQuestion(currentQuestionIndex);
            }, 1200);

        } catch (fatalError) {
            console.error("Crash no Script:", fatalError);
            confirmButton.disabled = false;
            confirmButton.textContent = "ERRO FATAL (TENTAR NOVAMENTE)";
            isAdvancing = false;
            alert("Erro interno do navegador: Uma função parou de funcionar.");
        }
    });
});
