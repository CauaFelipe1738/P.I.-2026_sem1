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
        Schema::create('funcionario_pergunta_lista', function (Blueprint $table) {
            $table->foreignId('idf_funcionario')->constrained('funcionario', 'id_funcionario')->onDelete('cascade');
            $table->foreignId('idf_pergunta_lista')->constrained('pergunta_lista', 'id_pergunta_lista')->onDelete('cascade');
            $table->foreignId('idf_resposta')->constrained('resposta', 'id_resposta')->onDelete('cascade');

            $table->primary(['idf_funcionario', 'idf_pergunta_lista']);

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
