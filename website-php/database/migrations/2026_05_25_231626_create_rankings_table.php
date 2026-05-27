<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB; // DB
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rankings', function (Blueprint $table) {
            $table->id('id_ranking');
            $table->integer('qtd_pessoas');
            $table->string('titulo', 30);
            $table->text('sobre')->nullable();
            $table->timestamps();
        });

        DB::table('ranking')->create([
            'qtd_pessoas' => '10',
            'titulo' => 'teste',
            'sobre' => 'sobreTeste'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rankings');
    }
};
