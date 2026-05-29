<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicioGasolina extends Model
{
    protected $table = 'servicio_gasolina';
    protected $primaryKey = 'idservicio_gasolina';

    protected $fillable = [
        'id_user',
        'id_servicio',
        'fecha_vale',
        'id_vehiculo',
        'cantidad_litros',
        'nro_vale',
    ];

    /**
     * Relación con el Usuario que registra el vale
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Relación con el Contrato/Servicio de origen
     */
    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class, 'id_servicio');
    }
    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class, 'id_vehiculo');
    }
}