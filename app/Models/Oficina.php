<?php 
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Oficina extends Model {
    protected $table = 'oficinas';
    protected $primaryKey = 'idoficinas';
    public $timestamps = false;
        protected $fillable = [
        'descripcion',
    ];
}