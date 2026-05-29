<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ranking extends Model
{
    // Definição de tabela e chave primária
    protected $table = 'ranking';
    protected $primaryKey = 'id_ranking';

    // Definição de campos preenchíveis por código
    protected $fillable = [
        'qtd_pessoas',
        'titulo',
        'sobre',
    ];
}
