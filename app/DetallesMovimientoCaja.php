<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetallesMovimientoCaja extends Model
{
   
    protected $table="detalles_movimiento_caja";
    protected $fillable=[
        'usuario_id','movimiento_id'
    ];
    public $timestamps = false;
}
