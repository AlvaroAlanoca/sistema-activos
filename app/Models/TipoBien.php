<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TipoBien extends Model {
    protected $table = 'tipo_bien';
    protected $primaryKey = 'idtipo_bien';
    public $timestamps = false;
    protected $fillable = [
        'id_rubro', 
        'descripcion'
        ];

    public function rubro() {
    return $this->belongsTo(Rubro::class, 'id_rubro', 'idrubros');
    }

}