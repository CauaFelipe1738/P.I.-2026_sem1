<?php

namespace App\Http\Controllers;

use App\Models\Lista;
use App\Models\Pergunta;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
        $idFuncionario = auth()->user()->id_funcionario;

        $lista = DB::table('lista')->where('id_lista', $id_lista)->first();
        if (!$lista) return redirect()->route('dashboard')->with('error', 'Lista não encontrada.');

        $isExpired = $lista->fim < \Carbon\Carbon::today()->toDateString();

        $perguntas = DB::table('pergunta')
            ->join('pergunta_lista', 'pergunta.id_pergunta', '=', 'pergunta_lista.idf_pergunta')
            ->where('pergunta_lista.idf_lista', $id_lista)
            ->select('pergunta.*', 'pergunta_lista.id_pergunta_lista')
            ->get();

        if ($perguntas->count() === 0) {
            return redirect()->route('dashboard')->with('error', 'Este questionário não possui questões.');
        }

        foreach ($perguntas as $p) {
            $p->respostas = DB::table('resposta')->where('idf_pergunta', $p->id_pergunta)->get();
        }

        $respostasUsuario = DB::table('funcionario_pergunta_lista')
            ->join('pergunta_lista', 'funcionario_pergunta_lista.idf_pergunta_lista', '=', 'pergunta_lista.id_pergunta_lista')
            ->where('pergunta_lista.idf_lista', $id_lista)
            ->where('funcionario_pergunta_lista.idf_funcionario', $idFuncionario)
            ->pluck('funcionario_pergunta_lista.idf_resposta', 'pergunta_lista.idf_pergunta')
            ->toArray();

        $perguntasJson = json_encode($perguntas);

        return view('quiz.show', compact('lista', 'perguntasJson', 'respostasUsuario', 'isExpired'));
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
        try {
            $idFuncionario = auth()->user()->id_funcionario;
            $idResposta = $request->input('id_resposta');

            $pivo = DB::table('pergunta_lista')
                ->where('idf_lista', $id_lista)
                ->where('idf_pergunta', $id_pergunta)
                ->first();

            if (!$pivo) {
                return response()->json(['error' => 'Pivô do questionário não encontrado.'], 404);
            }

            // Verifica se o usuário já tem essa resposta salva
            $jaRespondeu = DB::table('funcionario_pergunta_lista')
                ->where('idf_funcionario', $idFuncionario)
                ->where('idf_pergunta_lista', $pivo->id_pergunta_lista)
                ->exists();

            if (!$jaRespondeu) {
                // Prepara os dados limpos para a procedure
                $x = (int) $idFuncionario;
                $y = (int) $pivo->id_pergunta_lista;
                $z = (int) $idResposta;

                // Executa a procedure
                DB::unprepared("CALL responder({$x}, {$y}, {$z})");
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            // Se o MySQL der erro, envia o motivo para a tela
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
