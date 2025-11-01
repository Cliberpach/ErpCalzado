<?php

namespace App\Models\Ventas\Cotizacion;

use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    protected $table    = 'cotizaciones';

    protected $fillable = [
        'empresa_id',
        'cliente_id',
        'condicion_id',
        'fecha_documento',
        'fecha_atencion',
        'sub_total',
        'total_igv',
        'total',
        'igv_check',
        'igv',
        'moneda',
        'registrador_id',
        'estado',
        'monto_embalaje',
        'monto_envio',
        'total_pagar',
        'porcentaje_descuento',
        'monto_descuento',
        'sede_id',
        'almacen_id',
        'almacen_nombre',
        'registrador_nombre',
        'venta_id',
        'venta_serie',
        'venta_correlativo',
        'cliente_nombre',
        'pedido_id',
        'telefono'
    ];
}
