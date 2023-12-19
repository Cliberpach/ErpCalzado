<?php

namespace App\Ventas;

use App\Pos\MovimientoCaja;
use Illuminate\Database\Eloquent\Model;

class DetalleCuentaCliente extends Model
{
    protected $table="detalle_cuenta_cliente";
    protected $fillable=[
        'cuenta_cliente_id',
        'fecha',
        'observacion',
        'tipo_pago_id',
        'mcaja_id',
        'efectivo',
        'importe',
        'monto',
    ];
    public function cuenta_cliente()
    {
        return $this->belongsTo(CuentaCliente::class,'cuenta_cliente_id');
    }

    // protected static function booted()
    // {
    //     static::created(function(DetalleCuentaCliente $detalle){

    //         $caja = MovimientoCaja::find($detalle->mcaja_id);
    //         $caja->monto_final = $caja->monto_final + $detalle->monto;
    //         $caja->update();            
    //     });
    // }
}
