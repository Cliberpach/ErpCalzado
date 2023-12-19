<?php

namespace App\Almacenes;

use Illuminate\Database\Eloquent\Model;

class Talla extends Model
{
    protected $table = 'tallas';
    protected $fillable = ['descripcion','estado'];
    public $timestamps = true;
}
