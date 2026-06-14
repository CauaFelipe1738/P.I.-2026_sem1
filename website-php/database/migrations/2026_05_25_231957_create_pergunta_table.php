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
        Schema::create('pergunta', function (Blueprint $table) {
            $table->id('id_pergunta');
            $table->foreignId('idf_area')->constrained('area', 'id_area')->onDelete('restrict'); // Obriga o administrador a realocar as perguntas antes de excluir a área
            $table->text('pergunta');
            $table->unsignedInteger('valor');
            $table->text('imagem')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pergunta');
    }
};
