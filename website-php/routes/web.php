<?php

use App\Http\Controllers\Admin\AdminFuncionarioController;
use App\Http\Controllers\Admin\AdminListaController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\RankingController;
use Illuminate\Support\Facades\Route;

// Rotas para todos os funcionários logados
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/ranking', [RankingController::class, 'index'])->name('ranking');
    Route::get('/quiz/{id_lista}', [QuizController::class, 'show'])->name('quiz.show');
    Route::get('/ranking', [RankingController::class, 'index'])->name('ranking');
    Route::post('/quiz/{id_lista}/pergunta/{id_pergunta}/responder', [QuizController::class, 'responder'])->name('quiz.responder');
});

// Rotas exclusivas para administradores
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');

    // Rotas do CRUD de admin
    // Route::get('/admin/usuarios', [UserController::class, 'index'])->name('admin.usuarios.index');

    Route::get('/usuarios', [FuncionarioController::class, 'index'])->name('usuarios.index');
    Route::get('/usuarios/criar', [FuncionarioController::class, 'create'])->name('usuarios.create');
    Route::post('/usuarios/salvar', [FuncionarioController::class, 'store'])->name('usuarios.store');
    // Route::get('/admin/perguntas', [PerguntaController::class, 'index'])->name('perguntas.index');
    // Route::get('/admin/ranking', [RankingController::class, 'adminIndex'])->name('ranking.index');
    // Route::get('/admin/listas', [ListaController::class, 'index'])->name('listas.index');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Rotas de gerenciamento de funcionários
    Route::resource('funcionarios', AdminFuncionarioController::class);

    // Rotas de gerenciamento de questionários
    Route::apiResource('listas', AdminListaController::class)->names([
        'index'   => 'listas.index',
        'store'   => 'listas.store',
        'update'  => 'listas.update',
        'destroy' => 'listas.destroy',
    ]);
});

// Rota que exibe o formulário de login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

// Rota que processa os dados do formulário quando o usuário clicar em "Entrar"
Route::post('/login', [AuthController::class, 'login']);

// Rota sair do sistema
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
