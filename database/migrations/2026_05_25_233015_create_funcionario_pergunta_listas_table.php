<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('funcionario_pergunta_listas', function (Blueprint $table) {
            $table->foreignId('idf_funcionario')->constrained('funcionario', 'id_funcionario');
            $table->foreignId('idf_pergunta_lista')->constrained('pergunta_lista', 'id_pergunta_lista');
            $table->foreignId('idf_resposta')->constrained('resposta', 'id_resposta');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funcionario_pergunta_listas');
    }
};
