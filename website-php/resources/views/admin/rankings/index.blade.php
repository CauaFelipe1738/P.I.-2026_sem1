<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Rankings - CorpWare</title>
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/user.css') }}">
    <style>
        .pagination { display: flex; align-items: center; gap: 8px; }
        .pagination p { display: none; }
        .pagination span[aria-current="page"] { display: grid; place-items: center; width: 48px; height: 50px; border: 1px solid #2e73ff; border-radius: 8px; color: #a9d2ff; background: rgba(22, 39, 67, 0.4); font-size: 16px; font-weight: bold; }
        .pagination a, .pagination span { display: grid; place-items: center; width: 40px; height: 40px; color: #8fa4c7; text-decoration: none; border-radius: 8px; font-weight: bold; }
        .pagination a:hover { background: rgba(22, 39, 67, 0.4); }
        .pagination svg { width: 19px; height: 19px; }
        .w-5 { width: 1.25rem; } .h-5 { height: 1.25rem; }
    </style>
</head>
<body>
    <main class="users-page">
        <header class="page-header">
            <a class="back-button" href="{{ route('admin.dashboard') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 12H5"></path><path d="m12 19-7-7 7-7"></path></svg> Voltar
            </a>
            <div class="page-title">
                <h1>Editar Rankings</h1>
                <p>Gerencie as tabelas de classificação e competições da plataforma.</p>
            </div>
            <a class="create-button" href="{{ route('admin.rankings.create') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg> Criar Novo Ranking
            </a>
        </header>

        <form action="{{ route('admin.rankings.index') }}" method="GET">
            <section class="toolbar">
                <label class="search-field">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path></svg>
                    <input type="search" name="search" value="{{ $busca ?? '' }}" placeholder="Buscar por título ou descrição..." onchange="this.form.submit()">
                </label>
            </section>
        </form>

        <section class="users-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Qtd. Pessoas</th>
                        <th>Sobre</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rankings as $ranking)
                        <tr>
                            <td><strong>{{ $ranking->titulo }}</strong></td>
                            <td>{{ $ranking->qtd_pessoas }}</td>

                            <td>
                                <span data-tooltip="{{ $ranking->sobre }}" style="cursor: pointer;">
                                    {{ \Illuminate\Support\Str::limit($ranking->sobre, 50, '...') }}
                                </span>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('admin.rankings.edit', $ranking->id_ranking) }}" class="icon-button edit-ranking"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m16 4 4 4L8 20H4v-4L16 4Z"></path><path d="m14 6 4 4"></path></svg></a>
                                    <form action="{{ route('admin.rankings.destroy', $ranking->id_ranking) }}" method="POST" style="display: inline;" onsubmit="return confirm('Excluir ranking?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="icon-button delete-ranking" style="border: none; cursor: pointer;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 6h18"></path><path d="M8 6V4h8v2"></path><path d="M19 6 18 20H6L5 6"></path><path d="M10 11v5"></path><path d="M14 11v5"></path></svg></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align: center; color: var(--muted); height: 120px;">Nenhum ranking encontrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <footer class="table-footer">
                <span>Mostrando {{ $rankings->firstItem() ?? 0 }} a {{ $rankings->lastItem() ?? 0 }} de {{ $rankings->total() }} usuários</span>

                @if ($rankings->hasPages())
                    <nav class="pagination" aria-label="Paginação">
                        {{-- Botão Voltar (Desabilitado se estiver na primeira página) --}}
                        @if ($rankings->onFirstPage())
                            <span class="page-arrow" style="opacity: 0.3; cursor: not-allowed;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>
                            </span>
                        @else
                            <a href="{{ $rankings->previousPageUrl() }}" class="page-arrow" aria-label="Página anterior" style="text-decoration: none;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>
                            </a>
                        @endif

                        {{-- Número da Página Atual --}}
                        <span class="page-number" aria-current="page">{{ $rankings->currentPage() }}</span>

                        {{-- Botão Avançar (Desabilitado se estiver na última página) --}}
                        @if ($rankings->hasMorePages())
                            <a href="{{ $rankings->nextPageUrl() }}" class="page-arrow" aria-label="Próxima página" style="text-decoration: none;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg>
                            </a>
                        @else
                            <span class="page-arrow" style="opacity: 0.3; cursor: not-allowed;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg>
                            </span>
                        @endif
                    </nav>
                @endif
            </footer>
        </section>
    </main>

    <script src="{{ asset('js/pages/ranking.js') }}"></script>
</body>
</html>
