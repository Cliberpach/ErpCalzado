<?php

namespace App\Almacenes;

use App\Almacenes\Producto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class LoteProducto extends Model
{
    protected $table = 'lote_productos';
    protected $fillable = [
        'codigo_lote',
        'documento_compra_id',
        'nota_ingreso_id',
        'producto_id',

        'cantidad',
        'cantidad_logica',
        'cantidad_inicial',

        'fecha_vencimiento',
        'fecha_entrega',
        'observacion',

        'confor_almacen',
        'confor_produccion',

        'estado'
    ];

    public $timestamps = true;

    public function producto()
    {
        return $this->belongsTo('App\Almacenes\Producto','producto_id');
    }
    public function productoCliente()
    {
        return $this->belongsTo('App\Almacenes\TipoCliente','producto_id','producto_id');
    }

    public function detalle_nota()
    {
        return $this->hasOne('App\Almacenes\DetalleNotaIngreso','lote_id','id');
    }

    public function detalle_compra()
    {
        return $this->hasOne('App\Compras\Documento\Detalle','lote_id','id');
    }

    public function detalles_venta()
    {
        return $this->hasMany('App\Ventas\Documento\Detalle','lote_id','id');
    }

    public function detalles_salida()
    {
        return $this->hasMany('App\Almacenes\DetalleNotaSalidad','lote_id','id');
    }

    public function documento_compra()
    {
        return $this->belongsTo('App\Compras\Documento\Documento','documento_compra_id','id');
    }

    public function nota()
    {
        return $this->belongsTo('App\Almacenes\NotaIngreso','nota_ingreso_id','id');
    }

    //EVENTO AL CREAR Y AL MODIFICAR
    protected static function booted()
    {
        static::saved(function(LoteProducto $loteProducto){
            //RECORRER DETALLE NOTAS
            $cantidadProductos = LoteProducto::where('producto_id',$loteProducto->producto_id)->where('estado','1')->sum('cantidad');
            //ACTUALIZAR EL STOCK DEL PRODUCTO
            $producto = Producto::findOrFail($loteProducto->producto_id);
            $producto->stock = $cantidadProductos ? $cantidadProductos : 0.00;
            $producto->update();
        });

        static::created(function(LoteProducto $loteProducto){
            //RECORRER DETALLE NOTAS
            $cantidadProductos = LoteProducto::where('producto_id',$loteProducto->producto_id)->where('estado','1')->sum('cantidad');
            //ACTUALIZAR EL STOCK DEL PRODUCTO
            $producto = Producto::findOrFail($loteProducto->producto_id);
            $producto->stock = $cantidadProductos ? $cantidadProductos : 0.00;
            $producto->update();
        });

        static::updated(function(LoteProducto $loteProducto){
            //RECORRER DETALLE NOTAS
            $cantidadProductos = LoteProducto::where('producto_id',$loteProducto->producto_id)->where('estado','1')->sum('cantidad');
            $producto = Producto::findOrFail($loteProducto->producto_id);
            $producto->stock = $cantidadProductos ? $cantidadProductos : 0;
            $producto->update();
        });

        static::deleted(function(LoteProducto $loteProducto){
            //RECORRER DETALLE NOTAS
            $cantidadProductos = LoteProducto::where('producto_id',$loteProducto->producto_id)->where('estado','1')->sum('cantidad');
            //ACTUALIZAR EL STOCK DEL PRODUCTO
            $producto = Producto::findOrFail($loteProducto->producto_id);
            $producto->stock = $cantidadProductos ? $cantidadProductos : 0;
            $producto->update();
        });
    }
}
