<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
        'responsable_id'

    ];
    public function responsable()
    {
        // El orden es: Modelo, Mi llave foránea, La llave primaria del otro
        return $this->belongsTo(Responsable::class, 'responsable_id', 'idresponsables');
    }
    public function getFilamentName(): string
    {
        // Si es responsable y tiene una ficha vinculada, mostramos ese nombre
        if ($this->rol === 'responsable' && $this->responsable) {
            return $this->responsable->nombre_apellido . ' (Funcionario)';
        }
        
        // Si es admin, mostramos su nombre normal
        return $this->name . ' (Administrador)';
    }
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
    public function isAdmin(): bool
    {
        return $this->rol === 'admin';
    }

    public function isResponsable(): bool
    {
        return $this->rol === 'responsable';
    }
}
