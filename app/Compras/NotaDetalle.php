<?php

namespace App\Compras;

use App\Compras\Documento\Detalle;
use Illuminate\Database\Eloquent\Model;

class NotaDetalle extends Model
{
    protected $table = 'nota_credito_compras_detalle';
    protected $fillable = [
        'nota_id',
        'detalle_id',
        'producto_id',
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
        return $this->belongsTo('App\Compras\Documento\Detalle','detalle_id','id');
    }

    public function producto()
    {
        return $this->belongsTo('App\Almacenes\Producto','producto_id');
    }

    public function nota_dev(){
        return $this->belongsTo(Nota::class,'nota_id','id');
    }

    protected static function booted()
    {
        static::created(function(NotaDetalle $detalle){

            $sumatoria = NotaDetalle::where('detalle_id',$detalle->detalle_id)->sum('cantidad');
            $detalle_venta = Detalle::findOrFail($detalle->detalle_id);
            /*if($detalle_venta->cantidad == $sumatoria)
            {
                $detalle_venta->estado = 'ANULADO';
                $detalle_venta->update();
            }*/
        });
    }


}
