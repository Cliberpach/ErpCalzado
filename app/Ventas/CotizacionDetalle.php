<?php

namespace App\Ventas;

use Illuminate\Database\Eloquent\Model;

class CotizacionDetalle extends Model
{
    protected $table = 'cotizacion_detalles';
    // protected $fillable = [
    //     'cotizacion_id',
    //     'producto_id',
    //     'cantidad',
    //     'descuento',
    //     'dinero',
    //     'precio_inicial',
    //     'precio_nuevo',
    //     'valor_unitario',
    //     'precio_unitario',
    //     'valor_venta',
    //     'estado'
    // ];
    protected $guarded = [];

    public function cotizacion()
    {
        return $this->belongsTo('App\Ventas\Cotizacion');
    }

    public function producto()
    {
        return $this->belongsTo('App\Almacenes\Producto');
    }
    public function color()
    {
        return $this->belongsTo('App\Almacenes\Color');
    }
    public function talla()
    {
        return $this->belongsTo('App\Almacenes\Talla');
    }
}
