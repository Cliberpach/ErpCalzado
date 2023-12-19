<?php

namespace App\Almacenes;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    protected $table = 'colores';
    protected $fillable = ['descripcion','estado'];
    public $timestamps = true;
}
