<?php

namespace App\Http\Controllers;
use App\Models\Lista;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $funcionario = Auth::user();

        // Futuramente, terá queries para separar listas disponíveis (que estão dentro do prazo de início/fim) e listas em andamento/concluídas pelo usuário

        // Se a requisição pedir JSON
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'funcionario' => $funcionario,
                    // 'listas' => $listas
                ]
            ], 200);
        }

        // Se for um navegador normal, retorna a tela do Blade
        return view('dashboard', compact('funcionario'));
    }
}
