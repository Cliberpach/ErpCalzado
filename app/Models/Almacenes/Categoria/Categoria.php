<?php

namespace App\Models\Almacenes\Categoria;

use Illuminate\Database\Eloquent\Model;


class Categoria extends Model
{
    protected $table = 'categorias';
    protected $fillable = [
        'descripcion',
        'estado',
        'tipo',
        'img_ruta',
        'img_nombre',
        'mostrar_en_web'
    ];
    public $timestamps = true;
}
