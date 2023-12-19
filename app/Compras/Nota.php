<?php

namespace App\Compras;

use App\Mantenimiento\Condicion;
use Illuminate\Database\Eloquent\Model;
use App\Mantenimiento\Tabla\Detalle as TablaDetalle;
use App\Compras\DetalleCuentaProveedor;
use App\Compras\CuentaProveedor;
use App\Compras\Documento\Detalle;
use App\Compras\Documento\Documento;

class Nota extends Model
{
    protected $table = 'nota_credito_compras';
    protected $fillable = [
        'documento_id',
        'tipDoc',
        'numDocfectado',
        'desMotivo',

        'tipoDoc',
        'fechaEmision',
        'tipoMoneda',

        //PROVEEDOR
        'cod_tipo_documento_proveedor',
        'tipo_documento_proveedor',
        'documento_proveedor',
        'direccion_proveedor',
        'proveedor',

        'correlativo',
        'serie',

        'ruta_comprobante_archivo',
        'nombre_comprobante_archivo',

        'mtoOperGravadas',
        'mtoIGV',
        'totalImpuestos',
        'mtoImpVenta',

        'code',
        'value',
        'estado',

        'user_id',
    ];

    public function documento()
    {
        return $this->belongsTo('App\Compras\Documento\Documento','documento_id');
    }

    protected static function booted()
    {
        static::created(function(Nota $nota){
            //CREAR LOTE PRODUCTO
            $condicion = Condicion::find($nota->documento->condicion_id);

            $nota->documento->total_pagar = $nota->documento->total - $nota->documento->notas->sum("mtoImpVenta");
            $nota->documento->update();
            
            if (strtoupper($condicion->descripcion) == 'CREDITO' || strtoupper($condicion->descripcion) == 'CRÉDITO') 
            {            
                if($nota->documento->cuenta)
                {
                    $monto_notas = Nota::where('documento_id',$nota->documento_id)->sum('mtoImpVenta');
                    $monto  = $nota->documento->total - $monto_notas;
                    $cuenta_proveedor = CuentaProveedor::find($nota->documento->cuenta->id);
                    if($monto > 0)
                    {
                        $cuenta_proveedor->monto = $monto;
                    }
                    else
                    {
                        $cuenta_proveedor->monto = 0.00;
                        $cuenta_proveedor->estado='PAGADO';
                    }

                    $monto_saldo = DetalleCuentaProveedor::where('cuenta_proveedor_id',$cuenta_proveedor->id)->sum('monto');
                    if($monto - $monto_saldo > 0)
                    {
                        $cuenta_proveedor->saldo = $monto - $monto_saldo;
                    }
                    else
                    {
                        $cuenta_proveedor->saldo = 0.00;
                        $cuenta_proveedor->estado='PAGADO';
                    }

                    $cuenta_proveedor->update();
                }
            }

            $documento = Documento::find($nota->documento->id);
            $detalles = Detalle::where('documento_id', $nota->documento->id)->get();
            $cont = 0;

            foreach($detalles as $detalle)
            {
                if($detalle->cantidad == $detalle->detalles->sum('cantidad'))
                {
                    $cont = $cont + 1;
                }
            }
        });

    }
}
