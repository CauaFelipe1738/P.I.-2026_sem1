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
        Schema::create('pergunta_lista', function (Blueprint $table) {
            $table->id('id_pergunta_lista');
            $table->foreignId('idf_pergunta')->constrained('pergunta', 'id_pergunta')->onDelete('cascade');
            $table->foreignId('idf_lista')->constrained('lista', 'id_lista')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pergunta_listas');
    }
};
