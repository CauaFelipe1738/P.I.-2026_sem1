<?php

namespace App\Http\Controllers;
use App\Models\Lista;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Chama a procedure do banco de dados passando o ID do usuário logado e a data de hoje
        $listas = DB::select('CALL fetch_listas(?, ?)', [
            auth()->id(),
            now()->format('Y-m-d')
        ]);

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
