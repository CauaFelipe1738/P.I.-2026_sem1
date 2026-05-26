<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Funcionario extends Model
{
    protected $table = 'funcionario';
    protected $primaryKey = 'id_funcionario';

    public function listas()
    {
        return $this->belongsToMany(
            Lista::class,          // 1. O model com quem se relaciona
            'funcionario_lista',   // 2. O nome da tabela pivô
            'idf_funcionario',     // 3. A FK do model atual na tabela pivô
            'idf_lista',           // 4. A FK do model relacionado na tabela pivô
            'id_funcionario',      // 5. A PK do model atual
            'id_lista'             // 6. A PK do model relacionado
        )->withPivot('respondido', 'acertos'); // Para conseguir acessar esses campos extras depois
    }
}
