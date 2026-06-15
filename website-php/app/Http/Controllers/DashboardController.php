<?php

namespace App\Http\Controllers;
use App\Models\Lista;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $usuarioLogado = auth()->user();
        $hoje = Carbon::today()->toDateString();

        $listasBrutas = DB::select('CALL fetch_listas(?, ?)', [
            $usuarioLogado->id_funcionario,
            $hoje
        ]);

        $listas = collect($listasBrutas)->sortByDesc(function ($lista) {
            return $lista->perguntas > 0 ? 1 : 0;
        })->values();

        $pontosObtidos = $usuarioLogado->pontos;

        return view('dashboard', compact('listas', 'pontosObtidos'));
    }
}
