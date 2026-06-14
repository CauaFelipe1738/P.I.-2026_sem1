<?php

namespace App\Http\Controllers;

use App\Models\Lista;
use App\Models\Pergunta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
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
        $idFuncionario = Auth::id();

        // Conta quantas perguntas a lista tem no total
        $totalPerguntas = DB::table('pergunta_lista')->where('idf_lista', $id_lista)->count();

        // Se a lista não tem perguntas cadastradas ainda
        if ($totalPerguntas === 0) {
            return redirect()->route('dashboard')->with('error', 'Este módulo ainda não possui questões cadastradas. Volte mais tarde!');
        }

        // Conta quantas o usuário logado já respondeu nesta lista específica
        $totalRespondidas = DB::table('funcionario_pergunta_lista')
            ->join('pergunta_lista', 'funcionario_pergunta_lista.idf_pergunta_lista', '=', 'pergunta_lista.id_pergunta_lista')
            ->where('pergunta_lista.idf_lista', $id_lista)
            ->where('funcionario_pergunta_lista.idf_funcionario', $idFuncionario)
            ->count();

        // Se o usuário já respondeu tudo
        if ($totalRespondidas >= $totalPerguntas && $totalPerguntas > 0) {
            return redirect()->route('dashboard')->with('error', 'Você já concluiu este treinamento obrigatório!');
        }

        // Conta quantas o usuário logado já respondeu nesta lista específica
        $totalRespondidas = DB::table('funcionario_pergunta_lista')
            ->join('pergunta_lista', 'funcionario_pergunta_lista.idf_pergunta_lista', '=', 'pergunta_lista.id_pergunta_lista')
            ->where('pergunta_lista.idf_lista', $id_lista)
            ->where('funcionario_pergunta_lista.idf_funcionario', $idFuncionario)
            ->count();

        // Se ele já respondeu tudo, barra o acesso e manda de volta ao Dashboard
        if ($totalRespondidas >= $totalPerguntas && $totalPerguntas > 0) {
            return redirect()->route('dashboard')->with('error', 'Você já concluiu este treinamento obrigatório!');
        }

        // Se estiver liberado, carrega o quiz normalmente
        $lista = Lista::with('perguntas.respostas')->findOrFail($id_lista);
        $perguntasJson = $lista->perguntas->toJson();

        return view('quiz.show', compact('lista', 'perguntasJson'));
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

        // Busca o ID direto da tabela pivô via SQL puro.
        // Isso garante que o ID real vá para o parâmetro 'y' da procedure, ativando a trigger de pontos
        $perguntaLista = DB::table('pergunta_lista')
            ->where('idf_lista', $id_lista)
            ->where('idf_pergunta', $id_pergunta)
            ->first();

        if (!$perguntaLista) {
            return response()->json(['error' => 'Vínculo da pergunta não encontrado.'], 400);
        }

        // Executa a procedure "responder"
        DB::statement('CALL responder(?, ?, ?)', [
            Auth::id(),
            $perguntaLista->id_pergunta_lista,
            $request->id_resposta
        ]);

        if ($request->expectsJson()) {
            // Força o Laravel a reler o usuário direto do banco para pegar os pontos novos computados pelo MySQL
            $funcionarioAtualizado = Auth::user()->fresh();

            return response()->json([
                'status' => 'success',
                'pontos_totais' => $funcionarioAtualizado->pontos
            ]);
        }

        return redirect()->back()->with('success', 'Resposta salva!');
    }
}
