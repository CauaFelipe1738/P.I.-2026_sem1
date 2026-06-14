<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($ranking) ? 'Editar' : 'Criar Novo' }} Ranking - CorpWare</title>

    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/create_user.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/create_ranking.css') }}">
</head>

<body>
    <main class="create-user-page ranking-page">
        <header class="page-header">
            <div class="page-title">
                <h1>{{ isset($ranking) ? 'Editar' : 'Criar Novo' }} Ranking</h1>
                <p>Defina o título, a quantidade de participantes e a descrição que vão aparecer na listagem.</p>
            </div>

            <a class="back-button" href="{{ route('admin.rankings.index') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                    <path d="M19 12H5"></path>
                    <path d="m12 19-7-7 7-7"></path>
                </svg>
                Voltar para rankings
            </a>
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

        <form class="create-user-form" id="ranking-form"
              action="{{ isset($ranking) ? route('admin.rankings.update', $ranking->id_ranking) : route('admin.rankings.store') }}"
              method="POST">
            @csrf
            @if(isset($ranking))
                @method('PUT')
            @endif

            <section class="form-grid ranking-form-grid">
                <div class="form-card personal-card ranking-details-card">
                    <div class="card-heading">
                        <span class="heading-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M6 20V10"></path>
                                <path d="M12 20V4"></path>
                                <path d="M18 20v-7"></path>
                                <path d="M4 20h16"></path>
                            </svg>
                        </span>
                        <div>
                            <h2>Dados do Ranking</h2>
                            <p>Esses campos usam os mesmos nomes da tabela ranking no banco.</p>
                        </div>
                    </div>

                    <label class="field ranking-title-field">
                        <span>Título</span>
                        <input type="text" name="titulo" value="{{ old('titulo', $ranking->titulo ?? '') }}" placeholder="Ex: Top desempenho" maxlength="30" required>
                    </label>

                    <label class="field ranking-number-field">
                        <span>Quantidade de pessoas</span>
                        <input type="number" name="qtd_pessoas" value="{{ old('qtd_pessoas', $ranking->qtd_pessoas ?? '') }}" placeholder="Ex: 10" min="1" required>
                    </label>

                    <label class="field ranking-about-field">
                        <span>Sobre</span>
                        <textarea name="sobre" placeholder="Descrição do ranking">{{ old('sobre', $ranking->sobre ?? '') }}</textarea>
                    </label>
                </div>

                <aside class="form-card ranking-preview-card" aria-label="Prévia do ranking">
                    <div class="ranking-preview-top">
                        <span class="heading-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M8 21h8"></path>
                                <path d="M12 17v4"></path>
                                <path d="M7 4h10v5a5 5 0 0 1-10 0V4Z"></path>
                                <path d="M5 5H3v2a4 4 0 0 0 4 4"></path>
                                <path d="M19 5h2v2a4 4 0 0 1-4 4"></path>
                            </svg>
                        </span>
                        <span class="preview-label">Prévia</span>
                    </div>

                    <div class="ranking-preview-title" id="ranking-preview-title">Top desempenho</div>
                    <p class="ranking-preview-description" id="ranking-preview-description">
                        Descrição do ranking
                    </p>

                    <div class="ranking-preview-metrics">
                        <div>
                            <span id="ranking-preview-count">10</span>
                            <small>participantes</small>
                        </div>
                        <div>
                            <span id="ranking-preview-start-pos">#1</span>
                            <small>posição inicial</small>
                        </div>
                    </div>

                    <div class="ranking-preview-list" aria-hidden="true">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </aside>

                <aside class="form-card info-card ranking-info-card">
                    <span class="heading-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M12 16v-4"></path>
                            <path d="M12 8h.01"></path>
                        </svg>
                    </span>

                    <h2>Campos salvos</h2>
                    <p><strong>título</strong> identifica o ranking na tela administrativa.</p>
                    <p><strong>qtd_pessoas</strong> define até qual posição o colaborador ganha este título.</p>
                    <p><strong>sobre</strong> guarda a descrição vinculada ao ranking.</p>
                </aside>
            </section>

            <footer class="form-actions">
                <a class="cancel-button" href="{{ route('admin.rankings.index') }}">Cancelar</a>
                <button class="save-button" type="submit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z"></path>
                        <path d="M17 21v-8H7v8"></path>
                        <path d="M7 3v5h8"></path>
                    </svg>
                    {{ isset($ranking) ? 'Salvar Alterações' : 'Salvar Ranking' }}
                </button>
            </footer>
        </form>
    </main>

    <script>
        const form = document.querySelector("#ranking-form");
        const titleInput = form.querySelector('input[name="titulo"]');
        const countInput = form.querySelector('input[name="qtd_pessoas"]');
        const aboutInput = form.querySelector('textarea[name="sobre"]');

        const previewTitle = document.querySelector("#ranking-preview-title");
        const previewCount = document.querySelector("#ranking-preview-count");
        const previewStartPos = document.querySelector("#ranking-preview-start-pos");
        const previewDescription = document.querySelector("#ranking-preview-description");

        // Injeta a lista atual de limites cadastrados no banco via Blade para o JS calcular as folgas
        const rankingsExistentes = @json(\App\Models\Ranking::orderBy('qtd_pessoas', 'asc')->get(['id_ranking', 'qtd_pessoas'])->pluck('qtd_pessoas'));
        const idAtual = @json($ranking->id_ranking ?? null);
        const qtdAtualOriginal = @json($ranking->qtd_pessoas ?? null);

        function calcularMetricasPrevia(novaQtd) {
            if (!novaQtd || novaQtd <= 0) {
                return { posicaoInicial: 1, participantes: 0 };
            }

            // Clona a lista de limites e remove o valor antigo caso seja uma edição
            let limites = [...rankingsExistentes];
            if (idAtual && qtdAtualOriginal) {
                limites = limites.filter(q => q !== qtdAtualOriginal);
            }

            // Insere temporariamente o novo limite digitado e ordena
            limites.push(novaQtd);
            limites.sort((a, b) => a - b);

            // Encontra onde o valor atual se encaixa na fila de precedência
            const idx = limites.indexOf(novaQtd);

            // Regra de precedência: quem tem o menor limite vem antes
            let posicaoInicial = 1;
            if (idx > 0) {
                // A posição inicial é o limite do anterior + 1
                posicaoInicial = limites[idx - 1] + 1;
            }

            // Quantidade de participantes que entram nessa faixa
            let participantes = novaQtd - (posicaoInicial - 1);
            if (participantes < 0) participantes = 0;

            return {
                posicaoInicial: posicaoInicial,
                participantes: participantes
            };
        }

        function updatePreview() {
            const tituloValue = titleInput.value.trim();
            const qtdValue = parseInt(countInput.value, 10);
            const sobreValue = aboutInput.value.trim();

            previewTitle.textContent = tituloValue || "Top desempenho";
            previewDescription.textContent = sobreValue || "Descrição do ranking";

            const metricas = calcularMetricasPrevia(qtdValue);

            previewCount.textContent = metricas.participantes;
            previewStartPos.textContent = `#${metricas.posicaoInicial}`;
        }

        // Eventos para escutar a digitação e mudar a prévia na hora
        [titleInput, countInput, aboutInput].forEach((input) => {
            input.addEventListener("input", updatePreview);
        });

        // Dispara no carregamento inicial da página
        updatePreview();
    </script>
</body>

</html>
