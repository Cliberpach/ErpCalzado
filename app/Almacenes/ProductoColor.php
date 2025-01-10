<?php

namespace App\Almacenes;

use Illuminate\Database\Eloquent\Model;

class ProductoColor extends Model
{
    protected $table        = 'producto_colores';
    protected $primaryKey   = 'producto_id';
    protected $guarded      = [''];
}
