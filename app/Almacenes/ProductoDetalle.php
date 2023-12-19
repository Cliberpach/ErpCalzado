<?php

namespace App\Almacenes;

use Illuminate\Database\Eloquent\Model;

class ProductoDetalle extends Model
{
    protected $table = 'producto_detalles';
    protected $fillable = [
        'producto_id',
        'articulo_id',
        'cantidad',
        'peso',
        'observacion',
        'estado'
    ];

    public function producto()
    {
        return $this->belongsTo('App\Almacenes\Producto');
    }

    public function articulo()
    {
        return $this->belongsTo('App\Compras\Articulo');
    }


}
