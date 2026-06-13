<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Funcionario;

class AdminController extends Controller
{
    public function index()
    {
        // Calcula o total de contas criadas
        $totalContas = Funcionario::count();

        // Calcula a média de pontos (Apenas de usuários comuns, admin = 0)
        $mediaPontos = Funcionario::where('admin', 0)->avg('pontos') ?? 0;

        // Retorna a view principal do painel de administração
        return view('admin.index', compact('totalContas', 'mediaPontos'));
    }

    // Futuramente, terá funções de criar/editar usuários e listas
}
