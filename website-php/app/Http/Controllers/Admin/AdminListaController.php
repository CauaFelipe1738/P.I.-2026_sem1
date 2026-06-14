<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lista;
use Illuminate\Http\Request;

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
        return view('admin.listas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after:data_inicio',
        ], [
            'data_fim.after' => 'A data de fim deve ser posterior à data de início.'
        ]);

        Lista::create([
            'inicio' => $request->data_inicio,
            'fim' => $request->data_fim,
        ]);

        return redirect('/admin/questionarios')->with('success', 'Questionário criado com sucesso!');
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
        return view('admin.listas.edit', compact('lista'));
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
        ], [
            'data_fim.after' => 'A data de fim deve ser posterior à data de início.'
        ]);

        $lista->update([
            'inicio' => $request->data_inicio,
            'fim' => $request->data_fim,
        ]);

        return redirect('/admin/questionarios')->with('success', 'Questionário atualizado com sucesso!');
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
