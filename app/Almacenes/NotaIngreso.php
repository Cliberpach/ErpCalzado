<?php

namespace App\Almacenes;

use Illuminate\Database\Eloquent\Model;

class NotaIngreso extends Model
{
    protected $table    = 'nota_ingreso';
    protected $guarded  =   [''];

    public $timestamps = true;

    public function detalles()
    {
        return $this->hasMany('App\Almacenes\DetalleNotaIngreso','nota_ingreso_id');
    }

    public function lotes()
    {
        return $this->hasMany('App\Almacenes\LoteProducto','nota_ingreso_id');
    }
}
