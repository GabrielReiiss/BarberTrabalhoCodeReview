<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'description',
        'acess',
        'duration',
        'category_id',
        'avatar',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
}

/*
COMENTÁRIO:
🔍 Sugestão de Melhoria: Os métodos de relacionamento 
(como appointments(), favorites(), etc.) não possuem 
blocos de documentação (PHPDoc).

Benefícios da Mudança: Adicionar PHPDoc ajuda as IDEs 
a entenderem o tipo de retorno, habilitando o autocompletar 
e a análise estática de código, o que acelera o desenvolvimento e previne bugs.

@return \Illuminate\Database\Eloquent\Relations\HasMany
📌 Sugestão de Implementação:
public function appointments()
{
    return $this->hasMany(Appointment::class);
}
*/
