<?php

use App\Http\Controllers\FuncionarioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Rota para criar usuário via POST enviando JSON
Route::post('/usuarios', [FuncionarioController::class, 'storeAPI']);
