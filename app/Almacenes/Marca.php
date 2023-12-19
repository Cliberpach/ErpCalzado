<?php

namespace App\Almacenes;

use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $table = 'marcas';
    protected $fillable = ['marca','procedencia','estado'];
    public $timestamps = true;

    public function productos()
    {
        return $this->hasMany('App\Almacenes\Producto');
    }
}
