<?php 
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Responsable extends Model {
    protected $table = 'responsables';
    protected $primaryKey = 'idresponsables';

    protected $fillable = ['nombre_apellido', 
    'id_oficinas_cargos',
    'ci',
    'numero_item'];

    public function oficinaCargo() {
    return $this->belongsTo(OficinaCargo::class, 'id_oficinas_cargos', 'idoficinas_cargos');
    }
}
