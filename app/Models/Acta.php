<?php 
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Acta extends Model {
    protected $table = 'actas';
    protected $primaryKey = 'idacta';

    protected $fillable = ['tipo', 
    'numero_acta', 
    'id_responsables'];
    protected $casts = [
    'created_at' => 'datetime',
];

    // Esta relación conecta el acta con su lista de bienes
    public function items() {
        return $this->hasMany(ActaItem::class, 'id_acta', 'idacta');
    }

    public function responsable() {
        return $this->belongsTo(Responsable::class, 'id_responsables', 'idresponsables');
    }
}