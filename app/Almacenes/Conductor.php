<?php

namespace App\Almacenes;

use Illuminate\Database\Eloquent\Model;

class Conductor extends Model
{
    protected $table    =   'conductores';
    protected $guarded  =   [''];
    public $timestamps  =   true;
}
