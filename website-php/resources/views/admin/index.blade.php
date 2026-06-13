@extends('layouts.app')

@section('title', 'Painel Administrador - CorpWare')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/admin.css') }}">
@endsection

@section('content')
    <div class="admin-page">
        <section class="admin-area" aria-labelledby="admin-title">
            <div class="admin-heading">
                <div>
                    <span class="admin-kicker">Painel restrito</span>
                    <h3 id="admin-title">Administrador</h3>
                </div>
            </div>

            <div class="admin-metrics">
                <div class="admin-metric purple">
                    <span>Desempenho médio</span>
                    <strong>{{ number_format($mediaPontos, 0, ',', '.') }}</strong>
                    <small>pontos por colaborador</small>
                </div>
                <div class="admin-metric blue">
                    <span>Contas criadas</span>
                    <strong>{{ number_format($totalContas, 0, ',', '.') }}</strong>
                </div>
            </div>
        </section>

        <section class="quick-actions" aria-label="Ações rápidas">
            <h3>Ações rápidas</h3>

            <a class="action-card" href="{{ route('admin.usuarios.index') }}">
                <span class="action-icon create-user" aria-hidden="true">
                    <img src="{{ asset('img/admin/editar.png') }}" alt="" class="icon">
                </span>
                <span class="action-copy">
                    <strong>Criar e Editar Novo Usuário</strong>
                    <small>Adicione novos colaboradores à plataforma com permissões personalizadas.</small>
                </span>
                <span class="action-arrow" aria-hidden="true">›</span>
            </a>

            <a class="action-card" href="#">
                <span class="action-icon perguntas" aria-hidden="true">
                    <img src="{{ asset('img/admin/perguntas-frequentes.png') }}" alt="" class="icon">
                </span>
                <span class="action-copy">
                    <strong>Criar e Editar Perguntas</strong>
                    <small>Crie as perguntas usadas para avaliações e pesquisas.</small>
                </span>
                <span class="action-arrow" aria-hidden="true">›</span>
            </a>

            <a class="action-card" href="#">
                <span class="action-icon rankingg" aria-hidden="true">
                    <img src="{{ asset('img/admin/ranking-da-pagina.png') }}" alt="" class="icon">
                </span>
                <span class="action-copy">
                    <strong>Criar e Editar Ranking</strong>
                    <small>Crie rankings para aumentar a competitividade entre a equipe.</small>
                </span>
                <span class="action-arrow" aria-hidden="true">›</span>
            </a>

            <a class="action-card" href="#">
                <span class="action-icon questionnaire" aria-hidden="true">
                    <img src="{{ asset('img/admin/tabela-de-edicao.png') }}" alt="" class="icon">
                </span>
                <span class="action-copy">
                    <strong>Criar e Editar Questionário</strong>
                    <small>Crie e personalize questionários para avaliações e pesquisas.</small>
                </span>
                <span class="action-arrow" aria-hidden="true">›</span>
            </a>
        </section>
    </div>
@endsection

