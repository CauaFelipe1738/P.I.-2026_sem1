<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminListaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $busca = $request->input('search');
        $query = Lista::query();

        if (!empty($busca)) {
            // Busca pelo ID do questionário
            $query->where('id_lista', $busca);
        }

        $listas = $query->paginate(10)->appends(['search' => $busca]);
        return view('admin.listas.index', compact('listas', 'busca'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Puxa todas as perguntas com o nome da área para listar no formulário
        $todasPerguntas = DB::table('pergunta')
            ->join('area', 'pergunta.idf_area', '=', 'area.id_area')
            ->select('pergunta.id_pergunta', 'pergunta.pergunta', 'pergunta.valor', 'area.nome_area')
            ->orderBy('area.nome_area')
            ->get();

        return view('admin.listas.create', compact('todasPerguntas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after:data_inicio',
            'perguntas' => 'required|array|min:1', // Exige pelo menos 1 pergunta
        ]);

        // Cria a lista e pega o ID gerado
        $idLista = Lista::insertGetId([
            'inicio' => $request->data_inicio,
            'fim' => $request->data_fim,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Prepara o array para inserir na tabela pergunta_lista (Relação N:N)
        $relacoes = [];
        foreach ($request->perguntas as $idPergunta) {
            $relacoes[] = [
                'idf_lista' => $idLista,
                'idf_pergunta' => $idPergunta,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        DB::table('pergunta_lista')->insert($relacoes);

        return redirect()->route('admin.listas.index')->with('success', 'Questionário criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Lista $lista)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $lista = Lista::findOrFail($id);

        $todasPerguntas = DB::table('pergunta')
            ->join('area', 'pergunta.idf_area', '=', 'area.id_area')
            ->select('pergunta.id_pergunta', 'pergunta.pergunta', 'pergunta.valor', 'area.nome_area')
            ->orderBy('area.nome_area')
            ->get();

        // Pega apenas os IDs das perguntas que já pertencem a essa lista para marcar os checkboxes
        $perguntasSelecionadas = DB::table('pergunta_lista')
            ->where('idf_lista', $id)
            ->pluck('idf_pergunta')
            ->toArray();

        return view('admin.listas.create', compact('lista', 'todasPerguntas', 'perguntasSelecionadas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $lista = Lista::findOrFail($id);

        $request->validate([
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after:data_inicio',
            'perguntas' => 'required|array|min:1',
        ]);

        $lista->update([
            'inicio' => $request->data_inicio,
            'fim' => $request->data_fim,
        ]);

        // Apaga as relações antigas e insere as novas
        DB::table('pergunta_lista')->where('idf_lista', $id)->delete();

        $relacoes = [];
        foreach ($request->perguntas as $idPergunta) {
            $relacoes[] = [
                'idf_lista' => $id,
                'idf_pergunta' => $idPergunta,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        DB::table('pergunta_lista')->insert($relacoes);

        return redirect()->route('admin.listas.index')->with('success', 'Questionário atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        Lista::destroy($id);
        return back()->with('success', 'Questionário excluído!');
    }
}
