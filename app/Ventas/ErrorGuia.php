<?php

namespace App\Ventas;

use Illuminate\Database\Eloquent\Model;

class ErrorGuia extends Model
{
    protected $table = 'error_guia';
    protected $fillable = [
        'guia_id',
        'tipo',
        'descripcion',
        'ecxepcion',
    ];
}
