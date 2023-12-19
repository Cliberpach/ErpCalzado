<?php

namespace App\Movimientos;

use Illuminate\Database\Eloquent\Model;

class MovimientoAlmacen extends Model
{
    protected $table = 'movimiento_almacenes';
    public $timestamps = true;
    protected $fillable = [
            'almacen_inicio_id',
            'almacen_final_id',
            'cantidad',
            'nota',
            'observacion',
            'usuario_id',
            'movimiento',
            'producto_id',
            'lote_id',
            'documento_compra_id',
        ];
}
