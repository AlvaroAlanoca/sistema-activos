<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Rubro extends Model {
    protected $table = 'rubros';
    protected $primaryKey = 'idrubros';

    protected $fillable = [
        'clasificador_presupuestario', 
        'descripcion',
        'codigo_rubro'
        ];
}