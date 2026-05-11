<?php

namespace App\Models\Almacenes\Producto;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table    = 'productos';

    protected $fillable = [

        'categoria_id',
        'marca_id',
        'modelo_id',
        'almacen_id',

        'codigo',
        'nombre',
        'descripcion',
        'medida',
        'codigo_barra',

        'stock_minimo',

        'precio_compra',
        'precio_venta_1',
        'precio_venta_2',
        'precio_venta_3',
        'precio_venta_4',

        'igv',
        'facturacion',
        'estado',

        'costo',
        'tipo',

        'img1_ruta',
        'img1_nombre',

        'img2_ruta',
        'img2_nombre',

        'img3_ruta',
        'img3_nombre',

        'img4_ruta',
        'img4_nombre',

        'img5_ruta',
        'img5_nombre',

        'mostrar_en_web',

        'is_featured',
        'is_sale',
        'is_outlet',
    ];
}
