<?php

namespace App\Models\Almacenes\Almacen;

use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    protected $table = 'almacenes';
    protected $fillable = [
        'descripcion',
        'estado',
        'ubicacion',
        'sede_id'
    ];
    public $timestamps = true;
}
