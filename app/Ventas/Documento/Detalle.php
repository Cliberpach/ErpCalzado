<?php

namespace App\Ventas\Documento;

use App\Almacenes\Kardex;
use App\Almacenes\LoteProducto;
use App\Almacenes\Producto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class Detalle extends Model
{
    protected $table = 'cotizacion_documento_detalles';
    protected $guarded = [''];

    // protected $fillable = [
    //     'documento_id',
    //     // 'lote_id',
    //     'producto_id',
    //     'color_id',
    //     'talla_id',
    //     'codigo_producto',
    //     'unidad',
    //     'nombre_producto',
    //     //'codigo_lote',
    //     'cantidad',
    //     'precio_unitario',
    //     'importe',
    //     // 'precio_inicial',
    //     // 'precio_unitario',
    //     // 'precio_nuevo',
    //     // 'precio_minimo',
    //     // 'descuento',
    //     // 'dinero',
    //     // 'valor_unitario',
    //     // 'valor_venta',
    //     'estado',
    //     'eliminado',
    // ];

    public function detalles()
    {
        return $this->hasMany('App\Ventas\NotaDetalle', 'detalle_id', 'id');
    }

    public function documento()
    {
        return $this->belongsTo('App\Ventas\Documento\Documento');
    }

    public function lote()
    {
        return $this->belongsTo('App\Almacenes\LoteProducto', 'lote_id');
    }

    public function producto()
    {
        return $this->belongsTo('App\Almacenes\Producto');
    }

    // public function productoColorTalla()
    // {
    //     return $this->belongsTo('App\Almacenes\ProductoColorTalla', ['producto_id', 'color_id', 'talla_id'], ['producto_id', 'color_id', 'talla_id']);
    // }

    protected static function booted()
    {
        static::created(function (Detalle $detalle) {

            $producto_color_talla = DB::table('producto_color_tallas')
                                    ->where('producto_id', $detalle->producto_id)
                                    ->where('color_id', $detalle->color_id)
                                    ->where('talla_id', $detalle->talla_id)
                                    ->first();
            

            // $producto = Producto::find($detalle->lote->producto_id);
            // $producto->precio_venta_minimo = $detalle->precio_unitario;
            // $producto->update();
        });
    }

    //     // static::updated(function (Detalle $detalle) {

    //     //     // if($detalle->eliminado == '1')
    //     //     // {
    //     //     //     $lote = LoteProducto::find($detalle->lote_id);
    //     //     //     $lote->cantidad = $lote->cantidad + $detalle->cantidad;
    //     //     //     $lote->cantidad_logica = $lote->cantidad_logica + $detalle->cantidad;
    //     //     //     $lote->update();
    //     //     // }
    //     // });
    // }
}
