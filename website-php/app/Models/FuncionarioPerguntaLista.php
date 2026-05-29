<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Thiagoprz\CompositeKey\HasCompositeKey;

class FuncionarioPerguntaLista extends Model
{
    use HasCompositeKey;

    // Definição de tabela
    protected $table = 'funcionario_pergunta_lista';

    // Definição de chave primária composta
    protected $primaryKey = [
        'idf_funcionario',
        'idf_pergunta_lista',
    ];

    // Desativação do auto-incremento
    public $incrementing = false;

    // Definição de campos preenchíveis por código
    protected $fillable = [
        'idf_funcionario',
        'idf_pergunta_lista',
        'idf_resposta',
    ];
}
