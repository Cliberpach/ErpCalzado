<?php

namespace App\Ventas;

use Illuminate\Database\Eloquent\Model;

class DetalleResumen extends Model
{
    protected $table = 'resumenes_detalles';
    protected $guarded = [''];

    public function documento()
    {
        return $this->belongsTo('App\Ventas\Documento\Documento', 'documento_id');
    }
}
