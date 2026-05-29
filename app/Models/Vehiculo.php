<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehiculo extends Model
{
    protected $table = 'vehiculos';
    protected $primaryKey = 'idvehiculo'; // Llave primaria personalizada

    protected $fillable = [
        'placa',
        'descripcion',
    ];

    /**
     * Relación: Un vehículo puede tener muchos vales de gasolina cargados
     */
    public function valesGasolina(): HasMany
    {
        return $this->hasMany(ServicioGasolina::class, 'id_vehiculo');
    }
}