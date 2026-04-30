<?php 
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Bien extends Model {
    protected $table = 'bienes';
    protected $primaryKey = 'idbienes';
    public $timestamps = false;
    protected $fillable = [
        'estado', 
        'codigo', 
        'descripcion', 
        'id_tipo_bien'];

    public function tipoBien() {
        return $this->belongsTo(TipoBien::class, 'id_tipo_bien', 'idtipo_bien');
    }
}