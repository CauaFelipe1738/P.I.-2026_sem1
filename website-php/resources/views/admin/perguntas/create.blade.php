<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($pergunta) ? 'Editar' : 'Criar' }} Pergunta</title>
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/create_pergunta.css') }}">
</head>

<body>
    <main class="question-page">

        <header class="page-header">
            <div>
                <h1>{{ isset($pergunta) ? 'Editar Pergunta' : 'Criar Nova Pergunta' }}</h1>
                <p>Atualize os dados, área e alternativas da pergunta.</p>
            </div>

            <div class="header-actions">
                <a class="button button-secondary" href="{{ route('admin.perguntas.index') }}" id="cancel-question">Cancelar</a>
                <button class="button button-primary" type="submit" form="question-form">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z"></path>
                        <path d="M17 21v-8H7v8"></path>
                        <path d="M7 3v5h8"></path>
                    </svg>
                    {{ isset($pergunta) ? 'Salvar Alterações' : 'Salvar Pergunta' }}
                </button>
            </div>
        </header>

        @if ($errors->any())
            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #fca5a5; padding: 15px 20px; border-radius: 8px; margin-bottom: 24px;">
                <ul style="margin: 0; padding-left: 20px; font-size: 14px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="question-form" id="question-form"
              action="{{ isset($pergunta) ? route('admin.perguntas.update', $pergunta->id_pergunta) : route('admin.perguntas.store') }}"
              method="POST">
            @csrf
            @if(isset($pergunta)) @method('PUT') @endif

            <section class="panel question-details" aria-labelledby="question-title">
                <label class="field question-field">
                    <span id="question-title">Pergunta</span>
                    <span class="textarea-wrap">
                        <textarea name="pergunta" required id="question-text">{{ old('pergunta', $pergunta->pergunta ?? '') }}</textarea>
                    </span>
                </label>

                <label class="field theme-field">
                    <span>Área da pergunta</span>
                    <span class="theme-control">
                        <select name="idf_area" required style="padding-left: 20px;">
                            <option value="">Selecione uma Área</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id_area }}" {{ (old('idf_area', $pergunta->idf_area ?? '') == $area->id_area) ? 'selected' : '' }}>
                                    {{ $area->nome_area }}
                                </option>
                            @endforeach
                        </select>
                    </span>
                </label>

                <div class="two-columns" style="align-items: start;">

                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        <label class="field" style="margin: 0;">
                            <span>Adicionar imagem (Link URL)</span>
                            <span class="image-control">
                                <input type="url" name="imagem" id="question-image" placeholder="Ex: https://site.com/imagem.png" value="{{ old('imagem', $pergunta->imagem ?? '') }}">
                            </span>
                        </label>

                        <div id="image-preview-container" style="display: {{ isset($pergunta) && $pergunta->imagem ? 'block' : 'none' }}; border: 1px dashed rgba(141, 165, 203, 0.28); padding: 10px; border-radius: 8px; text-align: center; background: rgba(0,0,0,0.2);">
                            <img id="image-preview" src="{{ old('imagem', $pergunta->imagem ?? '') }}" style="max-width: 100%; max-height: 150px; border-radius: 4px;" alt="Prévia">
                        </div>
                    </div>

                    <label class="field" style="margin: 0;">
                        <span>Pontuação</span>
                        <span class="score-control">
                            <input type="number" name="valor" value="{{ old('valor', $pergunta->valor ?? '') }}" min="0" required id="question-score">
                            <small>pontos</small>
                        </span>
                    </label>
                </div>

            <section class="panel answers-panel" aria-labelledby="answers-title">
                <div class="section-heading">
                    <h2 id="answers-title">Respostas (Mín: 2 | Máx: 5)</h2>
                    <p>Adicione as alternativas e marque o "bolinha" da correta.</p>
                </div>

                <div class="answer-list" id="answer-list">
                    </div>

                <button class="add-answer" type="button" id="btn-add-answer">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path d="M12 5v14"></path>
                        <path d="M5 12h14"></path>
                    </svg>
                    Adicionar Resposta
                </button>
            </section>
        </form>
    </main>

    <script>
        // 1. Lógica de Prévia de Imagem
        const imageInput = document.getElementById('question-image');
        const previewContainer = document.getElementById('image-preview-container');
        const previewImage = document.getElementById('image-preview');

        imageInput.addEventListener('input', function() {
            const url = this.value.trim();
            if (url) {
                previewImage.src = url;
                previewContainer.style.display = 'block';
            } else {
                previewImage.src = '';
                previewContainer.style.display = 'none';
            }
        });

        // 2. Lógica Dinâmica das Respostas
        const answerList = document.getElementById('answer-list');
        const btnAddAnswer = document.getElementById('btn-add-answer');
        const letras = "ABCDE"; // Limite de 5
        let count = 0;

        // Se estivermos editando, o Laravel injeta as respostas existentes no JS
        const respostasExistentes = @json($respostas ?? []);

        function renderAnswers() {
            const lines = answerList.querySelectorAll('.answer-option');
            lines.forEach((line, index) => {
                line.querySelector('.answer-letter').textContent = letras[index];
                // Atualiza o value do Radio Button para bater com a posição do array do Laravel
                line.querySelector('input[type="radio"]').value = index;
            });

            // Oculta botão de adicionar se bater 5
            btnAddAnswer.style.display = lines.length >= 5 ? 'none' : 'inline-flex';
        }

        function createAnswerNode(texto = "", isCorrect = false) {
            if (answerList.querySelectorAll('.answer-option').length >= 5) return;

            const div = document.createElement('div');
            div.className = "answer-option";
            // Repare no name="respostas[]" -> Isso vira array no Laravel!
            div.innerHTML = `
                <span class="answer-letter"></span>
                <input type="radio" name="solucao_index" required ${isCorrect ? 'checked' : ''} style="cursor: pointer;">
                <input type="text" name="respostas[]" value="${texto.replace(/"/g, '&quot;')}" required class="answer-text" placeholder="Digite a resposta aqui" style="flex-grow: 1; border: none; background: transparent; color: white; outline: none; font-size: 16px;">
                <button class="delete-answer" type="button" onclick="removeAnswer(this)" style="background: transparent; border: none; color: #ef4444; cursor: pointer;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="20" height="20"><path d="M3 6h18"></path><path d="M8 6V4h8v2"></path><path d="M19 6 18 20H6L5 6"></path></svg>
                </button>
            `;
            answerList.appendChild(div);
            renderAnswers();
        }

        function removeAnswer(btn) {
            if (answerList.querySelectorAll('.answer-option').length <= 2) {
                alert("A pergunta precisa ter pelo menos duas respostas.");
                return;
            }
            btn.closest('.answer-option').remove();
            renderAnswers();
        }

        btnAddAnswer.addEventListener('click', () => createAnswerNode());

        // Inicialização
        if (respostasExistentes.length > 0) {
            // Se for edição, carrega as do banco
            respostasExistentes.forEach(resp => {
                createAnswerNode(resp.resposta, resp.solucao == 1);
            });
        } else {
            // Se for criação nova, coloca 2 campos vazios obrigatórios
            createAnswerNode();
            createAnswerNode();
        }
    </script>
</body>
</html>
