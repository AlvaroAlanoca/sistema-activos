<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BienBaja extends Model
{
    use HasFactory;

    // 1. Le decimos el nombre exacto de tu tabla
    protected $table = 'bienes_baja';

    // 2. Le indicamos cuál es tu llave primaria personalizada
    protected $primaryKey = 'idbienes_baja';

    // 3. Los campos que Filament podrá llenar
    protected $fillable = [
        'idbienes',
        'motivo_baja',
        'fecha_aprobacion',
    ];

    // 4. La relación: Una Baja pertenece a un Bien
    public function bien()
    {
        return $this->belongsTo(Bien::class, 'idbienes', 'idbienes'); // Ajusta 'idbienes' si la llave primaria en tu modelo Bien es diferente
    }
}