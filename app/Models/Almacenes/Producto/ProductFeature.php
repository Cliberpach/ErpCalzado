<?php

namespace App\Models\Almacenes\Producto;

use Illuminate\Database\Eloquent\Model;

class ProductFeature extends Model
{
    protected $table    = 'product_features';

    protected $fillable = [
        'product_id',
        'title',
        'icon',
        'description',
        'sort_order',
        'status',
    ];
}
