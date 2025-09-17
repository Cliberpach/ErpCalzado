<?php

namespace App\Mantenimiento\Cuenta;

use Illuminate\Database\Eloquent\Model;

class Cuenta extends Model
{
    protected $table = 'cuentas';
    protected $fillable = [
        'banco_id',
        'banco_nombre',
        'nro_cuenta',
        'moneda',
        'cci',
        'celular',
        'eliminador_id',
        'registrador_id',
        'eliminador_nombre',
        'registrador_nombre',
        'titular',
        'estado'
    ];
}
