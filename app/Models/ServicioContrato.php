<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicioContrato extends Model
{
    use HasFactory;

    protected $table = 'servicio_contrato';

    protected $primaryKey = 'idservicio_contrato';

    protected $fillable = [
        'id_user',
        'id_servicio',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'descripcion',
    ];

    /**
     * Relación con el Usuario (Responsable)
     * Esto permite el autocompletado en el formulario
     */
 public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Relación con el Servicio
     * Permite obtener el CUCE o la Empresa desde el contrato
     */
    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'id_servicio', 'idservicios');
    }
}