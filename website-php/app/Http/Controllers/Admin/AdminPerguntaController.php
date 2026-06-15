<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Pergunta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminPerguntaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $busca = $request->input('search');
        $areaFiltro = $request->input('area_id');

        // Puxa as perguntas com um JOIN para trazer o nome da área
        $query = Pergunta::select('pergunta.*', 'area.nome_area')
            ->join('area', 'pergunta.idf_area', '=', 'area.id_area');

        // Filtro por texto na Pergunta
        if (!empty($busca)) {
            $query->where('pergunta', 'LIKE', '%' . $busca . '%');
        }

        // Filtro por área
        if (!empty($areaFiltro)) {
            $query->where('idf_area', $areaFiltro);
        }

        $perguntas = $query->paginate(10)->appends(['search' => $busca, 'area_id' => $areaFiltro]);

        // Carrega as áreas para o select de filtros
        $areas = Area::orderBy('nome_area', 'asc')->get();

        return view('admin.perguntas.index', compact('perguntas', 'areas', 'busca', 'areaFiltro'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $areas = Area::orderBy('nome_area', 'asc')->get();

        return view('admin.perguntas.create', compact('areas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Valida os dados (garante array de respostas e apenas um selecionado como solução)
        $request->validate([
            'pergunta' => 'required|string|max:65535',
            'idf_area' => 'required|integer|exists:area,id_area',
            'valor' => 'required|integer|min:0',
            'imagem' => 'nullable|url',
            'respostas' => 'required|array|min:2|max:5',
            'solucao_index' => 'required|integer|min:0|max:4'
        ]);

        // Salva a pergunta e resgata o ID gerado
        $idPergunta = DB::table('pergunta')->insertGetId([
            'idf_area' => $request->idf_area,
            'pergunta' => $request->pergunta,
            'valor' => $request->valor,
            'imagem' => $request->imagem,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Salva as alternativas
        $respostasData = [];
        foreach ($request->respostas as $index => $texto) {
            $respostasData[] = [
                'idf_pergunta' => $idPergunta,
                'resposta' => $texto,
                'solucao' => ($request->solucao_index == $index), // Define TRUE se for o índice correto
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        DB::table('resposta')->insert($respostasData);

        return redirect()->route('admin.perguntas.index')->with('success', 'Pergunta criada com sucesso!');
    }

    // Ação acionada pelo Modal via AJAX ou Form Normal para criar uma nova área rápida
    public function storeArea(Request $request)
    {
        $request->validate([
            'nome_area' => 'required|string|max:30|unique:area,nome_area'
        ], [
            'nome_area.unique' => 'Já existe uma área com esse nome.'
        ]);

        Area::create(['nome_area' => $request->nome_area]);

        return back()->with('success', 'Área criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $pergunta = DB::table('pergunta')->where('id_pergunta', $id)->first();
        $respostas = DB::table('resposta')->where('idf_pergunta', $id)->get();
        $areas = Area::orderBy('nome_area', 'asc')->get();

        return view('admin.perguntas.create', compact('pergunta', 'respostas', 'areas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'pergunta' => 'required|string|max:65535',
            'idf_area' => 'required|integer|exists:area,id_area',
            'valor' => 'required|integer|min:0',
            'imagem' => 'nullable|url',
            'respostas' => 'required|array|min:2|max:5',
            'solucao_index' => 'required|integer|min:0|max:4'
        ]);

        // Atualiza a pergunta
        DB::table('pergunta')->where('id_pergunta', $id)->update([
            'idf_area' => $request->idf_area,
            'pergunta' => $request->pergunta,
            'valor' => $request->valor,
            'imagem' => $request->imagem,
            'updated_at' => now()
        ]);

        // Apaga as respostas antigas e insere as novas (mais seguro que tentar dar update nelas)
        DB::table('resposta')->where('idf_pergunta', $id)->delete();

        $respostasData = [];
        foreach ($request->respostas as $index => $texto) {
            $respostasData[] = [
                'idf_pergunta' => $id,
                'resposta' => $texto,
                'solucao' => ($request->solucao_index == $index),
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        DB::table('resposta')->insert($respostasData);

        return redirect()->route('admin.perguntas.index')->with('success', 'Pergunta atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
