<?php

namespace App\Almacenes;

use Illuminate\Database\Eloquent\Model;

class MovimientoNota extends Model
{
    protected $table    =   'movimiento_nota';
    
    protected $guarded  =   [''];

    public $timestamps  =   true;
}
