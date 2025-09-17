<?php

namespace App\Models\Kardex\Cuenta;

use Illuminate\Database\Eloquent\Model;

class KardexCuenta extends Model
{
    protected $table    = 'kardex_cuentas';

     protected $fillable = [
        'cuenta_bancaria_id',
        'venta_id',
        'egreso_id',
        'pago_cliente_id',
        'pago_proveedor_id',
        'registrador_id',
        'registrador_nombre',
        'metodo_pago_id',
        'metodo_pago_nombre',
        'fecha_registro',
        'documento',
        'banco_abreviatura',
        'nro_cuenta',
        'monto',
        'tipo_documento',
        'tipo_operacion'
    ];
}
