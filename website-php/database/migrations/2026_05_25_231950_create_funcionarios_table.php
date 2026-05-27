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
        Schema::create('funcionarios', function (Blueprint $table) {
            $table->id('id_funcionario');
            $table->foreignId('idf_ranking')->nullable()->constrained('rankings', 'id_ranking');
            $table->string('nome_funcionario', 40);
            $table->string('senha', 40);
            $table->boolean('admin');
            $table->integer('pontos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funcionarios');
    }
};
