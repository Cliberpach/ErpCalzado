<?php

namespace App\Ventas;

use App\Almacenes\Kardex;
use App\Almacenes\MovimientoNota;
use App\Ventas\Documento\Detalle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


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
        'producto_id',
        'color_id',
        'talla_id'
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
        static::created(function(NotaDetalle $detalle){
            
            $producto_color_talla = DB::table('producto_color_tallas')
            ->where('producto_id', $detalle->producto_id)
            ->where('color_id', $detalle->color_id)
            ->where('talla_id', $detalle->talla_id)
            ->first();


            //KARDEX
            $kardex                 =   new Kardex();
            $kardex->origen         =   'INGRESO';
            $kardex->numero_doc     =   'NOTA-'.$detalle->nota_dev->id;
            $kardex->fecha          =   $detalle->nota_dev->fechaEmision;
            $kardex->cantidad       =   $detalle->cantidad;
            //$kardex->producto_id  =   $detalle->detalle->lote->producto_id;
            $kardex->producto_id    =   $detalle->producto_id;
            $kardex->color_id       =   $detalle->color_id;
            $kardex->talla_id       =   $detalle->talla_id;
            $kardex->descripcion    =   'DEVOLUCIÓN';
            $kardex->precio         =   $detalle->mtoPrecioUnitario;
            $kardex->importe        =   $detalle->mtoPrecioUnitario * $detalle->cantidad;
            //$kardex->stock        =   $detalle->detalle->lote->producto->stock;
            $kardex->stock          =   $producto_color_talla->stock;

            $kardex->save();

            $sumatoria           = NotaDetalle::where('detalle_id',$detalle->detalle_id)->sum('cantidad');
            $detalle_venta       = Detalle::findOrFail($detalle->detalle_id);
            if($detalle_venta->cantidad == $sumatoria)
            {
                 $detalle_venta->estado = 'ANULADO';
                 $detalle_venta->update();
            }

            
        });
    }


}
