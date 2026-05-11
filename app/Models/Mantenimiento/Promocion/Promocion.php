<?php

namespace App\Models\Mantenimiento\Promocion;

use Illuminate\Database\Eloquent\Model;

class Promocion extends Model
{
    protected $table = 'promociones';

    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo_promocion',
        'valor',
        'cantidad_minima',
        'fecha_inicio',
        'fecha_fin',
        'estado',
    ];

    public $timestamps = true;
}
