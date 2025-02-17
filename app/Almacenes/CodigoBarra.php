<?php

namespace App\Almacenes;

use Illuminate\Database\Eloquent\Model;

class CodigoBarra extends Model
{
    protected $table    = 'codigos_barra';
    protected $guarded  = [''];
    public $timestamps  = true;
}
