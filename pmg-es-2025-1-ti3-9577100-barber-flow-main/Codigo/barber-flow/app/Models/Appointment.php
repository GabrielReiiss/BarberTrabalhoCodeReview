<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'user_id',
        'barber_id',
        'service_id',
        'start',
        'end',
        'confirmed_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function barber()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}

/*
COMENTÁRIO:
🔍 Sugestão de Melhoria: Os relacionamentos user() e barber() apontam para o mesmo modelo User. Embora funcional, isso pode gerar confusão na leitura. Qual deles é o cliente?

Benefícios da Mudança: Renomear o relacionamento user() para client() tornaria o propósito de cada um instantaneamente claro.

📌 Sugestão de Implementação:
public function client()
{
    return $this->belongsTo(User::class, 'user_id');
}
*/
