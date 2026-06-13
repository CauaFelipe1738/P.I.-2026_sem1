@extends('layouts.app')

@section('title', 'Dashboard - CorpWare')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/pages/dashboard.css') }}">
@endsection

@section('content')
<div class="hero">
    <div>
    <h2>Dashboard</h2>
    <p class="subtitle">
        Acompanhe o progresso dos treinamentos obrigatórios, as pendências por área e a evolução da equipe.
    </p>
    </div>

    <div class="stats">
    <article>
        <span>Pontos obtidos</span>
        <strong>{{ auth()->user()->pontos }}</strong>
    </article>
    </div>
</div>

<div class="linha-separadora"></div>

<h1 class="quest-title">QUESTS DISPONÍVEIS</h1>

<div class="section-grid" id="treinamentosGrid">

    @forelse($listas as $lista)
        <article class="panel">

        @if($lista->perguntas == 0)
            <span class="status" style="background-color: #6c757d; color: white;">EM BREVE</span>
        @elseif($lista->respostas >= $lista->perguntas && $lista->perguntas > 0)
            <span class="status" style="background-color: #2ec4b6; color: white;">CONCLUÍDO</span>
        @elseif($lista->respostas > 0)
            <span class="status" style="background-color: #ff9f1c; color: white;">EM ANDAMENTO</span>
        @else
            <span class="status status-nao-iniciado">NÃO INICIADO</span>
        @endif

        <p class="questao" style="font-weight: bold; font-size: 1.2rem; margin-top: 10px;">Lista #{{ $lista->id_lista }}</p>
        <p>
            Disponível de: {{ \Carbon\Carbon::parse($lista->inicio)->format('d/m/Y') }} <br>
            Até: {{ \Carbon\Carbon::parse($lista->fim)->format('d/m/Y') }}
        </p>

        @if($lista->perguntas == 0)
            <button class="btn-iniciar" style="background-color: #6c757d; cursor: not-allowed;" disabled>
                SEM QUESTÕES
            </button>
        @elseif($lista->respostas >= $lista->perguntas && $lista->perguntas > 0)
            <button class="btn-iniciar" style="background-color: #a3a3a3; cursor: not-allowed;" disabled>
                CONCLUÍDO
            </button>
        @else
            <a href="{{ route('quiz.show', $lista->id_lista) }}">
                <button class="btn-iniciar">
                    {{ $lista->respostas > 0 ? 'CONTINUAR' : 'INICIAR AGORA' }}
                </button>
            </a>
        @endif

        <div class="container-barra">
            @php
                $percentual = $lista->perguntas > 0 ? ($lista->respostas / $lista->perguntas) * 100 : 0;
            @endphp
            <div class="barra-progresso" style="width: {{ $percentual }}%;"></div>
        </div>

    </article>
    @empty
    <p style="color: var(--muted); grid-column: 1/-1;">Nenhum treinamento obrigatório disponível no momento.</p>
    @endforelse

</div>
@endsection
