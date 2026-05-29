<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pergunta extends Model
{
    // Definição de tabela e chave primária
    protected $table = 'pergunta';
    protected $primaryKey = 'id_pergunta';

    // Definição de campos preenchíveis por código
    protected $fillable = [
        'idf_area',
        'pergunta',
        'valor',
        'imagem',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class, 'idf_area', 'id_area');
    }
}
