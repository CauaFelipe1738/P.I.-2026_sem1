<?php

use App\Http\Controllers\Admin\AdminFuncionarioController;
use App\Http\Controllers\Admin\AdminListaController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ListaController;
use App\Http\Controllers\RankingController;
use Illuminate\Support\Facades\Route;

// Rotas para todos os funcionários logados
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/ranking', [RankingController::class, 'index'])->name('ranking');
    Route::get('/quiz/{id_lista}', [ListaController::class, 'show'])->name('quiz.show');
    Route::get('/ranking', [RankingController::class, 'index'])->name('ranking');
});

// Rotas exclusivas para administradores
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
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
