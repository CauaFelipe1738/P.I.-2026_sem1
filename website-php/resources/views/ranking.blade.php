@extends('layouts.app')

@section('title', 'Ranking - CorpWare')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/ranking.css') }}">
@endsection

@section('content')
    <div class="hero">
        <div>
            <h2>Ranking por Colaborador</h2>
            <p class="subtitle">
                Visualize pontuação, ritmo de conclusão e engajamento nos módulos obrigatórios.
            </p>
        </div>

        <div class="stats">
            <article>
                <span>MAIOR PONTUAÇÃO</span>
                <strong>{{ number_format($maiorPontuacao, 0, ',', '.') }} XP</strong>
            </article>
            <article>
                <span>SUA POSIÇÃO</span>
                <strong>#{{ str_pad($posicaoLogado, 2, '0', STR_PAD_LEFT) }}</strong>
            </article>
        </div>
    </div>

    <div class="list">
        @foreach($funcionarios as $index => $funcionario)

            @php
                $posicao = $index + 1;
                // Define a classe "me" se for a linha do usuário logado
                $isMe = ($funcionario->id_funcionario === auth()->id()) ? 'me' : '';

                // Lógica simples de títulos baseada em pontos (temporária até integrar com o banco de dados)
                if ($funcionario->pontos >= 18000) {
                    $titulo = "Poseidon Supremo";
                    $tooltip = "Ganhou esse título por atingir 18k+ XP e alta performance.";
                } elseif ($funcionario->pontos >= 15000) {
                    $titulo = "Príncipe dos Mares";
                    $tooltip = "Título baseado em consistência e 15k+ XP acumulados.";
                } elseif ($funcionario->pontos >= 10000) {
                    $titulo = "Lutador";
                    $tooltip = "Reconhecimento por alta taxa de acerto nos módulos.";
                } else {
                    $titulo = "Iniciante";
                    $tooltip = "Evolução consistente. Continue completando módulos!";
                }
            @endphp

            <article class="list-row {{ $isMe }}">
                <span>#{{ str_pad($posicao, 2, '0', STR_PAD_LEFT) }}</span>

                <div style="display: flex; flex-direction: column;">
                    <strong>{{ $funcionario->nome_funcionario }}</strong>
                    <small style="color: var(--muted); font-size: 12px; margin-top: 2px;">{{ '@' . $funcionario->username }}</small>
                </div>

                <em data-tooltip="{{ $tooltip }}">{{ $titulo }}</em>
                <b>{{ number_format($funcionario->pontos, 0, ',', '.') }} XP</b>
            </article>
        @endforeach
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/pages/ranking.js') }}"></script>
@endsection
