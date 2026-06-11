<?php

namespace App\Http\Controllers;

use App\Models\Funcionario;
use App\Models\Ranking;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Pega todos os funcionários, ordenados pela maior pontuação (desc)
        $funcionarios = Funcionario::orderBy('pontos', 'desc')->get();

        // Pega o usuário logado atualmente
        $usuarioLogado = auth()->user();

        // Encontra a posição do usuário logado na coleção (soma 1 porque o array no índice 0)
        $posicaoLogado = $funcionarios->search(function ($user) use ($usuarioLogado) {
            return $user->id_funcionario === $usuarioLogado->id_funcionario;
        }) + 1;

        // Pega o primeiro da lista para o card de "Maior Pontuação"
        $maiorPontuacao = $funcionarios->first()->pontos ?? 0;

        return view('ranking', compact('funcionarios', 'posicaoLogado', 'maiorPontuacao'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Ranking $ranking)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ranking $ranking)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ranking $ranking)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ranking $ranking)
    {
        //
    }
}
