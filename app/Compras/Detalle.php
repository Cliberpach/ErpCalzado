<?php

namespace App\Compras;

use Illuminate\Database\Eloquent\Model;

class Detalle extends Model
{
    protected $table = 'detalle_ordenes';
    public $timestamps = true;
    protected $fillable = [
        'orden_id',
        'producto_id',
        'color_id',
        'talla_id',
        'cantidad',
        'precio',
        'importe_producto_id'
    ];

    public function orden()
    {
        return $this->belongsTo('App\Compras\Orden');
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
