<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Questionários - CorpWare</title>
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/user.css') }}">
    <style>
        .pagination { display: flex; align-items: center; gap: 8px; }
        .pagination p { display: none; }
        .pagination span[aria-current="page"] { display: grid; place-items: center; width: 48px; height: 50px; border: 1px solid #2e73ff; border-radius: 8px; color: #a9d2ff; background: rgba(22, 39, 67, 0.4); font-size: 16px; font-weight: bold; }
        .pagination a, .pagination span { display: grid; place-items: center; width: 40px; height: 40px; color: #8fa4c7; text-decoration: none; border-radius: 8px; font-weight: bold; }
        .pagination a:hover { background: rgba(22, 39, 67, 0.4); }
        .pagination svg { width: 19px; height: 19px; }
    </style>
</head>
<body>
    <main class="users-page">
        <header class="page-header">
            <a class="back-button" href="{{ route('admin.dashboard') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 12H5"></path><path d="m12 19-7-7 7-7"></path></svg> Voltar
            </a>
            <div class="page-title">
                <h1>Editar Questionários</h1>
                <p>Crie e gerencie os questionários e listas de avaliação da plataforma.</p>
            </div>
            <a class="create-button" href="{{ route('admin.listas.create') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg> Criar Questionário
            </a>
        </header>

        @if (session('success'))
            <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid #10b981; color: #6ee7b7; padding: 15px 20px; border-radius: 8px; margin-bottom: 24px;">{{ session('success') }}</div>
        @endif

        <form action="{{ route('admin.listas.index') }}" method="GET">
            <section class="toolbar">
                <label class="search-field">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path></svg>
                    <input type="number" name="search" value="{{ $busca ?? '' }}" placeholder="Buscar pelo ID do questionário..." onchange="this.form.submit()">
                </label>
            </section>
        </form>

        <section class="users-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID (Lista)</th>
                        <th>Data de Início</th>
                        <th>Data de Término</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($listas as $lista)
                        <tr>
                            <td><strong>#{{ $lista->id_lista }}</strong></td>
                            <td>{{ \Carbon\Carbon::parse($lista->inicio)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($lista->fim)->format('d/m/Y') }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('admin.listas.edit', $lista->id_lista) }}" class="icon-button edit-ranking"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m16 4 4 4L8 20H4v-4L16 4Z"></path><path d="m14 6 4 4"></path></svg></a>
                                    <form action="{{ route('admin.listas.destroy', $lista->id_lista) }}" method="POST" style="display: inline;" onsubmit="return confirm('Excluir este questionário? Todas as perguntas dele serão perdidas!');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="icon-button delete-ranking" style="border: none; cursor: pointer;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 6h18"></path><path d="M8 6V4h8v2"></path><path d="M19 6 18 20H6L5 6"></path><path d="M10 11v5"></path><path d="M14 11v5"></path></svg></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align: center; color: var(--muted); height: 120px;">Nenhum questionário encontrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <footer class="table-footer">
                <span>Mostrando {{ $listas->firstItem() ?? 0 }} a {{ $listas->lastItem() ?? 0 }} de {{ $listas->total() }} listas</span>

                @if ($listas->hasPages())
                    <nav class="pagination">
                        @if ($listas->onFirstPage())
                            <span class="page-arrow" style="opacity: 0.3; cursor: not-allowed;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m15 18-6-6 6-6"></path></svg></span>
                        @else
                            <a href="{{ $listas->previousPageUrl() }}" class="page-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m15 18-6-6 6-6"></path></svg></a>
                        @endif

                        <span class="page-number" aria-current="page">{{ $listas->currentPage() }}</span>

                        @if ($listas->hasMorePages())
                            <a href="{{ $listas->nextPageUrl() }}" class="page-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m9 18 6-6-6-6"></path></svg></a>
                        @else
                            <span class="page-arrow" style="opacity: 0.3; cursor: not-allowed;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m9 18 6-6-6-6"></path></svg></span>
                        @endif
                    </nav>
                @endif
            </footer>
        </section>
    </main>
</body>
</html>
