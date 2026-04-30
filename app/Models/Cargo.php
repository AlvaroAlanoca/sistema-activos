<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Cargo extends Model {
    protected $table = 'cargos';
    protected $primaryKey = 'idcargos';
    public $timestamps = false;
    protected $fillable = [
        'descripcion',
    ];
}