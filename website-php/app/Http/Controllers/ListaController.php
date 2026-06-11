<?php

namespace App\Http\Controllers;

use App\Models\FuncionarioPerguntaLista;
use App\Models\Lista;
use App\Models\Pergunta;
use App\Models\Resposta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function show($id_lista)
    {
        // Busca a lista pelo ID, com erro 404 automático se não existir
        $lista = Lista::findOrFail($id_lista);

        // Retorna a view do quiz passando os dados da lista
        return view('quiz.show', compact('lista'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lista $lista)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lista $lista)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lista $lista)
    {
        //
    }

    public function responder(Request $request, $id_lista, $id_pergunta)
    {
        $request->validate([
            'id_resposta' => 'required|integer|exists:resposta,id_resposta'
        ]);

        $funcionario = Auth::user();
        $resposta = Resposta::findOrFail($request->id_resposta);
        $pergunta = Pergunta::findOrFail($id_pergunta);

        // Acha o ID da tabela pivô "pergunta_lista" (que liga esta pergunta a esta lista)
        $perguntaLista = $pergunta->listas()->where('lista.id_lista', $id_lista)->first();

        if (!$perguntaLista) {
            return response()->json(['error' => 'Pergunta não pertence a esta lista.'], 400);
        }

        // Salva a resposta do funcionário na sua chave composta
        FuncionarioPerguntaLista::updateOrCreate(
            [
                'idf_funcionario' => $funcionario->id_funcionario,
                'idf_pergunta_lista' => $perguntaLista->pivot->id_pergunta_lista,
            ],
            [
                'idf_resposta' => $resposta->id_resposta,
            ]
        );

        // Adiciona pontos se acertou a resposta
        $acertou = $resposta->solucao;
        if ($acertou) {
            $funcionario->pontos += $pergunta->valor;
            $funcionario->save();
            // Poderia adicionar a lógica de verificar se ele subiu no ranking
        }

        // Retorno JSON
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'acertou' => $acertou,
                'pontos_ganhos' => $acertou ? $pergunta->valor : 0,
                'pontos_totais' => $funcionario->pontos
            ]);
        }

        // Se for navegação normal, redireciona para a próxima pergunta
        return redirect()->back()->with('success', 'Resposta salva!');
    }
}
