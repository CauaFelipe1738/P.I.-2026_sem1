<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resposta extends Model
{
    // Definição de tabela e chave primária
    protected $table = 'resposta';
    protected $primaryKey = 'id_resposta';

    // Definição de campos preenchíveis por código
    protected $fillable = [
        'idf_pergunta',
        'resposta',
        'solucao',
    ];

    protected $casts = [
        'solucao' => 'boolean',
    ];

    public function pergunta()
    {
        return $this->belongsTo(Pergunta::class, 'idf_pergunta', 'id_pergunta');
    }
}
