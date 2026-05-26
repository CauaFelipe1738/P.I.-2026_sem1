<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro | Projeto Gamificação</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
  <div class="container">

    <div class="left">
      <div class="left-content">
        <p class="kicker">NOVO ACESSO</p>
        <h1>CRIE SUA<br><span>CONTA</span></h1>
        <p class="description">
          Cadastre suas credenciais para iniciar sua jornada nos treinamentos corporativos.
        </p>
      </div>
    </div>

    <div class="right">
      <div class="login-box">
        <h2>CADASTRO</h2>
        <p class="subtitle">Informe seu e-mail e uma senha para criar seu acesso.</p>

        <form method="POST" action="{{ route('register') }}">
          @csrf

          {{-- E-mail --}}
          <label for="email">E-MAIL</label>
          <div class="field">
            <input
              id="email"
              name="email"
              type="email"
              placeholder="usuario@gmail.com"
              value="{{ old('email') }}"
              required
              autofocus
            >
          </div>
          @error('email')
            <p style="color:red; font-size:12px; margin-bottom:10px;">{{ $message }}</p>
          @enderror

          {{-- Senha --}}
          <label for="password">SENHA</label>
          <div class="field">
            <input
              id="password"
              name="password"
              type="password"
              placeholder="••••••••••••"
              required
            >
          </div>
          @error('password')
            <p style="color:red; font-size:12px; margin-bottom:10px;">{{ $message }}</p>
          @enderror

          {{-- Confirmar Senha --}}
          <label for="password_confirmation">CONFIRMAR SENHA</label>
          <div class="field">
            <input
              id="password_confirmation"
              name="password_confirmation"
              type="password"
              placeholder="••••••••••••"
              required
            >
          </div>

          {{-- Botão --}}
          <button type="submit" class="btn">
            CRIAR CADASTRO →
          </button>

          <p class="signup-call">
            Já possui uma conta?
            <a href="{{ route('login') }}">Voltar ao login</a>
          </p>
        </form>

      </div>
    </div>

  </div>
</body>
</html>
