<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerguntaLista extends Model
{
    // Definição de tabela e chave primária
    protected $table = 'pergunta_lista';
    protected $primaryKey = 'id_pergunta_lista';

    // Definição de campos preenchíveis por código
    protected $fillable = [
        'idf_pergunta',
        'idf_lista',
    ];
}
