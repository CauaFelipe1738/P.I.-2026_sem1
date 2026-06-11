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

        <span class="status status-nao-iniciado">
          DISPONÍVEL
        </span>

        <p class="questao">Lista #{{ $lista->id_lista }}</p>

        <h3>Treinamento Obrigatório</h3>
        <p>
          Disponível de: {{ \Carbon\Carbon::parse($lista->inicio)->format('d/m/Y') }} <br>
          Até: {{ \Carbon\Carbon::parse($lista->fim)->format('d/m/Y') }}
        </p>

        <a href="{{ route('quiz.show', $lista->id_lista) }}">
          <button class="btn-iniciar">
            INICIAR AGORA
          </button>
        </a>

        <div class="container-barra">
          <div class="barra-progresso" style="width: 0%;"></div>
        </div>

      </article>
    @empty
      <p style="color: var(--muted); grid-column: 1/-1;">Nenhum treinamento obrigatório disponível no momento.</p>
    @endforelse

  </div>
@endsection
