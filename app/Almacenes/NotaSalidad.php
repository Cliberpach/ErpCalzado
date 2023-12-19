<?php

namespace App\Almacenes;

use Illuminate\Database\Eloquent\Model;

class NotaSalidad extends Model
{
    protected $table = 'nota_salidad';
    protected $fillable = [
        'numero',
        'fecha',
        'origen',
        'destino',
        'observacion',
        'usuario',
        'estado'
    ];
    public $timestamps = true;

    public function detalles()
    {
        return $this->hasMany('App\Almacenes\DetalleNotaSalidad','nota_salidad_id');
    }
}
