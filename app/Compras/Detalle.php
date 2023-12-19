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
        'cantidad',
        'precio',
    ];

    public function orden()
    {
        return $this->belongsTo('App\Compras\Orden');
    }
    
    public function producto()
    {
        return $this->belongsTo('App\Almacenes\Producto');
    }
}
