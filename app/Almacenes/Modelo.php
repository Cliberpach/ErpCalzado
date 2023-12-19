<?php

namespace App\Almacenes;

use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    protected $table = 'modelos';
    protected $fillable = ['descripcion','estado'];
    public $timestamps = true;
}
