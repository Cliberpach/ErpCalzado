<?php

namespace App\Models\Almacenes\Marca;

use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $table = 'marcas';
    protected $fillable = [
        'marca',
        'procedencia',
        'estado',
        'tipo'
    ];
    public $timestamps = true;
}
