<?php

namespace App\Almacenes;

use Illuminate\Database\Eloquent\Model;

class MovimientoNota extends Model
{
    protected $table = 'movimiento_nota';
    protected $fillable = [
        'id',
        'orden_produccion_detalle_id',
        'almacen_inicio_id',
        'almacen_final_id',
        // 'lote_id',
        'cantidad',
        'observacion',
        'usuario_id',
        'nota_id',
        'movimiento',
        'producto_id',
        'color_id',
        'talla_id'
    ];
    public $timestamps = true;
}
