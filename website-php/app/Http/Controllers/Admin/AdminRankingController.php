<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ranking;
use Illuminate\Http\Request;

class AdminRankingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $busca = $request->input('search');
        $query = Ranking::query();

        if (!empty($busca)) {
            $query->where(function($q) use ($busca) {
                $q->where('titulo', 'LIKE', "%{$busca}%")
                  ->orWhere('sobre', 'LIKE', "%{$busca}%");
            });
        }

        $rankings = $query->paginate(10)->appends(['search' => $busca]);
        return view('admin.rankings.index', compact('rankings', 'busca'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.rankings.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
        'titulo' => 'required|string|max:30',
        'qtd_pessoas' => 'required|integer|min:1|unique:ranking,qtd_pessoas',
        'sobre' => 'nullable|string',
        ], [
            // Mensagem de erro caso a regra unique falhe
            'qtd_pessoas.unique' => 'Já existe um ranking cadastrado com essa exata quantidade de pessoas. Escolha outro valor.',
        ]);

        Ranking::create($request->all());

        return redirect('/admin/rankings')->with('success', 'Ranking criado com sucesso!');
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
        $ranking = Ranking::findOrFail($id);
        return view('admin.rankings.edit', compact('ranking'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $ranking = Ranking::findOrFail($id);

        $request->validate([
        'titulo' => 'required|string|max:30',
        'qtd_pessoas' => 'required|integer|min:1|unique:ranking,qtd_pessoas,' . $id . ',id_ranking',
        'sobre' => 'nullable|string',
        ], [
            'qtd_pessoas.unique' => 'Já existe um outro ranking cadastrado com essa exata quantidade de pessoas. Escolha outro valor.',
        ]);

        $ranking->update($request->all());
        return redirect()->route('admin.rankings.index')->with('success', 'Ranking atualizado!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Ranking::destroy($id);
        return back()->with('success', 'Ranking excluído!');
    }
}
