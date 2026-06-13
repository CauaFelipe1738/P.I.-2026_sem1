
        const form = document.querySelector(".question-form");
        const questionText = document.querySelector("#question-text");
        const themeInput = document.querySelector("#question-theme");
        const answerList = document.querySelector(".answer-list");
        const addAnswerButton = document.querySelector(".add-answer");
        const saveButton = document.querySelector("#save-question");
        const scoreInput = document.querySelector("#question-score");
        const imageInput = document.querySelector("#question-image");
        const themeModal = document.querySelector("#theme-modal");
        const themeModalTitle = document.querySelector("#theme-modal-title");
        const openThemeModalButton = document.querySelector("#open-theme-modal");
        const editThemeButton = document.querySelector("#edit-theme");
        const deleteThemeButton = document.querySelector("#delete-theme");
        const closeThemeModalButton = document.querySelector("#close-theme-modal");
        const cancelThemeModalButton = document.querySelector("#cancel-theme-modal");
        const saveThemeButton = document.querySelector("#save-theme");
        const newThemeInput = document.querySelector("#new-theme-name");

        const letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        const THEMES_STORAGE_KEY = "temasPergunta";
        const defaultThemes = [
        ];
        let toastTimer;
        let editingTheme = "";

        function showToast(message, type = "success") {
            let toast = document.querySelector(".page-toast");

            if (!toast) {
                toast = document.createElement("div");
                toast.className = "page-toast";
                document.body.appendChild(toast);
            }

            toast.textContent = message;
            toast.classList.toggle("is-error", type === "error");
            toast.classList.add("is-visible");

            clearTimeout(toastTimer);
            toastTimer = setTimeout(() => {
                toast.classList.remove("is-visible");
            }, 2600);
        }

        function updateCounter(textarea) {
            const counter = textarea.closest(".textarea-wrap").querySelector("small");
            counter.textContent = `${textarea.value.length}/${textarea.maxLength}`;
        }

        function normalizeThemeName(theme) {
            return theme.trim().replace(/\s+/g, " ");
        }

        function getStoredThemes() {
            try {
                const storedThemes = JSON.parse(localStorage.getItem(THEMES_STORAGE_KEY)) || [];
                return Array.isArray(storedThemes) ? storedThemes : [];
            } catch (error) {
                return [];
            }
        }

        function getThemes() {
            const themes = [...defaultThemes, ...getStoredThemes()].map(normalizeThemeName).filter(Boolean);
            return [...new Set(themes)];
        }

        function renderThemeOptions(selectedTheme = "") {
            if (!themeInput || themeInput.tagName !== "SELECT") {
                return;
            }

            const themes = getThemes();
            themeInput.innerHTML = '<option value="">Selecione um tema</option>';

            themes.forEach((theme) => {
                const option = document.createElement("option");
                option.value = theme;
                option.textContent = theme;
                option.selected = theme === selectedTheme;
                themeInput.appendChild(option);
            });
        }

        function openThemeModal(themeName = "") {
            if (!themeModal || !newThemeInput) {
                return;
            }

            editingTheme = normalizeThemeName(themeName);
            themeModalTitle.textContent = editingTheme ? "Editar tema" : "Criar tema";
            themeModal.classList.add("is-visible");
            themeModal.setAttribute("aria-hidden", "false");
            newThemeInput.value = editingTheme;
            newThemeInput.classList.remove("is-invalid");
            setTimeout(() => newThemeInput.focus(), 0);
        }

        function closeThemeModal() {
            if (!themeModal) {
                return;
            }

            themeModal.classList.remove("is-visible");
            themeModal.setAttribute("aria-hidden", "true");
            editingTheme = "";
        }

        function saveNewTheme() {
            if (!newThemeInput) {
                return;
            }

            const themeName = normalizeThemeName(newThemeInput.value);
            const existingTheme = getThemes().find((theme) => theme.toLowerCase() === themeName.toLowerCase() && theme.toLowerCase() !== editingTheme.toLowerCase());

            newThemeInput.classList.remove("is-invalid");

            if (!themeName) {
                newThemeInput.classList.add("is-invalid");
                showToast("Digite o nome do tema.", "error");
                return;
            }

            if (existingTheme) {
                renderThemeOptions(existingTheme);
                closeThemeModal();
                showToast("Tema selecionado.");
                return;
            }

            const isEditing = Boolean(editingTheme);
            let storedThemes = getStoredThemes();

            if (isEditing) {
                storedThemes = storedThemes.map((theme) => theme.toLowerCase() === editingTheme.toLowerCase() ? themeName : theme);
            } else {
                storedThemes.push(themeName);
            }

            localStorage.setItem(THEMES_STORAGE_KEY, JSON.stringify(storedThemes));
            renderThemeOptions(themeName);
            closeThemeModal();
            showToast(isEditing ? "Tema atualizado com sucesso." : "Tema criado com sucesso.");
        }

        function editSelectedTheme() {
            const selectedTheme = normalizeThemeName(themeInput.value);

            if (!selectedTheme) {
                showToast("Selecione um tema para editar.", "error");
                return;
            }

            openThemeModal(selectedTheme);
        }

        function deleteSelectedTheme() {
            const selectedTheme = normalizeThemeName(themeInput.value);

            if (!selectedTheme) {
                showToast("Selecione um tema para excluir.", "error");
                return;
            }

            if (!confirm(`Deseja excluir o tema "${selectedTheme}"?`)) {
                return;
            }

            const storedThemes = getStoredThemes().filter((theme) => theme.toLowerCase() !== selectedTheme.toLowerCase());
            localStorage.setItem(THEMES_STORAGE_KEY, JSON.stringify(storedThemes));
            renderThemeOptions();
            showToast("Tema excluido com sucesso.");
        }

        function refreshAnswerLetters() {
            if (!answerList) {
                return;
            }

            const options = answerList.querySelectorAll(".answer-option");

            options.forEach((option, index) => {
                const letter = letters[index] || index + 1;
                const radio = option.querySelector('input[type="radio"]');
                const deleteButton = option.querySelector(".delete-answer");

                option.querySelector(".answer-letter").textContent = letter;
                radio.setAttribute("aria-label", `Marcar alternativa ${letter} como correta`);
                deleteButton.setAttribute("aria-label", `Excluir alternativa ${letter}`);
            });
        }

        function setCorrectAnswer(selectedRadio) {
            if (!answerList) {
                return;
            }

            answerList.querySelectorAll(".answer-option").forEach((option) => {
                const radio = option.querySelector('input[type="radio"]');
                const oldBadge = option.querySelector(".correct-badge");
                const isCorrect = radio === selectedRadio;

                option.classList.toggle("correct", isCorrect);

                if (oldBadge) {
                    oldBadge.remove();
                }

                if (isCorrect) {
                    const badge = document.createElement("span");
                    badge.className = "correct-badge";
                    badge.textContent = "Resposta correta";
                    option.insertBefore(badge, option.querySelector(".delete-answer"));
                }
            });
        }

        function makeAnswerEditable(answerText) {
            answerText.contentEditable = "true";
            answerText.setAttribute("role", "textbox");
            answerText.setAttribute("aria-label", "Texto da alternativa");
            answerText.addEventListener("keydown", (event) => {
                if (event.key === "Enter") {
                    event.preventDefault();
                    answerText.blur();
                }
            });
        }

        function createAnswer(text = "Nova resposta") {
            if (!answerList) {
                return;
            }

            const option = document.createElement("div");
            option.className = "answer-option";
            option.innerHTML = `
                <span class="answer-letter"></span>
                <input type="radio" name="correct-answer">
                <span class="answer-text">${text}</span>
                <button class="delete-answer" type="button">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path d="M3 6h18"></path>
                        <path d="M8 6V4h8v2"></path>
                        <path d="M19 6 18 20H6L5 6"></path>
                        <path d="M10 11v5"></path>
                        <path d="M14 11v5"></path>
                    </svg>
                </button>
            `;

            makeAnswerEditable(option.querySelector(".answer-text"));
            answerList.appendChild(option);
            refreshAnswerLetters();
            option.querySelector(".answer-text").focus();
        }

        function getQuestionData() {
            const answers = answerList ? [...answerList.querySelectorAll(".answer-option")].map((option, index) => ({
                letter: letters[index] || String(index + 1),
                text: option.querySelector(".answer-text").textContent.trim(),
                correct: option.querySelector('input[type="radio"]').checked
            })) : [];

            return {
                question: questionText.value.trim(),
                theme: themeInput.value.trim(),
                image: imageInput.value.trim(),
                score: Number(scoreInput.value),
                answers
            };
        }

        function validateQuestion(data) {
            form.querySelectorAll(".is-invalid").forEach((item) => item.classList.remove("is-invalid"));

            if (!data.question) {
                questionText.classList.add("is-invalid");
                return "Digite o texto da pergunta.";
            }

            if (!data.theme) {
                themeInput.classList.add("is-invalid");
                return "Digite o tema da pergunta.";
            }

            if (!Number.isFinite(data.score) || data.score < 0) {
                scoreInput.classList.add("is-invalid");
                return "Informe uma pontuação válida.";
            }

            if (answerList && data.answers.length < 2) {
                return "Adicione pelo menos duas respostas.";
            }

            const emptyAnswer = data.answers.findIndex((answer) => !answer.text);

            if (emptyAnswer !== -1) {
                if (answerList) {
                    answerList.querySelectorAll(".answer-text")[emptyAnswer].classList.add("is-invalid");
                }
                return "Preencha todas as alternativas.";
            }

            if (answerList && !data.answers.some((answer) => answer.correct)) {
                return "Marque uma resposta correta.";
            }

            return "";
        }

        [questionText].forEach((textarea) => {
            updateCounter(textarea);
            textarea.addEventListener("input", () => updateCounter(textarea));
        });

        renderThemeOptions();

        if (openThemeModalButton) {
            openThemeModalButton.addEventListener("click", () => openThemeModal());
        }

        if (editThemeButton) {
            editThemeButton.addEventListener("click", editSelectedTheme);
        }

        if (deleteThemeButton) {
            deleteThemeButton.addEventListener("click", deleteSelectedTheme);
        }

        if (closeThemeModalButton) {
            closeThemeModalButton.addEventListener("click", closeThemeModal);
        }

        if (cancelThemeModalButton) {
            cancelThemeModalButton.addEventListener("click", closeThemeModal);
        }

        if (saveThemeButton) {
            saveThemeButton.addEventListener("click", saveNewTheme);
        }

        if (themeModal) {
            themeModal.addEventListener("click", (event) => {
                if (event.target === themeModal) {
                    closeThemeModal();
                }
            });
        }

        if (newThemeInput) {
            newThemeInput.addEventListener("keydown", (event) => {
                if (event.key === "Enter") {
                    event.preventDefault();
                    saveNewTheme();
                }

                if (event.key === "Escape") {
                    closeThemeModal();
                }
            });
        }

        if (answerList) {
            answerList.querySelectorAll(".answer-text").forEach(makeAnswerEditable);
            refreshAnswerLetters();

            answerList.addEventListener("change", (event) => {
                if (event.target.matches('input[type="radio"]')) {
                    setCorrectAnswer(event.target);
                }
            });

            answerList.addEventListener("click", (event) => {
                const deleteButton = event.target.closest(".delete-answer");

                if (!deleteButton) {
                    return;
                }

                const options = answerList.querySelectorAll(".answer-option");

                if (options.length <= 2) {
                    showToast("A pergunta precisa ter pelo menos duas respostas.", "error");
                    return;
                }

                const option = deleteButton.closest(".answer-option");
                const wasCorrect = option.querySelector('input[type="radio"]').checked;
                option.remove();

                if (wasCorrect) {
                    const firstRadio = answerList.querySelector('input[type="radio"]');
                    firstRadio.checked = true;
                    setCorrectAnswer(firstRadio);
                }

                refreshAnswerLetters();
                showToast("Resposta removida.");
            });
        }

        if (addAnswerButton) {
            addAnswerButton.addEventListener("click", () => {
                if (answerList.querySelectorAll(".answer-option").length >= letters.length) {
                showToast("Limite de alternativas atingido.", "error");
                return;
                }

                createAnswer();
            });
        }

        saveButton.addEventListener("click", () => {
            const data = getQuestionData();
            const error = validateQuestion(data);

            if (error) {
                showToast(error, "error");
                return;
            }

            localStorage.setItem("perguntaCriada", JSON.stringify(data));
            const savedQuestions = JSON.parse(localStorage.getItem("perguntasAdmin") || "[]");
            savedQuestions.push({
                id: Date.now(),
                question: data.question,
                theme: data.theme,
                image: data.image,
                score: data.score,
                answers: data.answers
            });
            localStorage.setItem("perguntasAdmin", JSON.stringify(savedQuestions));
            showToast("Pergunta salva com sucesso.");
        });
