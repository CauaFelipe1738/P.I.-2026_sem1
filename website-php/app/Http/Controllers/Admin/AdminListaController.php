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
        // Traz junto todas as perguntas associadas a cada lista
        $listas = Lista::with('perguntas')->get();

        if ($request->expectsJson()) {
            return response()->json(['status' => 'success', 'data' => $listas]);
        }
        return view('admin.listas.index', compact('listas'));
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
        $request->validate([
            'inicio' => 'required|date',
            'fim' => 'required|date|after_or_equal:inicio',
            'perguntas' => 'required|array', // Array de IDs das perguntas selecionadas
            'perguntas.*' => 'integer|exists:pergunta,id_pergunta' // Valida se cada ID realmente existe na tabela pergunta
        ]);

        // Cria a lista (ID, data de início e fim)
        $lista = Lista::create($request->only(['inicio', 'fim']));

        // Alimenta a tabela pivô "pergunta_lista" automaticamente
        // O método "attach" faz exatamente a inserção dos relacionamentos no banco
        $lista->perguntas()->attach($request->perguntas);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Questionário criado e perguntas vinculadas!',
                'data' => $lista->load('perguntas')
            ], 201);
        }
        return redirect()->back()->with('success', 'Questionário criado com sucesso!');
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
    public function edit(Lista $lista)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $lista = Lista::findOrFail($id);

        $request->validate([
            'inicio' => 'required|date',
            'fim' => 'required|date|after_or_equal:inicio',
            'perguntas' => 'required|array',
            'perguntas.*' => 'integer|exists:pergunta,id_pergunta'
        ]);

        $lista->update($request->only(['inicio', 'fim']));

        // Método "sync" remove todas as perguntas antigas que não foram enviadas e adiciona as novas, mantendo a tabela pivô atualizada
        $lista->perguntas()->sync($request->perguntas);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Questionário atualizado!',
                'data' => $lista->load('perguntas')
            ]);
        }
        return redirect()->back()->with('success', 'Questionário atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $lista = Lista::findOrFail($id);

        $lista->delete();

        if ($request->expectsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Questionário excluído!']);
        }
        return redirect()->back()->with('success', 'Questionário excluído!');
    }
}
