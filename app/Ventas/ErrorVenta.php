<?php

namespace App\Ventas;

use Illuminate\Database\Eloquent\Model;

class ErrorVenta extends Model
{
    protected $table = 'error_venta';
    protected $fillable = [
        'documento_id',
        'tipo',
        'descripcion',
        'ecxepcion',
    ];
}
