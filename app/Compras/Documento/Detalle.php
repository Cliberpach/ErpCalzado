<?php

namespace App\Compras\Documento;

use App\Almacenes\Kardex;
use App\Almacenes\LoteProducto;
use App\Almacenes\Producto;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
//MOVIMIENTOS
use App\Movimientos\MovimientoAlmacen;

class Detalle extends Model
{
    protected $table = 'compra_documento_detalles';
    public $timestamps = true;
    protected $fillable = [
        'id',
        'documento_id',
        'producto_id',
        'codigo_producto',
        'descripcion_producto',
        'presentacion_producto',
        'medida_producto',
        'cantidad',

        'precio_mas_igv_soles',
        'precio_mas_igv_dolares',

        'precio',
        'precio_inicial',
        'costo_flete',

        'precio_soles',
        'precio_inicial_soles',
        'costo_flete_soles',

        'precio_dolares',
        'precio_inicial_dolares',
        'costo_flete_dolares',

        'lote',
        'lote_id',
        'fecha_vencimiento'
    ];

    public function detalles()
    {
        return $this->hasMany('App\Compras\NotaDetalle', 'detalle_id', 'id');
    }

    public function documento()
    {
        return $this->belongsTo('App\Compras\Documento\Documento');
    }

    public function producto()
    {
        return $this->belongsTo('App\Almacenes\Producto');
    }

    public function fechaFormateada()
    {
        $fecha = Carbon::createFromFormat('Y-m-d', $this->fecha_vencimiento)->format('d/m/Y');
        return $fecha;
    }

    public function loteProducto()
    {
        return $this->belongsTo('App\Almacenes\LoteProducto', 'lote_id');
    }

    protected static function booted()
    {
        static::created(function (Detalle $detalle) {
            $lote = new LoteProducto();
            $lote->compra_documento_id = $detalle->documento->id;
            $lote->codigo_lote = $detalle->lote;
            $lote->producto_id = $detalle->producto_id;
            $lote->cantidad = $detalle->cantidad;
            $lote->cantidad_logica = $detalle->cantidad;
            $lote->cantidad_inicial = $detalle->cantidad;
            $lote->fecha_vencimiento = $detalle->fecha_vencimiento;
            $lote->fecha_entrega = $detalle->documento->fecha_entrega;
            $lote->observacion = 'DOC. COMPRA';
            $lote->estado = '1';
            $lote->save();

            $detalle->lote_id = $lote->id;
            $detalle->update();

            //MOVIMIENTO
            $producto = Producto::findOrFail($detalle->producto_id);
            $producto->precio_compra = $detalle->precio_soles;
            $producto->update();

            $movimiento = new MovimientoAlmacen();
            $movimiento->almacen_final_id = $detalle->producto->almacen->id;
            $movimiento->cantidad = $detalle->cantidad;
            $movimiento->nota = 'COMPRA';
            $movimiento->observacion = $producto->codigo . ' - ' . $producto->descripcion;
            $movimiento->usuario_id = auth()->user()->id;
            $movimiento->movimiento = 'INGRESO';
            $movimiento->producto_id = $detalle->producto_id;
            $movimiento->lote_id = $lote->id;
            $movimiento->compra_documento_id = $detalle->documento_id; //DOCUMENTO DE COMPRA
            $movimiento->save();

            //KARDEX
            $kardex = new Kardex();
            $kardex->origen = 'COMPRA';
            $kardex->numero_doc = $detalle->documento->numero_doc;
            $kardex->fecha = $detalle->documento->fecha_emision;
            $kardex->cantidad = $detalle->cantidad;
            $kardex->producto_id = $detalle->producto_id;
            $kardex->descripcion = 'PROVEEDOR: ' . $detalle->documento->proveedor->descripcion;
            $kardex->precio = $detalle->precio;
            $kardex->importe = $detalle->precio * $detalle->cantidad;
            $kardex->stock = $producto->stock;
            $kardex->save();
        });

        static::updated(function (Detalle $detalle) {
            //ANULAR LOTE producto
            if ($detalle->estado == 'ANULADO') {
                $lote = LoteProducto::find($detalle->lote_id);
                if ($lote) {
                    $lote->estado = '0';
                    $lote->update();
                }
            }
        });
    }
}
