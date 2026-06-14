<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Criar Novo Ranking - CorpWare</title>
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/create_user.css') }}">
    </head>
<body>
    <main class="create-user-page ranking-page">
        <header class="page-header">
            <div class="page-title">
                <h1>Criar Novo Ranking</h1>
                <p>Defina o título, a quantidade de participantes e a descrição que vão aparecer na listagem.</p>
            </div>
            <a class="back-button" href="{{ route('admin.rankings.index') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 12H5"></path><path d="m12 19-7-7 7-7"></path></svg> Voltar
            </a>
        </header>

        <form class="create-user-form" action="{{ route('admin.rankings.store') }}" method="POST">
            @csrf
            <section class="form-grid ranking-form-grid">
                <div class="form-card personal-card ranking-details-card">
                    <div class="card-heading">
                        <span class="heading-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M6 20V10"></path><path d="M12 20V4"></path><path d="M18 20v-7"></path><path d="M4 20h16"></path></svg></span>
                        <div><h2>Dados do Ranking</h2></div>
                    </div>

                    <label class="field ranking-title-field">
                        <span>Título</span>
                        <input type="text" name="titulo" value="{{ old('titulo') }}" placeholder="Ex: Top desempenho" maxlength="30" required>
                    </label>

                    <label class="field ranking-number-field">
                        <span>Quantidade de pessoas</span>
                        <input type="number" name="qtd_pessoas" value="{{ old('qtd_pessoas') }}" placeholder="Ex: 10" min="1" required>
                    </label>

                    <label class="field ranking-about-field">
                        <span>Sobre</span>
                        <textarea name="sobre" placeholder="Descrição do ranking">{{ old('sobre') }}</textarea>
                    </label>
                </div>

                <aside class="form-card ranking-preview-card" aria-label="Prévia do ranking">
                    <div class="ranking-preview-top">
                        <span class="heading-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M8 21h8"></path><path d="M12 17v4"></path><path d="M7 4h10v5a5 5 0 0 1-10 0V4Z"></path><path d="M5 5H3v2a4 4 0 0 0 4 4"></path><path d="M19 5h2v2a4 4 0 0 1-4 4"></path></svg></span>
                        <span class="preview-label" style="font-weight:bold; color:var(--cyan);">Prévia</span>
                    </div>

                    <div class="ranking-preview-title" id="ranking-preview-title" style="font-size: 24px; font-weight: bold; margin-top: 15px;">Top desempenho</div>
                    <p class="ranking-preview-description" id="ranking-preview-description" style="color: var(--muted); margin-top: 10px;">Descrição do ranking</p>

                    <div class="ranking-preview-metrics" style="display: flex; gap: 20px; margin-top: 20px;">
                        <div>
                            <span id="ranking-preview-count" style="font-size: 20px; font-weight: bold;">10</span>
                            <small style="display: block; color: var(--muted);">participantes</small>
                        </div>
                    </div>
                </aside>
            </section>

            <footer class="form-actions">
                <a class="cancel-button" href="{{ route('admin.rankings.index') }}">Cancelar</a>
                <button class="save-button" type="submit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z"></path><path d="M17 21v-8H7v8"></path><path d="M7 3v5h8"></path></svg> Salvar Ranking
                </button>
            </footer>
        </form>
    </main>

    <script>
        const titleInput = document.querySelector('input[name="titulo"]');
        const countInput = document.querySelector('input[name="qtd_pessoas"]');
        const aboutInput = document.querySelector('textarea[name="sobre"]');
        const previewTitle = document.querySelector("#ranking-preview-title");
        const previewCount = document.querySelector("#ranking-preview-count");
        const previewDescription = document.querySelector("#ranking-preview-description");

        function updatePreview() {
            previewTitle.textContent = titleInput.value.trim() || "Top desempenho";
            previewCount.textContent = countInput.value || "10";
            previewDescription.textContent = aboutInput.value.trim() || "Descrição do ranking";
        }

        [titleInput, countInput, aboutInput].forEach((input) => {
            input.addEventListener("input", updatePreview);
        });

        // Dispara uma vez no load para preencher caso tenha old() values do Laravel
        updatePreview();
    </script>
</body>
</html>
