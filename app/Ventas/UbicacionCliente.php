<?php

namespace App\Ventas;

use Illuminate\Database\Eloquent\Model;

class UbicacionCliente extends Model
{
    protected $table = 'ubicacion_cliente';
    public $timestamps = true;
    protected $fillable = [
        'tienda_id',
        'ver'

    ];
}
