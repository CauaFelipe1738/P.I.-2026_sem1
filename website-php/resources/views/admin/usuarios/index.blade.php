<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuários - CorpWare</title>

    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/user.css') }}">

    <style>
        .pagination { display: flex; align-items: center; gap: 8px; }
        .pagination p { display: none; } /* Esconde texto inútil do paginador */
        .pagination span[aria-current="page"] {
            display: grid; place-items: center; width: 48px; height: 50px;
            border: 1px solid #2e73ff; border-radius: 8px; color: #a9d2ff;
            background: rgba(22, 39, 67, 0.4); font-size: 16px; font-weight: bold;
        }
        .pagination a, .pagination span {
            display: grid; place-items: center; width: 40px; height: 40px;
            color: #8fa4c7; text-decoration: none; border-radius: 8px; font-weight: bold;
        }
        .pagination a:hover { background: rgba(22, 39, 67, 0.4); }
        .pagination svg { width: 19px; height: 19px; }
        .w-5 { width: 1.25rem; } .h-5 { height: 1.25rem; }
    </style>
</head>
<body>
    <main class="users-page">
        <header class="page-header">
            <a class="back-button" href="{{ route('admin.dashboard') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                    <path d="M19 12H5"></path>
                    <path d="m12 19-7-7 7-7"></path>
                </svg>
                Voltar para Admin
            </a>

            <div class="page-title">
                <h1>Editar Usuários</h1>
                <p>Gerencie e edite informações, permissões e acessos de usuários existentes.</p>
            </div>

            <a class="create-button" href="{{ route('admin.usuarios.create') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                    <path d="M12 5v14"></path>
                    <path d="M5 12h14"></path>
                </svg>
                Criar Novo Usuário
            </a>
        </header>

        <form action="{{ route('admin.usuarios.index') }}" method="GET">
            <section class="toolbar" aria-label="Filtros de usuários">
                <label class="search-field">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-3.5-3.5"></path>
                    </svg>
                    <input type="search" name="search" value="{{ $busca ?? '' }}" placeholder="Buscar por nome ou username..." aria-label="Buscar por nome ou username" onchange="this.form.submit()">
                </label>
                @if(!empty($busca))
                    <a href="{{ route('admin.usuarios.index') }}" style="color: #18d7ee; display: flex; align-items: center; padding: 0 15px; text-decoration: none; font-weight: bold;">✕ Limpar</a>
                @endif
            </section>
        </form>

        <section class="users-table-wrap" aria-label="Lista de usuários">
            <table>
                <thead>
                    <tr>
                        <th>Usuário</th>
                        <th>Titulo</th>
                        <th>Tipo de Acesso</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="users-table-body">
                    @forelse($usuarios as $user)
                        <tr>
                            <td>
                                <div class="user-cell">
                                    <span class="avatar {{ $user->admin ? 'am' : 'cs' }}">
                                        {{ strtoupper(substr($user->nome_funcionario, 0, 2)) }}
                                    </span>
                                    {{ $user->nome_funcionario }}
                                    <small style="color: var(--muted); margin-left: 5px;">{{ '@' . $user->username }}</small>
                                </div>
                            </td>
                            <td>{{ $user->titulo ?? 'Sem cargo definido' }}</td>
                            <td>
                                <span class="badge {{ $user->admin ? 'admin' : 'user' }}">
                                    {{ $user->admin ? 'Administrador' : 'Usuário' }}
                                </span>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('admin.usuarios.edit', $user->id_funcionario) }}" class="icon-button edit-user" aria-label="Editar">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                            <path d="m16 4 4 4L8 20H4v-4L16 4Z"></path>
                                            <path d="m14 6 4 4"></path>
                                        </svg>
                                    </a>

                                    <form action="{{ route('admin.usuarios.destroy', $user->id_funcionario) }}" method="POST" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="icon-button delete-user" aria-label="Excluir" style="border: none; cursor: pointer;">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                                <path d="M3 6h18"></path>
                                                <path d="M8 6V4h8v2"></path>
                                                <path d="M19 6 18 20H6L5 6"></path>
                                                <path d="M10 11v5"></path>
                                                <path d="M14 11v5"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--muted); height: 120px;">
                                Nenhum usuário encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <footer class="table-footer">
                <span>Mostrando {{ $usuarios->firstItem() ?? 0 }} a {{ $usuarios->lastItem() ?? 0 }} de {{ $usuarios->total() }} usuários</span>
                <nav class="pagination" aria-label="Paginação">
                    {{ $usuarios->links('pagination::tailwind') }}
                </nav>
            </footer>
        </section>
    </main>
</body>
</html>
