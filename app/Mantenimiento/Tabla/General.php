<?php

namespace App\Mantenimiento\Tabla;

use Illuminate\Database\Eloquent\Model;

class General extends Model
{
    protected $table = 'tablas';
    protected $fillable = ['descripcion','simbolo','sigla','editable'];
    public $timestamps = true;

    public function detalles()
    {
        return $this->hasMany('App\Mantenimiento\Tabla\Detalle', 'tabla_id')
        ->where('tabladetalles.estado','=', 'ACTIVO');
    }
    
}
