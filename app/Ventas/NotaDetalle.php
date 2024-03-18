<?php

namespace App\Ventas;

use App\Almacenes\Kardex;
use App\Almacenes\MovimientoNota;
use App\Ventas\Documento\Detalle;
use Illuminate\Database\Eloquent\Model;

class NotaDetalle extends Model
{
    protected $table = 'nota_electronica_detalle';
    protected $fillable = [
        'nota_id',
        'detalle_id',
        'codProducto',
        'unidad',
        'descripcion',
        'cantidad',

        'mtoBaseIgv',
        'porcentajeIgv',
        'igv',
        'tipAfeIgv',

        'totalImpuestos',
        'mtoValorVenta',
        'mtoValorUnitario',
        'mtoPrecioUnitario',
    ];

    public function detalle()
    {
        return $this->belongsTo('App\Ventas\Documento\Detalle','detalle_id','id');
    }

    public function nota_dev(){
        return $this->belongsTo(Nota::class,'nota_id','id');
    }

    protected static function booted()
    {
        // static::created(function(NotaDetalle $detalle){
        //     //KARDEX
        //     $kardex = new Kardex();
        //     $kardex->origen = 'INGRESO';
        //     $kardex->numero_doc = 'NOTA-'.$detalle->nota_dev->id;
        //     $kardex->fecha = $detalle->nota_dev->fechaEmision;
        //     $kardex->cantidad = $detalle->cantidad;
        //     $kardex->producto_id = $detalle->detalle->lote->producto_id;
        //     $kardex->descripcion = 'DEVOLUCIÓN';
        //     $kardex->precio = $detalle->mtoPrecioUnitario;
        //     $kardex->importe = $detalle->mtoPrecioUnitario * $detalle->cantidad;
        //     $kardex->stock = $detalle->detalle->lote->producto->stock;
        //     $kardex->save();

        //     $sumatoria = NotaDetalle::where('detalle_id',$detalle->detalle_id)->sum('cantidad');
        //     $detalle_venta = Detalle::findOrFail($detalle->detalle_id);
        //     if($detalle_venta->cantidad == $sumatoria)
        //     {
        //         $detalle_venta->estado = 'ANULADO';
        //         $detalle_venta->update();
        //     }

            
        // });
    }


}
