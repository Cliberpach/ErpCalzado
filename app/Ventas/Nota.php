<?php

namespace App\Ventas;

use App\Mantenimiento\Condicion;
use Illuminate\Database\Eloquent\Model;
use App\Mantenimiento\Tabla\Detalle as TablaDetalle;
use App\Pos\DetalleMovimientoEgresosCaja;
use App\Pos\Egreso;
use App\Ventas\DetalleCuentaCliente;
use App\Ventas\CuentaCliente;
use App\Ventas\Documento\Detalle;
use App\Ventas\Documento\Documento;

class Nota extends Model
{
    protected $table    = 'nota_electronica';
    protected $guarded  = [''];

    public function documento()
    {
        return $this->belongsTo('App\Ventas\Documento\Documento','documento_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    protected static function booted()
    {
        static::created(function(Nota $nota){
            //CREAR LOTE PRODUCTO
            $condicion = Condicion::find($nota->documento->condicion_id);
            if (strtoupper($condicion->descripcion) == 'CREDITO' || strtoupper($condicion->descripcion) == 'CRÉDITO') 
            {            
                if($nota->documento->cuenta)
                {
                    $monto_notas = Nota::where('documento_id',$nota->documento_id)->sum('mtoImpVenta');
                    $monto  = $nota->documento->total - $monto_notas;
                    $cuenta_cliente = CuentaCliente::find($nota->documento->cuenta->id);
                    if($monto > 0)
                    {
                        $cuenta_cliente->monto = $monto;
                    }
                    else
                    {
                        $cuenta_cliente->monto = 0.00;
                        $cuenta_cliente->estado='PAGADO';
                    }

                    $monto_saldo = DetalleCuentaCliente::where('cuenta_cliente_id',$cuenta_cliente->id)->sum('monto');
                    if($monto - $monto_saldo > 0)
                    {
                        $cuenta_cliente->saldo = $monto - $monto_saldo;
                    }
                    else
                    {
                        $cuenta_cliente->saldo = 0.00;
                        $cuenta_cliente->estado='PAGADO';
                    }

                    $cuenta_cliente->update();
                }
            }

            $documento  = Documento::find($nota->documento->id);
            $detalles   = Detalle::where('documento_id', $nota->documento->id)->get();
            $cont = 0;

            foreach($detalles as $detalle)
            {
                if($detalle->cantidad == $detalle->detalles->sum('cantidad'))
                {
                    $cont = $cont + 1;
                }
            }

            if(count($detalles) == $cont)
            {
                $documento->sunat = '2';
                $documento->update();
            }
        });

    }
}
