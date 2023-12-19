<?php

namespace App\Mantenimiento;

use Illuminate\Database\Eloquent\Model;

class Condicion extends Model
{
    protected $table = 'condicions';
    protected $fillable = [
        'descripcion',
        'slug',
        'tabladetalle_id',
        'dias',
        'opcional'
    ];
}
