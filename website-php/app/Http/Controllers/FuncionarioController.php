<?php

namespace App\Http\Controllers;

use App\Models\Funcionario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class FuncionarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Captura o que o usuário digitou no campo de busca
        $busca = $request->input('search');

        // Inicia a query apontando para a view funcio_ranque
        $query = DB::table('funcio_ranque');

        // Se o usuário digitar algo no campo de pesquisa, aplica o filtro de nome ou username
        if (!empty($busca)) {
            $query->where(function($q) use ($busca) {
                $q->where('nome_funcionario', 'LIKE', "%{$busca}%")
                ->orWhere('username', 'LIKE', "%{$busca}%");
            });
        }

        // Mostra apenas 10 usuários por página
        // O appends() garante que, se o usuário pesquisar algo, a busca continue funcionando quando mudar de página
        $usuarios = $query->paginate(10)->appends(['search' => $busca]);

        return view('admin.usuarios.index', compact('usuarios', 'busca'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.usuarios.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:40',
            'username' => 'required|string|max:40|unique:funcionario,username',
            'senha' => 'required|string|min:6',
            'tipo_acesso' => 'required|in:administrador,usuario',
        ]);

        Funcionario::create([
            'nome_funcionario' => $request->nome,
            'username' => $request->username,
            'senha' => Hash::make($request->senha),
            'admin' => $request->tipo_acesso === 'administrador' ? 1 : 0,
            'pontos' => 0,
        ]);

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuário criado com sucesso!');
    }

    public function storeAPI(Request $request)
    {
        // Validação idêntica, mas que retorna JSON automaticamente se falhar
        $request->validate([
            'nome' => 'required|string|max:40',
            'username' => 'required|string|max:40|unique:funcionario,username',
            'senha' => 'required|string|min:6',
            'tipo_acesso' => 'required|in:administrador,usuario',
        ]);

        $usuario = Funcionario::create([
            'nome_funcionario' => $request->nome,
            'username' => $request->username,
            'senha' => Hash::make($request->senha),
            'admin' => $request->tipo_acesso === 'administrador' ? 1 : 0,
            'pontos' => 0
        ]);

        return response()->json([
            'message' => 'Usuário criado com sucesso via API!',
            'usuario' => [
                'id' => $usuario->id_funcionario,
                'nome' => $usuario->nome_funcionario,
                'username' => $usuario->username,
                'tipo_acesso' => $usuario->admin ? 'administrador' : 'usuario'
            ]
        ], 21);
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
    public function update(Request $request, Funcionario $funcionario)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Funcionario $funcionario)
    {
        //
    }
}
