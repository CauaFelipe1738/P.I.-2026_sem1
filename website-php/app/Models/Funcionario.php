<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Funcionario extends Authenticatable
{
    use Notifiable, HasFactory;

    // Definição de tabela e chave primária
    protected $table = 'funcionario';
    protected $primaryKey = 'id_funcionario';

    // Definição de campos preenchíveis por código
    protected $fillable = [
        'nome_funcionario',
        'username',
        'senha',
        'admin',
        'pontos',
    ];

    // Esconde a senha e o token quando o model for convertido para array/JSON
    protected $hidden = [
        'senha',
        'remember_token',
    ];

    // Definição de nome da coluna de senha: "senha"
    public function getAuthPassword()
    {
        return $this->senha;
    }

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
