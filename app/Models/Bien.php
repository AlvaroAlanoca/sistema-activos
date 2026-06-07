<?php 
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Bien extends Model {
    public $timestamps = false;
    protected $table = 'bienes';
    protected $primaryKey = 'idbienes';

    protected $fillable = [
        'estado', 
        'codigo', 
        'descripcion', 
        'id_tipo_bien',
        'correlativo',
        'costo',
        'depreciacion_acumulada',
        'valor_neto',
        'fecha_compra'];

    public function tipoBien() {
        return $this->belongsTo(TipoBien::class, 'id_tipo_bien', 'idtipo_bien');
    }
    public function items() 
    {
                return $this->hasMany(ActaItem::class, 'id_bienes', 'idbienes');
    }
}