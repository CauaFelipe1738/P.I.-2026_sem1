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
        // 1. LIMPEZA DOS DADOS ANTIGOS (Evita duplicidade ao rodar o migrate:seed repetidas vezes)
        // É importante limpar ANTES de criar o admin.
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('funcionario_pergunta_lista')->truncate();
        DB::table('pergunta_lista')->truncate();
        DB::table('resposta')->truncate();
        DB::table('pergunta')->truncate();
        DB::table('lista')->truncate();
        DB::table('funcionario')->truncate();
        DB::table('area')->truncate();
        DB::table('ranking')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. CRIAÇÃO DO USUÁRIO ADMIN ÚNICO
        Funcionario::create([
            'nome_funcionario' => 'Administrador Central',
            'username' => 'admin',
            'senha' => Hash::make('admin'),
            'admin' => 1,
            'pontos' => 0
            // Sem o campo 'titulo', conforme solicitado, pois é calculado automaticamente.
        ]);

        // 3. POPULANDO A TABELA DE RANKING (Regras de Gamificação)
        DB::table('ranking')->insert([
            ['qtd_pessoas' => 1, 'titulo' => 'Poseidon Supremo', 'sobre' => 'Atingiu o topo do conhecimento.'],
            ['qtd_pessoas' => 5, 'titulo' => 'Príncipe dos Mares', 'sobre' => 'Elite corporativa.'],
            ['qtd_pessoas' => 10, 'titulo' => 'Lutador', 'sobre' => 'Consistência e garra.'],
        ]);

        // 4. CRIAÇÃO DAS ÁREAS DE CONHECIMENTO
        $areaSeguranca = Area::create(['nome_area' => 'Segurança da Informação']);
        $areaCompliance = Area::create(['nome_area' => 'Compliance']);
        $areaDev = Area::create(['nome_area' => 'Desenvolvimento de Software']);
        $areaMecatronica = Area::create(['nome_area' => 'Mecatrônica']);
        $areaRedes = Area::create(['nome_area' => 'Redes e Infraestrutura']);

        // 5. CRIAÇÃO DA LISTA DE TREINAMENTO (QUESTIONÁRIO)
        $listaAtual = Lista::create([
            'inicio' => now()->subDays(2)->format('Y-m-d'), // Começou há 2 dias
            'fim' => now()->addDays(10)->format('Y-m-d'),   // Termina daqui a 10 dias
        ]);

        // 6. BANCO DE QUESTÕES ESTRATÉGICAS (Array para facilitar a inserção em massa)
        $questoes = [
            [
                'idf_area' => $areaSeguranca->id_area,
                'pergunta' => 'Você recebe um e-mail urgente supostamente enviado pelo CEO da empresa, solicitando uma transferência bancária confidencial. O remetente é "ceo@corpvvwaredigital.com" (o domínio correto seria @corpware.com). Qual a atitude correta?',
                'valor' => 500,
                'respostas' => [
                    ['resposta' => 'Realizar a operação imediatamente, pois ordens da diretoria com teor confidencial devem ser priorizadas.', 'solucao' => 0],
                    ['resposta' => 'Responder ao e-mail pedindo que o CEO confirme os dados bancários por escrito na mesma mensagem.', 'solucao' => 0],
                    ['resposta' => 'Desconfiar da urgência e da alteração no domínio, e validar a autenticidade por telefone ou presencialmente.', 'solucao' => 1],
                    ['resposta' => 'Ignorar o e-mail, visto que o firewall do servidor já bloquearia a mensagem se ela fosse falsa.', 'solucao' => 0],
                ]
            ],
            [
                'idf_area' => $areaCompliance->id_area,
                'pergunta' => 'No ambiente corporativo moderno, o que significa "Compliance"?',
                'valor' => 500,
                'respostas' => [
                    ['resposta' => 'É um processo exclusivo de averiguação de segurança física dos funcionários da empresa.', 'solucao' => 0],
                    ['resposta' => 'Um conjunto de regras e disciplinas para garantir que a empresa atue em conformidade com as leis.', 'solucao' => 1],
                    ['resposta' => 'É um protocolo de TI desenvolvido para manter a integridade dos dados sensíveis do sistema.', 'solucao' => 0],
                    ['resposta' => 'Uma ferramenta contábil utilizada estritamente para auditar o fluxo de caixa da matriz.', 'solucao' => 0],
                ]
            ],
            [
                'idf_area' => $areaDev->id_area,
                'pergunta' => 'Em programação orientada a objetos, o que é herança?',
                'valor' => 200,
                'respostas' => [
                    ['resposta' => 'Um método para armazenar dados em um banco de dados relacional.', 'solucao' => 0],
                    ['resposta' => 'Um mecanismo que permite que uma classe reutilize atributos e métodos de outra classe.', 'solucao' => 1],
                    ['resposta' => 'Uma forma de criptografar informações sensíveis no servidor.', 'solucao' => 0],
                    ['resposta' => 'Um tipo especial de variável global acessível por toda a aplicação.', 'solucao' => 0],
                ]
            ],
            [
                'idf_area' => $areaDev->id_area,
                'pergunta' => 'Qual é a principal vantagem de utilizar controle de versão com ferramentas como Git?',
                'valor' => 200,
                'respostas' => [
                    ['resposta' => 'Aumentar a velocidade da internet da equipe de desenvolvimento.', 'solucao' => 0],
                    ['resposta' => 'Permitir o acompanhamento de alterações no código e a colaboração entre desenvolvedores.', 'solucao' => 1],
                    ['resposta' => 'Eliminar completamente a necessidade de testes de software manuais.', 'solucao' => 0],
                    ['resposta' => 'Garantir que o código fonte nunca contenha erros de compilação ou bugs.', 'solucao' => 0],
                ]
            ],
            [
                'idf_area' => $areaMecatronica->id_area,
                'pergunta' => 'Em um sistema automatizado de esteira transportadora, qual é a principal função de um sensor fotoelétrico?',
                'valor' => 400,
                'respostas' => [
                    ['resposta' => 'Aumentar a potência e o torque do motor da esteira.', 'solucao' => 0],
                    ['resposta' => 'Detectar a presença ou ausência de objetos sem a necessidade de contato físico.', 'solucao' => 1],
                    ['resposta' => 'Controlar diretamente a velocidade e a frenagem mecânica do motor.', 'solucao' => 0],
                    ['resposta' => 'Armazenar dados de produção no banco de dados local da máquina.', 'solucao' => 0],
                ]
            ],
            [
                'idf_area' => $areaRedes->id_area,
                'pergunta' => 'Quais informações essenciais o servidor DHCP entrega a um novo host conectado na rede?',
                'valor' => 600,
                'respostas' => [
                    ['resposta' => 'IP do servidor DHCP, host e switch que o conectam.', 'solucao' => 0],
                    ['resposta' => 'IP, máscara de sub-rede e MAC Address do host.', 'solucao' => 0],
                    ['resposta' => 'IP do gateway, DNS, host e máscara de sub-rede do host.', 'solucao' => 1],
                    ['resposta' => 'IP e MAC Address de todos os hosts conectados à rede local.', 'solucao' => 0],
                ]
            ]
        ];

        // 7. INSERÇÃO DINÂMICA (Criando as perguntas, as respostas e o pivô da lista)
        foreach ($questoes as $qData) {

            // Cria a pergunta no banco usando o Model
            $pergunta = Pergunta::create([
                'idf_area' => $qData['idf_area'],
                'pergunta' => $qData['pergunta'],
                'valor' => $qData['valor'],
            ]);

            // Cria o vínculo entre a pergunta e a lista (Tabela Pivô)
            DB::table('pergunta_lista')->insert([
                'idf_pergunta' => $pergunta->id_pergunta,
                'idf_lista' => $listaAtual->id_lista,
            ]);

            // Prepara o array de respostas para inserir de uma vez
            $respostasParaInserir = [];
            foreach ($qData['respostas'] as $r) {
                $respostasParaInserir[] = [
                    'idf_pergunta' => $pergunta->id_pergunta,
                    'resposta' => $r['resposta'],
                    'solucao' => $r['solucao'],
                ];
            }

            // Insere todas as alternativas daquela pergunta
            Resposta::insert($respostasParaInserir);
        }
    }
}
