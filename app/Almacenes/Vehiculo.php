<?php

namespace App\Almacenes;

use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    protected $table    =   'vehiculos';
    protected $guarded  =   [''];
    public $timestamps  =   true;
}
