<?php

namespace App\Almacenes;

use Illuminate\Database\Eloquent\Model;


class Categoria extends Model
{
    protected $table = 'categorias';
    protected $fillable = ['descripcion','estado'];
    public $timestamps = true;
}
