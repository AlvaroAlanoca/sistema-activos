<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Traits\HasRoles; 
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'responsable_id'
    ];

    /**
     * Relación con la tabla Responsables
     */
    public function responsable(): BelongsTo
    {
        return $this->belongsTo(Responsable::class, 'responsable_id', 'idresponsables');
    }

    /**
     * Nombre a mostrar en la interfaz de Filament
     */
    public function getFilamentName(): string
    {
        // NUEVA LÓGICA: Usamos hasRole() de Spatie
        if ($this->hasRole('responsable') && $this->responsable) {
            return $this->responsable->nombre_apellido . ' (Funcionario)';
        }
        
        // Si no es responsable (ej. super_admin o admin)
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

}