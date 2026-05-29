<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lista extends Model
{
    // Definição de tabela e chave primária
    protected $table = 'lista';
    protected $primaryKey = 'id_lista';

    // Definição de campos preenchíveis por código
    protected $fillable = [
        'inicio',
        'fim',
    ];

    // Relação N para N com Perguntas
    public function perguntas()
    {
        return $this->belongsToMany(
            Pergunta::class,
            'pergunta_lista',
            'idf_lista',
            'idf_pergunta',
            'id_lista',
            'id_pergunta'
        )->withPivot('id_pergunta_lista'); // Traz a PK da tabela pivô junto
    }

    // Relação N para N com Funcionarios
    public function funcionarios()
    {
        return $this->belongsToMany(
            Funcionario::class,
            'funcionario_lista',
            'idf_lista',
            'idf_funcionario',
            'id_lista',
            'id_funcionario'
        )->withPivot('respondido', 'acertos');
    }
}
