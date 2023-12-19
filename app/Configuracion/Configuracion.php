<?php

namespace App\Configuracion;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'configuracion';
    public $timestamps = true;
    protected $fillable = [
            'slug',
            'descripcion',
            'propiedad'
        ];
}
