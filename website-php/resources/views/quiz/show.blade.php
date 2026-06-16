<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz: Lista #{{ $lista->id_lista }} - CorpWare</title>
    <link rel="stylesheet" href="{{ asset('css/pages/quiz.css') }}">

    <script>
        const quizData = {!! $perguntasJson !!};
        const respostasUsuario = {!! json_encode((object)$respostasUsuario) !!};
        const isExpired = {{ isset($isExpired) && $isExpired ? 'true' : 'false' }};
        const listaId = {{ $lista->id_lista }};
        const urlBase = "{{ url('/') }}";
        const csrfToken = "{{ csrf_token() }}";
    </script>

    <style>
        .challenge {
            display: block;
            max-width: 1350px;
            margin: 4rem auto 0;
            padding: 0 2rem;
        }

        .result-card {
            max-width: 420px;
            margin: 0 auto;
        }

        .question-grid {
            display: grid;
            grid-template-columns: minmax(450px, 5.5fr) minmax(300px, 4.5fr);
            grid-template-areas:
                "title image"
                "answers image"
                "action image";
            gap: 1.5rem 4rem;
            align-items: start;
        }

        .q-title { grid-area: title; margin-bottom: 0 !important; }
        .q-answers { grid-area: answers; }
        .q-action { grid-area: action; margin-top: 1rem !important; }

        .q-image {
            grid-area: image;
            width: 100%;
            position: sticky;
            top: 2rem;
        }

        .q-image img {
            width: 100%;
            max-height: 75vh;
            border-radius: 12px;
            border: 1px solid var(--border);
            box-shadow: 0 16px 45px rgba(0,0,0,0.25);
            object-fit: contain;
            background: rgba(15, 18, 27, 0.4);
            padding: 8px;
        }

        @media (max-width: 950px) {
            .question-grid {
                grid-template-columns: 1fr;
                grid-template-areas:
                    "title"
                    "image"
                    "answers"
                    "action";
                gap: 1.5rem;
            }
            .q-image {
                position: static;
            }
            .q-image img {
                max-height: 400px;
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <header class="topbar">
            <div class="brand-row">
                <a class="icon-button close" aria-label="Fechar" href="{{ route('dashboard') }}">
                    <span></span><span></span>
                </a>
                <div class="brand">CorpWare</div>
            </div>

            <div class="module-progress" aria-label="Progresso do módulo">
                <div class="progress-meta">
                    <span id="progress-text">CARREGANDO...</span>
                </div>
                <div class="progress-track">
                    <div id="progress-fill" class="progress-fill" style="width: 0%;"></div>
                </div>
            </div>

            <div class="stats">
                <div class="xp">
                    <span aria-hidden="true">⚡</span>
                    <span id="user-xp">0 PONTOS</span>
                </div>
            </div>
        </header>

        <main class="challenge">
            <section class="question-panel" aria-labelledby="question-title" id="quiz-container">

                <div class="question-grid">

                    <h1 id="question-title" class="q-title">Carregando pergunta...</h1>

                    <div class="q-image" id="question-image-wrap" style="display: none;">
                        <img id="quiz-question-image" src="" alt="Imagem de apoio">
                    </div>

                    <div class="answers q-answers" id="answers-container" role="radiogroup"></div>

                    <div class="action-row q-action">
                        <button class="confirm-button" type="button" id="btn-confirm" disabled style="width: 100%;">CONFIRMAR RESPOSTA</button>
                    </div>

                </div>

            </section>

            <aside class="result-card" id="result-card" aria-label="Resultado da resposta" style="display: none;">
                <div id="completion-banner" class="completion-banner">PARABÉNS</div>

                <div class="result-head">
                    <div class="success-mark">✓</div>
                    <div>
                        <h2 id="completion-title">RESULTADO FINAL</h2>
                        <p id="completion-subtitle" class="quote">Você concluiu este módulo de treinamento!</p>
                    </div>
                </div>

                <div class="score-list" id="completion-summary">
                    <div>
                        <span>Pontuação Obtida</span>
                        <strong id="skipped-count">0</strong>
                    </div>
                    <div>
                        <span>Questões acertadas</span>
                        <strong id="correct-count">0</strong>
                    </div>
                    <div>
                        <span>Questões erradas</span>
                        <strong id="incorrect-count">0</strong>
                    </div>
                    <div style="margin-top: 15px; grid-column: 1 / -1;">
                        <a href="{{ route('dashboard') }}">
                            <button class="confirm-button" style="width: 100%;">VOLTAR AO INÍCIO</button>
                        </a>
                    </div>
                </div>
            </aside>
        </main>
    </div>

    <div id="toast" class="toast" aria-live="polite" aria-atomic="true"></div>

    <script src="{{ asset('js/pages/quiz.js') }}?v={{ time() }}"></script>
</body>
</html>
