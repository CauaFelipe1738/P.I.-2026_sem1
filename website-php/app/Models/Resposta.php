<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resposta extends Model
{
    protected $table = 'resposta';
    protected $primaryKey = 'id_resposta';

    protected $casts = [
        'solucao' => 'boolean',
    ];

    public function pergunta()
    {
        return $this->belongsTo(Pergunta::class, 'idf_pergunta', 'id_pergunta');
    }
}
