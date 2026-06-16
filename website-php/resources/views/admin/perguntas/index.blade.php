<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Perguntas - CorpWare</title>
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/user.css') }}">
    <style>
        .pagination { display: flex; align-items: center; gap: 8px; }
        .pagination p { display: none; }
        .pagination span[aria-current="page"] { display: grid; place-items: center; width: 48px; height: 50px; border: 1px solid #2e73ff; border-radius: 8px; color: #a9d2ff; background: rgba(22, 39, 67, 0.4); font-size: 16px; font-weight: bold; }
        .pagination a, .pagination span { display: grid; place-items: center; width: 40px; height: 40px; color: #8fa4c7; text-decoration: none; border-radius: 8px; font-weight: bold; }
        .pagination a:hover { background: rgba(22, 39, 67, 0.4); }
        .pagination svg { width: 19px; height: 19px; }

        /* Expande a barra de pesquisa já que removemos os botões extras */
        .toolbar { display: flex; width: 100%; margin-bottom: 20px; }
        .search-field { flex-grow: 1; display: flex; align-items: center; background: rgba(9, 19, 34, 0.55); border: 1px solid rgba(141, 165, 203, 0.28); border-radius: 7px; padding: 0 15px; }
        .search-field input { width: 100%; background: transparent; border: none; color: #fff; outline: none; height: 48px; padding-left: 10px; }
    </style>
</head>
<body>
    <main class="users-page">
        <header class="page-header">
            <a class="back-button" href="{{ route('admin.dashboard') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 12H5"></path><path d="m12 19-7-7 7-7"></path></svg> Voltar
            </a>
            <div class="page-title">
                <h1>Editar Perguntas</h1>
                <p>Gerencie e edite informações de perguntas existentes e suas respectivas áreas.</p>
            </div>
            <a class="create-button" href="{{ route('admin.perguntas.create') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg> Nova Pergunta
            </a>
        </header>

        @if (session('success'))
            <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid #10b981; color: #6ee7b7; padding: 15px 20px; border-radius: 8px; margin-bottom: 24px;">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #fca5a5; padding: 15px 20px; border-radius: 8px; margin-bottom: 24px;">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #fca5a5; padding: 15px 20px; border-radius: 8px; margin-bottom: 24px;">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('admin.perguntas.index') }}" method="GET" id="filter-form">
            <section class="toolbar">
                <label class="search-field">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path></svg>
                    <input type="search" name="search" value="{{ $busca ?? '' }}" placeholder="Buscar por texto da pergunta..." onchange="this.form.submit()">
                </label>
            </section>

            <div class="theme-panel" style="margin-bottom: 24px;">
                <div class="theme-panel-copy">
                    <strong>Área</strong>
                    <span>Filtre as perguntas por área cadastrada.</span>
                </div>
                <label class="theme-select-field">
                    <select name="area_id" id="question-theme-filter" onchange="document.getElementById('filter-form').submit()">
                        <option value="">Todas as Áreas</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id_area }}" {{ (isset($areaFiltro) && $areaFiltro == $area->id_area) ? 'selected' : '' }}>
                                {{ $area->nome_area }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <div class="theme-actions" style="display: flex; gap: 10px;">
                    <button class="toolbar-button primary theme-create-button" type="button" onclick="toggleAreaModal(true)">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" style="width:18px; margin-right:8px;"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg> Criar Área
                    </button>
                    <button class="toolbar-button danger theme-delete-button" type="button" id="delete-theme-btn" {{ empty($areaFiltro) ? 'disabled' : '' }}>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" style="width:18px; margin-right:8px;"><path d="M3 6h18"></path><path d="M8 6V4h8v2"></path><path d="M19 6 18 20H6L5 6"></path><path d="M10 11v5"></path><path d="M14 11v5"></path></svg> Deletar Área
                    </button>
                </div>
            </div>
        </form>

        <form action="{{ route('admin.areas.destroy') }}" method="POST" id="form-delete-area" style="display: none;">
            @csrf
            @method('DELETE')
            <input type="hidden" name="area_id" id="hidden-delete-area-id">
        </form>

        <section class="users-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50%;">Pergunta</th>
                        <th>Área</th>
                        <th>Valor</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($perguntas as $pergunta)
                        <tr>
                            <td>
                                <div class="user-cell custom-tooltip" data-texto-tooltip="{{ $pergunta->pergunta }}" style="cursor: help;">
                                    {{ \Illuminate\Support\Str::limit($pergunta->pergunta, 60) }}
                                </div>
                            </td>

                            <td>{{ $pergunta->nome_area }}</td>
                            <td><span class="badge admin">{{ $pergunta->valor }} XP</span></td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('admin.perguntas.edit', $pergunta->id_pergunta) }}" class="icon-button edit-question"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m16 4 4 4L8 20H4v-4L16 4Z"></path><path d="m14 6 4 4"></path></svg></a>
                                    <form action="{{ route('admin.perguntas.destroy', $pergunta->id_pergunta) }}" method="POST" style="display: inline;" onsubmit="return confirm('Excluir esta pergunta permanentemente?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="icon-button delete-question" style="border: none; cursor: pointer;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 6h18"></path><path d="M8 6V4h8v2"></path><path d="M19 6 18 20H6L5 6"></path><path d="M10 11v5"></path><path d="M14 11v5"></path></svg></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align: center; color: var(--muted); height: 120px;">Nenhuma pergunta encontrada.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <footer class="table-footer">
                <span>Mostrando {{ $perguntas->firstItem() ?? 0 }} a {{ $perguntas->lastItem() ?? 0 }} de {{ $perguntas->total() }} perguntas</span>
                @if ($perguntas->hasPages())
                    <nav class="pagination">
                        @if ($perguntas->onFirstPage())
                            <span class="page-arrow" style="opacity: 0.3; cursor: not-allowed;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m15 18-6-6 6-6"></path></svg></span>
                        @else
                            <a href="{{ $perguntas->previousPageUrl() }}" class="page-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m15 18-6-6 6-6"></path></svg></a>
                        @endif
                        <span class="page-number" aria-current="page">{{ $perguntas->currentPage() }}</span>
                        @if ($perguntas->hasMorePages())
                            <a href="{{ $perguntas->nextPageUrl() }}" class="page-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m9 18 6-6-6-6"></path></svg></a>
                        @else
                            <span class="page-arrow" style="opacity: 0.3; cursor: not-allowed;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m9 18 6-6-6-6"></path></svg></span>
                        @endif
                    </nav>
                @endif
            </footer>
        </section>
    </main>

    <div class="modal-overlay" id="area-modal" aria-hidden="true" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 100; align-items: center; justify-content: center;">
        <div class="theme-modal" role="dialog" style="background: #111b2b; padding: 25px; border-radius: 12px; border: 1px solid #38d2f8; min-width: 320px;">
            <div class="theme-modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="color: #fff; font-size: 20px; margin: 0;">Criar nova Área</h2>
                <button type="button" onclick="toggleAreaModal(false)" style="background: transparent; border: none; color: #8fa4c7; cursor: pointer;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" style="width:24px;"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg></button>
            </div>

            <form action="{{ route('admin.areas.store') }}" method="POST">
                @csrf
                <label style="display: block; margin-bottom: 25px;">
                    <span style="color: #fff; display: block; margin-bottom: 8px;">Nome da área (Máx. 30 caracteres)</span>
                    <input type="text" name="nome_area" maxlength="30" required style="width: 100%; height: 45px; border-radius: 6px; background: rgba(9, 19, 34, 0.8); border: 1px solid rgba(141, 165, 203, 0.4); color: white; padding: 0 15px; outline: none;">
                </label>

                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" onclick="toggleAreaModal(false)" style="padding: 10px 20px; background: transparent; color: white; border: 1px solid #8fa4c7; border-radius: 6px; cursor: pointer;">Cancelar</button>
                    <button type="submit" style="padding: 10px 20px; background: #38d2f8; color: #000; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">Salvar Área</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Função do Modal de Criar Área (Que já funcionava)
        function toggleAreaModal(show) {
            const modal = document.getElementById('area-modal');
            modal.style.display = show ? 'flex' : 'none';
            modal.setAttribute('aria-hidden', !show);
            if (show) {
                modal.querySelector('input').focus();
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Lógica de Deletar Área
            const deleteBtn = document.getElementById('delete-theme-btn');
            const selectTheme = document.getElementById('question-theme-filter');

            if (selectTheme && deleteBtn) {
                selectTheme.addEventListener('change', () => {
                    deleteBtn.disabled = selectTheme.value === "";
                });

                deleteBtn.addEventListener('click', () => {
                    const areaNome = selectTheme.options[selectTheme.selectedIndex].text;
                    if (confirm(`Deseja realmente tentar excluir a área "${areaNome.trim()}"?`)) {
                        document.getElementById('hidden-delete-area-id').value = selectTheme.value;
                        document.getElementById('form-delete-area').submit();
                    }
                });
            }

            // Lógica do Tooltip flutuante usando base.css
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            document.body.appendChild(tooltip);

            document.addEventListener('mouseover', (e) => {
                const target = e.target.closest('[data-texto-tooltip]');
                if (target) {
                    tooltip.textContent = target.getAttribute('data-texto-tooltip');
                    tooltip.classList.add('active');
                }
            });

            document.addEventListener('mousemove', (e) => {
                if (tooltip.classList.contains('active')) {
                    tooltip.style.left = (e.pageX + 15) + 'px';
                    tooltip.style.top = (e.pageY + 15) + 'px';
                }
            });

            document.addEventListener('mouseout', (e) => {
                const target = e.target.closest('[data-texto-tooltip]');
                if (target) {
                    tooltip.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>
