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
        Schema::create('funcionario_lista', function (Blueprint $table) {
            $table->foreignId('idf_funcionario')->constrained('funcionario', 'id_funcionario')->onDelete('cascade');
            $table->foreignId('idf_lista')->constrained('lista', 'id_lista')->onDelete('cascade');
            
            $table->boolean('respondido');
            $table->integer('acertos');

            $table->primary(['idf_funcionario', 'idf_lista']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funcionario_listas');
    }
};
