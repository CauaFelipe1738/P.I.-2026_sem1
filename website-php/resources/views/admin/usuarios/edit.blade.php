<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário - CorpWare</title>
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/create_user.css') }}">
</head>
<body>
    <main class="create-user-page">
        <header class="page-header">
            <div class="page-title">
                <h1>Editar Usuário</h1>
                <p>Atualize as informações, permissões e acessos do usuário selecionado.</p>
            </div>

            <a class="back-button" href="{{ route('admin.usuarios.index') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M19 12H5"></path><path d="m12 19-7-7 7-7"></path></svg>
                Voltar para usuários
            </a>
        </header>

        @if ($errors->any())
            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #fca5a5; padding: 15px 20px; border-radius: 8px; margin: 0 5px 24px;">
                <ul style="margin: 0; padding-left: 20px; font-size: 14px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="create-user-form" action="{{ route('admin.usuarios.update', $usuario->id_funcionario) }}" method="POST">
            @csrf
            @method('PUT')

            <section class="form-grid">
                <div class="form-card personal-card">
                    <div class="card-heading">
                        <span class="heading-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        </span>
                        <h2>Informações Pessoais</h2>
                    </div>

                    <label class="field">
                        <span>Nome completo</span>
                        <input type="text" name="nome" value="{{ old('nome', $usuario->nome_funcionario) }}" required>
                    </label>

                    <label class="field">
                        <span>Username</span>
                        <input type="text" name="username" value="{{ old('username', $usuario->username) }}" required>
                    </label>

                    <label class="field password-field">
                        <span>Nova Senha</span>
                        <span class="password-control">
                            <input type="password" name="senha" placeholder="Deixe em branco para não alterar" minlength="6">
                            <button type="button" aria-label="Mostrar senha" id="toggle-password">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            </button>
                        </span>
                        <small>Mínimo de 6 caracteres. Só preencha se quiser mudar.</small>
                    </label>
                </div>

                <div class="form-card access-card">
                    <div class="card-heading">
                        <span class="heading-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"></path><path d="M12 8v4"></path><path d="M12 16h.01"></path></svg>
                        </span>
                        <h2>Acesso e Permissões</h2>
                    </div>

                    <fieldset class="access-options">
                        <legend>Tipo de acesso</legend>

                        <label class="radio-option">
                            <input type="radio" name="tipo_acesso" value="administrador" {{ old('tipo_acesso', $usuario->admin ? 'administrador' : 'usuario') == 'administrador' ? 'checked' : '' }}>
                            <span class="radio-mark"></span>
                            <span class="radio-copy">
                                <strong>Administrador</strong>
                                <small>Acesso total à plataforma e todas as permissões.</small>
                            </span>
                        </label>

                        <label class="radio-option">
                            <input type="radio" name="tipo_acesso" value="usuario" {{ old('tipo_acesso', $usuario->admin ? 'administrador' : 'usuario') == 'usuario' ? 'checked' : '' }}>
                            <span class="radio-mark"></span>
                            <span class="radio-copy">
                                <strong>Usuário</strong>
                                <small>Acesso limitado conforme permissões definidas.</small>
                            </span>
                        </label>
                    </fieldset>
                </div>

                <aside class="form-card info-card">
                    <span class="heading-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path></svg>
                    </span>
                    <h2>Sobre os tipos de acesso</h2>
                    <p>Administradores podem gerenciar usuários, criar questionários e visualizar relatórios completos.</p>
                    <p>Usuários têm acesso limitado às funcionalidades permitidas para sua função.</p>
                </aside>
            </section>

            <footer class="form-actions">
                <a class="cancel-button" href="{{ route('admin.usuarios.index') }}">Cancelar</a>
                <button class="save-button" type="submit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M19 8v6"></path><path d="M22 11h-6"></path></svg>
                    Salvar Alterações
                </button>
            </footer>
        </form>
    </main>

    <script>
        const passwordInput = document.querySelector('input[name="senha"]');
        document.querySelector("#toggle-password").addEventListener("click", () => {
            passwordInput.type = passwordInput.type === "password" ? "text" : "password";
        });
    </script>
</body>
</html>
