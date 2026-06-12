<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Derruba as estruturas se já existirem para evitar erros
        DB::unprepared('DROP FUNCTION IF EXISTS vrecompensa');
        DB::unprepared('DROP PROCEDURE IF EXISTS responder');
        DB::unprepared('DROP PROCEDURE IF EXISTS fetch_listas');
        DB::unprepared('DROP PROCEDURE IF EXISTS quest_info');
        DB::unprepared('DROP VIEW IF EXISTS funcio_ranque');

        // Criar função vrecompensa
        DB::unprepared('
            CREATE FUNCTION vrecompensa(z int) RETURNS int
            DETERMINISTIC
            BEGIN
              DECLARE recompensa int;
              select (valor * solucao) into recompensa from resposta
              inner join pergunta on resposta.idf_pergunta = pergunta.id_pergunta
              where id_resposta = z;
              RETURN recompensa;
            END
        ');

        // Criar procedure responder
        DB::unprepared('
            CREATE PROCEDURE responder(IN x INT, in y int, in z int)
            BEGIN
                DECLARE recompensa INT;
                set recompensa = vrecompensa(z);
                INSERT INTO funcionario_pergunta_lista (idf_funcionario,idf_pergunta_lista,idf_resposta) VALUE (x,y,z);

                if recompensa > 0 then
                    update funcionario set pontos = pontos + recompensa where id_funcionario = x;
                end if;
            END
        ');

        // Criar procedure fetch_listas
        DB::unprepared('
            CREATE PROCEDURE fetch_listas(IN x INT, IN y date)
            BEGIN
                select lista.*, count(idf_funcionario) as respostas, count(id_pergunta_lista) as perguntas from lista
                left join pergunta_lista on pergunta_lista.idf_lista = lista.id_lista
                left join (select * from funcionario_pergunta_lista where idf_funcionario = x) fpl on fpl.idf_pergunta_lista = pergunta_lista.id_pergunta_lista
                group by id_lista
                having inicio <= y and fim > y or respostas > 0;
            END
        ');

        // Criar procedure quest_info
        DB::unprepared('
            CREATE PROCEDURE quest_info(IN x INT, in y int)
            BEGIN
                select * from lista where id_lista = x;

                select id_pergunta, pergunta, valor, image, nome_area from pergunta
                inner join area on pergunta.idf_area = area.id_area
                inner join pergunta_lista on pergunta.id_pergunta = pergunta_lista.idf_pergunta
                where pergunta_lista.idf_lista = x;

                select resposta.* from resposta
                inner join pergunta_lista on resposta.idf_pergunta = pergunta_lista.idf_pergunta
                where pergunta_lista.idf_lista = x;

                select pergunta_lista.idf_pergunta,idf_resposta from funcionario_pergunta_lista
                inner join pergunta_lista on pergunta_lista.id_pergunta_lista = funcionario_pergunta_lista.idf_pergunta_lista
                where pergunta_lista.idf_lista = x and idf_funcionario = y;
            END
        ');

        // Criar view funcio_ranque
        DB::unprepared('
            CREATE VIEW funcio_ranque AS
            select
                `funcionario`.`id_funcionario` AS `id_funcionario`,
                `funcionario`.`username` AS `username`,
                `funcionario`.`nome_funcionario` AS `nome_funcionario`,
                `funcionario`.`admin` AS `admin`,
                `funcionario`.`pontos` AS `pontos`,
                `ranking`.`titulo` AS `titulo`,
                `ranking`.`sobre` AS `sobre`
            from ((`funcionario`
            join (
                select `funcionario`.`id_funcionario` AS `id_funcionario`,
                    min(`ranking`.`qtd_pessoas`) AS `minimo`
                from ((`funcionario`
                join (
                    select `funcionario`.`id_funcionario` AS `id_funcionario`,
                        row_number() OVER (ORDER BY `funcionario`.`pontos` desc ) AS `row_position`
                    from `funcionario`
                ) `positions` on((`positions`.`id_funcionario` = `funcionario`.`id_funcionario`)))
                left join `ranking` on((`positions`.`row_position` <= `ranking`.`qtd_pessoas`)))
                group by `funcionario`.`id_funcionario`
            ) `asociacao` on((`asociacao`.`id_funcionario` = `funcionario`.`id_funcionario`)))
            left join `ranking` on((`ranking`.`qtd_pessoas` = `asociacao`.`minimo`)));
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP FUNCTION IF EXISTS vrecompensa');
        DB::unprepared('DROP PROCEDURE IF EXISTS responder');
        DB::unprepared('DROP PROCEDURE IF EXISTS fetch_listas');
        DB::unprepared('DROP PROCEDURE IF EXISTS quest_info');
        DB::unprepared('DROP VIEW IF EXISTS funcio_ranque');
    }
};
