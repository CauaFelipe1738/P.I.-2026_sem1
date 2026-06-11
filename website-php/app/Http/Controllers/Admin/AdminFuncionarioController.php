<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Funcionario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminFuncionarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $funcionarios = Funcionario::all();

        if ($request->expectsJson()) {
            return response()->json(['status' => 'success', 'data' => $funcionarios]);
        }
        return view('admin.funcionarios.index', compact('funcionarios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.funcionarios.create'); // Ajuste o caminho se salvou com outro nome
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome_funcionario' => 'required|string|max:255',
            'username' => 'required|string|unique:funcionario,username|max:255',
            'senha' => 'required|string|min:6',
            'admin' => 'required|boolean'
        ]);

        $funcionario = Funcionario::create([
            'nome_funcionario' => $request->nome_funcionario,
            'username' => $request->username,
            'senha' => Hash::make($request->senha), // Criptografia obrigatória
            'admin' => $request->admin,
            'pontos' => 0 // Todo funcionário começa com zero pontos
        ]);

        if ($request->expectsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Funcionário criado!', 'data' => $funcionario], 201);
        }
        return redirect()->back()->with('success', 'Funcionário cadastrado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Funcionario $funcionario)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Funcionario $funcionario)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $funcionario = Funcionario::findOrFail($id);

        $request->validate([
            'nome_funcionario' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:funcionario,username,' . $funcionario->id_funcionario . ',id_funcionario',
            'admin' => 'required|boolean',
            'pontos' => 'required|integer'
        ]);

        $dados = $request->only(['nome_funcionario', 'username', 'admin', 'pontos']);

        // Se o admin digitou uma nova senha, atualiza. Se deixou em branco, mantém a antiga.
        if ($request->filled('senha')) {
            $request->validate(['senha' => 'string|min:6']);
            $dados['senha'] = Hash::make($request->senha);
        }

        $funcionario->update($dados);

        if ($request->expectsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Funcionário atualizado!', 'data' => $funcionario]);
        }
        return redirect()->back()->with('success', 'Funcionário atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $funcionario = Funcionario::findOrFail($id);
        $funcionario->delete();

        if ($request->expectsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Funcionário excluído!']);
        }
        return redirect()->back()->with('success', 'Funcionário excluído com sucesso!');
    }
}
