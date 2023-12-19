<?php

namespace App\Mantenimiento\Tabla;

use Illuminate\Database\Eloquent\Model;

class Detalle extends Model
{
    protected $table = 'tabladetalles';
    public $timestamps = true;
    protected $fillable = [
        'tabla_id',
        'nombre',
        'descripcion',
        'simbolo',
        'parametro',
        'operacion',
        'tipo',
        'editable',
        'estado',
    ];

    public function tabla()
    {
        return $this->belongsTo('App\Mantenimiento\Tabla\General', 'tabla_id');
    }

    public function descripcion(): int
    {
        $detalle = tipo_clientes()
            ->where('id', $this->id)
            ->first();
        if (is_null($detalle)) {
            return '-';
        } else {
            return $detalle->descripcion;
        }
    }
}
