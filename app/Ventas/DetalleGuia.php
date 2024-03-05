<?php

namespace App\Ventas;

use App\Almacenes\Producto;
use Illuminate\Database\Eloquent\Model;

class DetalleGuia extends Model
{
    protected $table = 'guia_detalles';
    protected $fillable = [
        'guia_id',
        'producto_id',
        'color_id',
        'talla_id',
        'lote_id',
        'codigo_producto',
        'cantidad',
        'nombre_producto',
        'nombre_modelo',
        'nombre_color',
        'nombre_talla',
        'unidad',
    ];

    public function lote()
    {
        return $this->belongsTo('App\Almacenes\LoteProducto', 'lote_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

}
