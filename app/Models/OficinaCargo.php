<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OficinaCargo extends Model {
    protected $table = 'oficinas_cargos';
    protected $primaryKey = 'idoficinas_cargos';

    protected $fillable = [
        'id_oficinas',
        'id_cargos',
    ];
    
    public function oficina() { 
        return $this->belongsTo(Oficina::class, 'id_oficinas', 'idoficinas'); 
    }
    public function cargo() { 
    return $this->belongsTo(Cargo::class, 'id_cargos', 'idcargos'); 
    }
}