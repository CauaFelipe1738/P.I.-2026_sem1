<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($lista) ? 'Editar' : 'Criar' }} Questionário</title>
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/create_user.css') }}">
    </head>

<body>
    <main class="create-user-page quest-page">
        <header class="page-header">
            <div class="page-title">
                <h1>{{ isset($lista) ? 'Editar Questionário #'.$lista->id_lista : 'Criar Questionário' }}</h1>
                <p>Defina o período de disponibilidade para as avaliações e pesquisas.</p>
            </div>

            <a class="back-button" href="{{ route('admin.listas.index') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                    <path d="M19 12H5"></path>
                    <path d="m12 19-7-7 7-7"></path>
                </svg>
                Voltar
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

        <form class="create-user-form" id="questionnaire-form"
              action="{{ isset($lista) ? route('admin.listas.update', $lista->id_lista) : route('admin.listas.store') }}"
              method="POST">
            @csrf
            @if(isset($lista))
                @method('PUT')
            @endif

            <section class="form-grid">
                <div class="form-card personal-card" style="grid-column: 1 / -1; min-height: 0;">
                    <div class="card-heading">
                        <span class="heading-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                <path d="M8 2v4"></path>
                                <path d="M16 2v4"></path>
                                <path d="M3 10h18"></path>
                                <path d="M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"></path>
                            </svg>
                        </span>
                        <h2>Período do Questionário</h2>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <label class="field">
                            <span>Data de início</span>
                            <input type="date" name="data_inicio" value="{{ old('data_inicio', $lista->inicio ?? '') }}" required>
                        </label>

                        <label class="field">
                            <span>Data de fim</span>
                            <input type="date" name="data_fim" value="{{ old('data_fim', $lista->fim ?? '') }}" required>
                        </label>
                    </div>
                </div>
            </section>

            <footer class="form-actions">
                <a class="cancel-button" href="{{ route('admin.listas.index') }}">Cancelar</a>
                <button class="save-button" type="submit" id="save-questionnaire">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z"></path>
                        <path d="M17 21v-8H7v8"></path>
                        <path d="M7 3v5h8"></path>
                    </svg>
                    Salvar Questionário
                </button>
            </footer>
        </form>
    </main>
</body>
</html>
