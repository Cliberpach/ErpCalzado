<?php

namespace App\Almacenes;

use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    protected $table = 'almacenes';
    protected $fillable = ['descripcion','estado','ubicacion'];
    public $timestamps = true;
}
