<?php

namespace App\Ventas;

use Illuminate\Database\Eloquent\Model;

class ErrorNota extends Model
{
    protected $table = 'error_nota';
    protected $fillable = [
        'nota_id',
        'tipo',
        'descripcion',
        'ecxepcion',
    ];
}
