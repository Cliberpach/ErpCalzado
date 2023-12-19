<?php

namespace App\Compras;

use Illuminate\Database\Eloquent\Model;

class DetalleCuentaProveedor extends Model
{
    protected $table="detalle_cuenta_proveedor";
    protected $fillable=[
        'cuenta_proveedor_id',
        'mcaja_id',
        'fecha',
        'observacion',
        'ruta_imagen',
        'tipo_pago_id',
        'monto',
        'efectivo',
        'importe',
        'saldo'
    ];
    public function cuenta_proveedor()
    {
        return $this->belongsTo(CuentaProveedor::class,'cuenta_proveedor_id');
    }
    public function tipo_pago()
    {
        return $this->belongsTo('App\Ventas\TipoPago','tipo_pago_id');
    }


}
