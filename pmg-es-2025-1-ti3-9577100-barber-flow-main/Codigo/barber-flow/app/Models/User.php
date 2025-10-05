<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'access_level',
        'status',
        'google_id',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function is_admin() : bool
    {
        return $this->access_level === 1;
    }
}

/*
COMENTÁRIO:
🔍 Sugestão de Melhoria: O método is_admin() usa o "número mágico" 
1 para verificar o nível de acesso ($this->access_level === 1). 
Isso torna o código menos legível e mais difícil de manter.

Benefícios da Mudança: Se o significado de 1 mudar, você terá 
que alterá-lo em vários lugares. Usar uma constante ou um 
Enum centraliza essa definição.

📌 Sugestão de Implementação (PHP 8.1+ Enums):
enum UserAccessLevel: int {
    case Client = 0;
    case Admin = 1;
}
use App\Enums\UserAccessLevel;
protected $casts = [
    // ...
    'access_level' => UserAccessLevel::class,
];
public function isAdmin(): bool // convenção de nome camelCase
{
    return $this->access_level === UserAccessLevel::Admin;
}
*/