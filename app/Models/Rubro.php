<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Rubro extends Model {
    protected $table = 'rubros';
    protected $primaryKey = 'idrubros';
    public $timestamps = false;
    protected $fillable = [
        'clasificador_presupuestario', 
        'descripcion'
        ];
}