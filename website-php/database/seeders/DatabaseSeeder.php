<?php

namespace Database\Seeders;

use App\Models\Funcionario;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Funcionario::create([
            'nome_funcionario' => 'Administrador Chefe',
            'username' => 'admin',
            'senha' => Hash::make('senha123'), // Criptografia de senha
            'admin' => true,
            'pontos' => 0,
        ]);
    }
}
