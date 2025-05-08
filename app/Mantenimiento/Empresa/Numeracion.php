<?php

namespace App\Mantenimiento\Empresa;

use Illuminate\Database\Eloquent\Model;

class Numeracion extends Model
{
    protected $table    =   'empresa_numeracion_facturaciones';
    public $timestamps  =   true;
    
    public $guarded     =   [''];

    public function empresa()
    {
        return $this->belongsTo('App\Mantenimiento\Empresa\Empresa','empresa_id');
    }

    public function comprobanteDescripcion(): string
    {
        $comprobante = tipos_venta()->where('id', $this->tipo_comprobante)->first();
        if (is_null($comprobante))
            return "-";
        else
            return $comprobante->descripcion;
    }


}
