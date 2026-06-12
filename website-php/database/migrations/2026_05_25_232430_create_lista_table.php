<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lista', function (Blueprint $table) {
            $table->id('id_lista');
            $table->date('inicio');
            $table->date('fim');
            $table->timestamps();
        });

        // Adiciona constraint por SQL puro
        DB::statement('ALTER TABLE lista ADD CONSTRAINT fim_depois CHECK (fim > inicio)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lista');
    }
};
