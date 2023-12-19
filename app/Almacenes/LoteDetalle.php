<?php

namespace App\Almacenes;

use Illuminate\Database\Eloquent\Model;

class LoteDetalle extends Model
{
    protected $table = 'lote_detalle';
    protected $fillable = [
        'detalle_id',
        'lote_id',
        'cantidad',
        'estado'
    ];
    public $timestamps = true;

    public function lotes()
    {
        return $this->hasMany('App\Almacenes\LoteProducto','lote_id');
    }
}
