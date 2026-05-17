<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    use HasFactory;

    // Nombre de la tabla en MySQL
    protected $table = 'servicios';

    // Llave primaria personalizada
    protected $primaryKey = 'idservicios';

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'cuce',
        'descripcion',
        'empresa',
        'tipo',
    ];

    // Relación inversa: Un servicio puede estar en muchos contratos
    public function contratos()
    {
        return $this->hasMany(ServicioContrato::class, 'id_servicio', 'idservicios');
    }
}