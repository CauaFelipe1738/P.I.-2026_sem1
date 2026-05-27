<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ranking extends Model
{
    protected $table = 'ranking';
    protected $primaryKey = 'id_ranking';

    public function funcionarios()
    {
        return $this->hasMany(Funcionario::class, 'idf_ranking', 'id_ranking');
    }
}
