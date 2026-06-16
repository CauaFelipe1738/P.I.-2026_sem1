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
            <article class="list-row {{ ($funcionario->id_funcionario === auth()->id()) ? 'me' : '' }}">
                <span>#{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>

                <div style="display: flex; flex-direction: column; min-width: 0;" data-tooltip="{{ $funcionario->nome_funcionario }} ({{ '@' . $funcionario->username }})">
                    <strong>{{ \Illuminate\Support\Str::limit($funcionario->nome_funcionario, 22) }}</strong>
                    <small>{{ \Illuminate\Support\Str::limit('@' . $funcionario->username, 25) }}</small>
                </div>

                <em data-tooltip="{{ $funcionario->titulo ?? 'Recruta' }} - {{ $funcionario->sobre ?? 'Sem descrição.' }}">
                    {{ \Illuminate\Support\Str::limit($funcionario->titulo ?? 'Recruta', 22) }}
                </em>

                <b data-tooltip="{{ number_format($funcionario->pontos, 0, ',', '.') }} Pontos de Experiência">
                    {{ number_format($funcionario->pontos, 0, ',', '.') }} XP
                </b>
            </article>
        @endforeach
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/pages/ranking.js') }}"></script>
@endsection
