<?php

namespace Database\Factories;

use App\Models\Funcionario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Funcionario>
 */
class FuncionarioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome_funcionario' => fake()->name(), // Nome real gerado automaticamente
            'username' => fake()->unique()->userName(), // Usernames únicos
            'senha' => Hash::make('senha123'), // Mesma senha para todos para facilitar o login
            'admin' => 0, // Não são admins
            'pontos' => fake()->numberBetween(0, 20000), // Pontuação aleatória entre 0 e 20 mil
        ];
    }
}
