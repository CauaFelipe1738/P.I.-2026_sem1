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
                $titulo = $funcionario->pontos >= 18000 ? 'Poseidon' : ($funcionario->pontos >= 15000 ? 'Príncipe' : ($funcionario->pontos >= 12000 ? 'Lutador' : 'Recruta'));
            @endphp
            <article class="list-row {{ ($funcionario->id_funcionario === auth()->id()) ? 'me' : '' }}">
                <span>#{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>

                <div style="display: flex; flex-direction: column;">
                    <strong>{{ $funcionario->nome_funcionario }}</strong>
                    <small style="color: var(--muted); font-size: 12px; margin-top: 2px;">{{ '@' . $funcionario->username }}</small>
                </div>

                <em data-tooltip="18k XP: Poseidon | 15k XP: Príncipe | 12k XP: Lutador">
                    {{ $titulo }}
                </em>
                <b>{{ number_format($funcionario->pontos, 0, ',', '.') }} XP</b>
            </article>
        @endforeach
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/pages/ranking.js') }}"></script>
@endsection
