<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    // Definição de tabela e chave primária
    protected $table = 'area';
    protected $primaryKey = 'id_area';

    // Definição de campos preenchíveis por código
    protected $fillable = [
        'nome_area',
    ];
}
