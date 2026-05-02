<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ActaItem extends Model {
    protected $table = 'acta_items';
    protected $primaryKey = 'idacta_items';

    protected $fillable = [
        'id_bienes', 
        'id_acta', 
        'estado'];
protected static function booted()
{
    // Cuando se crea un registro en acta_items
    static::created(function ($item) {
        if ($item->acta->tipo === 'DEVOLUCION') {
            $item->bien->update(['estado' => 'DISPONIBLE']);
        } else {
            $item->bien->update(['estado' => 'ENTREGADO']);
        }
    });

    // Opcional: Cuando se elimina un registro (se devuelve el bien)
    static::deleted(function ($item) {
        $item->bien->update(['estado' => 'DISPONIBLE']);
    });
}
        public function acta()
    {
        return $this->belongsTo(Acta::class, 'id_acta', 'idacta');
    }
    // Esta relación permite que en el formulario veas el nombre del bien
    public function bien() {
        return $this->belongsTo(Bien::class, 'id_bienes', 'idbienes');
    }
    
}