<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pergunta extends Model
{
    protected $table = 'pergunta';
    protected $primaryKey = 'id_pergunta';

    public function area()
    {
        return $this->belongsTo(Area::class, 'idf_area', 'id_area');
    }
}
