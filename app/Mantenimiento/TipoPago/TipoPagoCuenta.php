<?php

namespace App\Mantenimiento\TipoPago;

use Illuminate\Database\Eloquent\Model;

class TipoPagoCuenta extends Model
{
    protected $table = 'tipo_pago_cuentas';
    protected $fillable = [
        'tipo_pago_id',
        'cuenta_id ',
    ];
}
