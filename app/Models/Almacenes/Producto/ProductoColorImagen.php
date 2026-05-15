<?php

namespace App\Models\Almacenes\Producto;

use Illuminate\Database\Eloquent\Model;

class ProductoColorImagen extends Model
{
    protected $table = 'producto_color_imagenes';

    protected $fillable = [
        'producto_id',
        'color_id',
        'img_route',
        'img_name',
        'orden',
    ];
}
