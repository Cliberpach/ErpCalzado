<?php

namespace App\Models\Ventas\Cotizacion;

use Illuminate\Database\Eloquent\Model;

class CotizacionDetalle extends Model
{
    protected $table = 'cotizacion_detalles';

    protected $fillable = [
        'cotizacion_id',
        'producto_id',
        'color_id',
        'talla_id',
        'cantidad',
        'precio_unitario',
        'importe',
        'estado',
        'porcentaje_descuento',
        'precio_unitario_nuevo',
        'importe_nuevo',
        'monto_descuento',
        'almacen_id',
        'almacen_nombre',
        'producto_nombre',
        'color_nombre',
        'talla_nombre',
        'tipo'
    ];
}
