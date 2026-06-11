<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        // Retorna a view principal do painel de administração
        return view('admin.index');
    }

    // Futuramente, terá funções de criar/editar usuários e listas
}
