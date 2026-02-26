<?php

namespace App\Almacenes;

use Illuminate\Database\Eloquent\Model;


class Categoria extends Model
{
    protected $table = 'categorias';
    protected $fillable = [
        'descripcion',
        'estado',
        'tipo',
        'img_ruta',
        'img_nombre'
    ];
    public $timestamps = true;
}
