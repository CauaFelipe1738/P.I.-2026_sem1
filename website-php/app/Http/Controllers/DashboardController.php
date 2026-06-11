<?php

namespace App\Http\Controllers;
use App\Models\Lista;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Pega todas as listas cadastradas no banco de dados
        $listas = Lista::all();

        // Se a requisição pedir JSON
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'funcionario' => auth()->user(),
                    'listas' => $listas,
                ]
            ], 200);
        }

        // Se for um navegador normal, retorna a tela do Blade
        return view('dashboard', compact('listas'));
    }
}
