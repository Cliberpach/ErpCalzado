<?php

namespace App\Almacenes;

use Illuminate\Database\Eloquent\Model;

class ProductoColorTalla extends Model
{
    protected $table = 'producto_color_tallas';
    protected $primaryKey = 'producto_id';
    protected $guarded = [];


    public function producto()
    {
        return $this->belongsTo('App\Almacenes\Producto');
    }
}
