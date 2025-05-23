<?php

namespace App\Almacenes;

use Illuminate\Database\Eloquent\Model;

class Kardex extends Model
{
    protected $table    =   'kardex';
    protected $guarded  =   ['']; 
    public $timestamps  =   true;

    public function producto()
    {
        return $this->belongsTo(Producto::class,'producto_id','id');
    }

    public function productoColorTalla()
    {
        return $this->belongsTo(ProductoColorTalla::class, [
            'producto_id',
            'color_id',
            'talla_id',
        ], [
            'producto_id',
            'color_id',
            'talla_id',
        ]);
    }
}
