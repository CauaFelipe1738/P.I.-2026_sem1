<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Novo Usuário</title>
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/create_user.css') }}">
</head>

<body>
    <main class="create-user-page">
        <header class="page-header">
            <div class="page-title">
                <h1>Criar Novo Usuário</h1>
                <p>Preencha os dados abaixo para adicionar um novo usuário à plataforma.</p>
            </div>

            <a class="back-button" href="{{ route('admin.funcionarios.index') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                    <path d="M19 12H5"></path>
                    <path d="m12 19-7-7 7-7"></path>
                </svg>
                Voltar para usuários
            </a>
        </header>

        @if(session('success'))
            <div class="alert alert-success" style="color: green; margin-bottom: 15px;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger" style="color: red; margin-bottom: 15px;">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="create-user-form" action="{{ route('admin.funcionarios.store') }}" method="POST">
            @csrf

            <section class="form-grid">
                <div class="form-card personal-card">
                    <div class="card-heading">
                        <span class="heading-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </span>
                        <h2>Informações Pessoais</h2>
                    </div>

                    <label class="field">
                        <span>Nome completo</span>
                        <input type="text" name="nome_funcionario" placeholder="Digite o nome completo" value="{{ old('nome_funcionario') }}" required>
                    </label>

                    <label class="field">
                        <span>Nome de Usuário (Username)</span>
                        <input type="text" name="username" placeholder="Digite o username para login" value="{{ old('username') }}" required>
                    </label>

                    <label class="field password-field">
                        <span>Senha</span>
                        <span class="password-control">
                            <input type="password" name="senha" placeholder="Digite a senha" minlength="6" required>
                            <button type="button" aria-label="Mostrar senha" id="togglePassword">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                    <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </span>
                        <small>Mínimo de 6 caracteres.</small>
                    </label>
                </div>

                <div class="form-card access-card">
                    <div class="card-heading">
                        <span class="heading-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"></path>
                                <path d="M12 8v4"></path>
                                <path d="M12 16h.01"></path>
                            </svg>
                        </span>
                        <h2>Acesso e Permissões</h2>
                    </div>

                    <fieldset class="access-options">
                        <legend>Tipo de acesso</legend>

                        <label class="radio-option">
                            <input type="radio" name="admin" value="1" {{ old('admin') == '1' ? 'checked' : '' }}>
                            <span class="radio-mark"></span>
                            <span class="radio-copy">
                                <strong>Administrador</strong>
                                <small>Acesso total à plataforma e todas as permissões.</small>
                            </span>
                        </label>

                        <label class="radio-option">
                            <input type="radio" name="admin" value="0" {{ old('admin', '0') == '0' ? 'checked' : '' }}>
                            <span class="radio-mark"></span>
                            <span class="radio-copy">
                                <strong>Usuário</strong>
                                <small>Acesso limitado conforme permissões definidas.</small>
                            </span>
                        </label>
                    </fieldset>
                </div>
            </section>

            <footer class="form-actions">
                <a class="cancel-button" href="{{ route('admin.funcionarios.index') }}">Cancelar</a>
                <button class="save-button" type="submit">
                    Salvar Usuário
                </button>
            </footer>
        </form>
    </main>

    <script>
        const passwordInput = document.querySelector('input[name="senha"]');
        const passwordToggle = document.getElementById("togglePassword");

        if(passwordToggle && passwordInput) {
            passwordToggle.addEventListener("click", () => {
                passwordInput.type = passwordInput.type === "password" ? "text" : "password";
            });
        }
    </script>
</body>
</html>
