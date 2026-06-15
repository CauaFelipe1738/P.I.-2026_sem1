<?php

use App\Http\Controllers\Admin\AdminFuncionarioController;
use App\Http\Controllers\Admin\AdminListaController;
use App\Http\Controllers\Admin\AdminPerguntaController;
use App\Http\Controllers\Admin\AdminRankingController;
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
    // Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');

    Route::get('/usuarios', [FuncionarioController::class, 'index'])->name('usuarios.index');
    Route::get('/usuarios/criar', [FuncionarioController::class, 'create'])->name('usuarios.create');
    Route::post('/usuarios/salvar', [FuncionarioController::class, 'store'])->name('usuarios.store');

    Route::get('/usuarios/{id}/editar', [FuncionarioController::class, 'edit'])->name('usuarios.edit');
    Route::put('/usuarios/{id}', [FuncionarioController::class, 'update'])->name('usuarios.update');
    Route::delete('/usuarios/{id}', [FuncionarioController::class, 'destroy'])->name('usuarios.destroy');

    Route::get('/rankings', [AdminRankingController::class, 'index'])->name('rankings.index');
    Route::get('/rankings/criar', [AdminRankingController::class, 'create'])->name('rankings.create');
    Route::post('/rankings/salvar', [AdminRankingController::class, 'store'])->name('rankings.store');
    Route::get('/rankings/{id}/editar', [AdminRankingController::class, 'edit'])->name('rankings.edit');
    Route::put('/rankings/{id}', [AdminRankingController::class, 'update'])->name('rankings.update');
    Route::delete('/rankings/{id}', [AdminRankingController::class, 'destroy'])->name('rankings.destroy');


    Route::get('/questionarios', [AdminListaController::class, 'index'])->name('listas.index');
    Route::get('/questionarios/criar', [AdminListaController::class, 'create'])->name('listas.create');
    Route::post('/questionarios/salvar', [AdminListaController::class, 'store'])->name('listas.store');
    Route::get('/questionarios/{id}/editar', [AdminListaController::class, 'edit'])->name('listas.edit');
    Route::put('/questionarios/{id}', [AdminListaController::class, 'update'])->name('listas.update');
    Route::delete('/questionarios/{id}', [AdminListaController::class, 'destroy'])->name('listas.destroy');

    Route::get('/perguntas', [AdminPerguntaController::class, 'index'])->name('perguntas.index');
    Route::post('/areas', [AdminPerguntaController::class, 'storeArea'])->name('areas.store');
    Route::delete('/perguntas/{id}', [AdminPerguntaController::class, 'destroy'])->name('perguntas.destroy');
    Route::get('/perguntas/criar', [AdminPerguntaController::class, 'create'])->name('perguntas.create');
    Route::post('/perguntas/salvar', [AdminPerguntaController::class, 'store'])->name('perguntas.store');
    Route::get('/perguntas/{id}/editar', [AdminPerguntaController::class, 'edit'])->name('perguntas.edit');
    Route::put('/perguntas/{id}', [AdminPerguntaController::class, 'update'])->name('perguntas.update');
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
