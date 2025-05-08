<?php

namespace App\Almacenes;

use Illuminate\Database\Eloquent\Model;

class NotaSalidad extends Model
{
    protected $table    =   'nota_salidad';
    protected $guarded  =   [''];

    

    public $timestamps = true;

    public function detalles()
    {
        return $this->hasMany('App\Almacenes\DetalleNotaSalidad','nota_salidad_id');
    }

   
}
