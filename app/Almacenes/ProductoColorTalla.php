<?php

namespace App\Almacenes;

use Illuminate\Database\Eloquent\Model;
use App\Almacenes\Color;
use App\Almacenes\Talla;

class ProductoColorTalla extends Model
{
    protected $table = 'producto_color_tallas';
    protected $primaryKey = 'producto_id';
    protected $guarded = [];


    public function producto()
    {
        return $this->belongsTo('App\Almacenes\Producto')->with('modelo');
    }


    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    public function talla()
    {
        return $this->belongsTo(Talla::class, 'talla_id');
    }

}
