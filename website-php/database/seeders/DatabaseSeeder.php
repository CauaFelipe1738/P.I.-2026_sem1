<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Funcionario;
use App\Models\Lista;
use App\Models\Pergunta;
use App\Models\Resposta;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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

        // Cria um usuário admin para logar
        Funcionario::create([
            'nome_funcionario' => 'Administrador Central',
            'username' => 'admin',
            'senha' => Hash::make('admin'),
            'admin' => 1,
            'pontos' => 0
        ]);

        // Gera 50 usuários aleatórios com a factory de funcionário
        Funcionario::factory()->count(50)->create();

        // Criar áreas de conhecimento
        $areaSeguranca = Area::create(['nome_area' => 'Segurança da Informação']);
        $areaCompliance = Area::create(['nome_area' => 'Compliance']);

        // Criar listas
        $listaAtual = Lista::create([
            'inicio' => now()->subDays(2)->format('Y-m-d'), // Começou há 2 dias
            'fim' => now()->addDays(10)->format('Y-m-d'),   // Termina daqui a 10 dias
        ]);

        // Criar perguntas e respostas
        $pergunta1 = Pergunta::create([
            'idf_area' => $areaSeguranca->id_area,
            'pergunta' => 'Qual é a principal característica de um ataque de Phishing?',
            'valor' => 500,
        ]);

        // Respostas da pergunta 1
        Resposta::insert([
            ['idf_pergunta' => $pergunta1->id_pergunta, 'resposta' => 'Um vírus que queima o HD.', 'solucao' => 0],
            ['idf_pergunta' => $pergunta1->id_pergunta, 'resposta' => 'Enganação para roubar senhas e dados.', 'solucao' => 1],
            ['idf_pergunta' => $pergunta1->id_pergunta, 'resposta' => 'Um software de proteção nativo do Windows.', 'solucao' => 0],
        ]);

        $pergunta2 = Pergunta::create([
            'idf_area' => $areaCompliance->id_area,
            'pergunta' => 'É permitido compartilhar sua senha corporativa com colegas de trabalho?',
            'valor' => 300,
        ]);

        // Respostas da pergunta 2
        Resposta::insert([
            ['idf_pergunta' => $pergunta2->id_pergunta, 'resposta' => 'Sim, apenas com o chefe.', 'solucao' => 0],
            ['idf_pergunta' => $pergunta2->id_pergunta, 'resposta' => 'Não, a senha é pessoal e intransferível.', 'solucao' => 1],
            ['idf_pergunta' => $pergunta2->id_pergunta, 'resposta' => 'Sim, se for para adiantar um projeto.', 'solucao' => 0],
        ]);

        // Vinculando as perguntas à lista (tabela pivô pergunta_lista)
        DB::table('pergunta_lista')->insert([
            ['idf_pergunta' => $pergunta1->id_pergunta, 'idf_lista' => $listaAtual->id_lista],
            ['idf_pergunta' => $pergunta2->id_pergunta, 'idf_lista' => $listaAtual->id_lista],
        ]);

        // Populando a tabela de ranking
        DB::table('ranking')->insert([
            ['qtd_pessoas' => 1, 'titulo' => 'Poseidon Supremo', 'sobre' => 'Atingiu o topo do conhecimento.'],
            ['qtd_pessoas' => 5, 'titulo' => 'Príncipe dos Mares', 'sobre' => 'Elite corporativa.'],
            ['qtd_pessoas' => 10, 'titulo' => 'Lutador', 'sobre' => 'Consistência e garra.'],
        ]);
    }
}
