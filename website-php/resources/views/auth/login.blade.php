<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Projeto Gameficação</title>
  <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
</head>
<body>
  <div class="container">
    <div class="left">
      <div class="left-content">
        <p class="kicker">PROTOTIPO GAMIFICAÇÃO</p>
        <h1>DESBLOQUEIE<br><span>SEU<br>POTENCIAL</span></h1>
        <p class="description">
          Acesse a fronteira do conhecimento corporativo através de nossa interface de alta performance. Nível 42 aguarda sua ignição.
        </p>
      </div>
    </div>
    <div class="right">
      <div class="login-box">
        <h2>AUTENTICAÇÃO</h2>
        <p class="subtitle">Insira suas credenciais para acessar a central.</p>

        @if($errors->any())
            <div style="color: #ff4d4d; margin-bottom: 15px; font-size: 14px; background: rgba(255, 77, 77, 0.1); padding: 10px; border-radius: 4px;">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
          @csrf

          <label for="username">LOGIN</label>
          <div class="field">
            <input id="username" name="username" type="text" placeholder="Seu usuário" value="{{ old('username') }}" required>
          </div>

          <div class="password-head">
            <label for="senha">CHAVE DE ACESSO</label>
            </div>
          <div class="field">
            <input id="senha" name="senha" type="password" placeholder="••••••••••••" required>
          </div>

          <button type="submit" class="btn" style="width: 100%; border: none; cursor: pointer; display: block; text-align: center;">ACESSAR SISTEMA →</button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
