<?php

namespace App\Mantenimiento\TipoPago;

use Illuminate\Database\Eloquent\Model;

class TipoPago extends Model
{
    protected $table = 'tipos_pago';
    protected $fillable = [
        'descripcion',
        'simbolo',
        'editable'
    ];
}
