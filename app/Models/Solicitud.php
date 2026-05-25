<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    protected $table = 'solicitudes';
    protected $fillable = ['bien_id', 'responsable_id', 'estado', 'motivo', 'respuesta_admin'];

    public function bien() {
        return $this->belongsTo(Bien::class, 'bien_id', 'idbienes');
    }
    public function responsable() {
        return $this->belongsTo(Responsable::class, 'responsable_id', 'idresponsables');
    }
}