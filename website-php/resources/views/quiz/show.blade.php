<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz: Lista #{{ $lista->id_lista }} - CorpWare</title>
    <link rel="stylesheet" href="{{ asset('css/pages/quiz.css') }}">

    <script>
        // Imprime a string do JSON sem escapar os caracteres especiais
        const quizData = {!! $perguntasJson !!};
        const listaId = {{ $lista->id_lista }};
        const urlBase = "{{ url('/') }}";
        const csrfToken = "{{ csrf_token() }}";
    </script>
</head>
<body>
    <div class="shell">
        <header class="topbar">
            <div class="brand-row">
                <a class="icon-button close" aria-label="Fechar" href="{{ route('dashboard') }}">
                    <span></span>
                    <span></span>
                </a>
                <div class="brand">CorpWare</div>
            </div>

            <div class="module-progress" aria-label="Progresso do módulo">
                <div class="progress-meta">
                    <span id="progress-text">1 / {{ count($lista->perguntas) }} QUESTÕES</span>
                </div>
                <div class="progress-track">
                    <div id="progress-fill" class="progress-fill" style="width: 0%;"></div>
                </div>
            </div>

            <div class="stats">
                <div class="xp">
                    <span aria-hidden="true">⚡</span>
                    <span id="user-xp">{{ number_format(auth()->user()->pontos, 0, ',', '.') }} PONTOS</span>
                </div>
            </div>
        </header>

        <main class="challenge">
            <section class="question-panel" aria-labelledby="question-title" id="quiz-container">

                <h1 id="question-title">Carregando pergunta...</h1>

                <div class="answers" id="answers-container" role="radiogroup" aria-label="Alternativas">
                </div>

                <div class="action-row">
                    <button class="skip-button" type="button" id="btn-skip">▸ PULAR QUESTÃO</button>
                    <button class="confirm-button" type="button" id="btn-confirm" disabled>CONFIRMAR RESPOSTA</button>
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
                        <span>Questões puladas</span>
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

    <script src="{{ asset('js/pages/quiz.js') }}"></script>
</body>
</html>
