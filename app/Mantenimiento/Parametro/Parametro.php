<?php

namespace App\Mantenimiento\Parametro;

use Illuminate\Database\Eloquent\Model;

class Parametro extends Model
{
    protected $table = 'parametros';
    public $timestamps = true;
    protected $fillable = [
            'http',
            'token',
            'usuario_proveedor',
            'contra_proveedor'
    ];
}
