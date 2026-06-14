<!DOCTYPE html>
<html lang="pt-BR">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'CorpWare')</title>
<link rel="stylesheet" href="{{ asset('css/base.css') }}">
<link rel="stylesheet" href="{{ asset('css/layout/lateral.css') }}">
@yield('styles') {{-- Espaço para a página filha injetar seu próprio CSS --}}
</head>

<body>

<div class="layout">
    <aside class="sidebar">
    <div class="brand">
        <h1>CorpWare</h1>
    </div>

    <nav>
        <ul class="menu">
        <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}">Dashboard</a>
        </li>
        <li class="{{ request()->routeIs('ranking') ? 'active' : '' }}">
            <a href="{{ route('ranking') }}">Ranking</a>
        </li>
        <li class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}">Administrador</a>
        </li>

        <li style="margin-top: 20px;">
            <form action="{{ route('logout') }}" method="POST" id="logout-form">
            @csrf
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color: #ff4d4d;">Sair</a>
            </form>
        </li>
        </ul>
    </nav>
    </aside>

    <main class="content">
        <header class="topbar">
            <div class="profile">
                <div class="profile-meta">
                <strong>{{ auth()->user()->nome_funcionario }}</strong>
                <p style="text-transform: uppercase;">
                    @php
                    // Busca o título do usuário logado direto da view do banco
                    $tituloUsuario = \Illuminate\Support\Facades\DB::table('funcio_ranque')
                        ->where('id_funcionario', auth()->id())
                        ->value('titulo');
                    @endphp
                    {{ $tituloUsuario ?? 'RECRUTA' }}
                </p>
                </div>
                <div class="profile-auth">
                    <div class="avatar">
                        @php
                        $nomes = explode(' ', auth()->user()->nome_funcionario);
                        $iniciais = strtoupper(substr($nomes[0], 0, 1) . (isset($nomes[1]) ? substr($nomes[1], 0, 1) : ''));
                        @endphp
                        {{ $iniciais }}
                    </div>
                </div>
            </div>
        </header>

    @yield('content')

    </main>
</div>

@yield('scripts')
</body>
</html>
