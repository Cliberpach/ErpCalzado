<?php

namespace App\Ventas;

use Illuminate\Database\Eloquent\Model;

class Retencion extends Model
{
    protected $table = 'retencions';
    protected $fillable = [
        'documento_id',
        'serie',
        'correlativo',
        'fechaEmision',

        'ruc',
        'razonSocial',
        'nombreComercial',
        'direccion_empresa',
        'provincia_empresa',
        'departamento_empresa',
        'distrito_empresa',
        'ubigeo_empresa',

        'tipoDoc',
        'numDoc',
        'rznSocial',
        'direccion_provedor',
        'provincia_proveedor',
        'departamento_proveedor',
        'distrito_proveedor',
        'ubigeo_proveedor',

        'observacion',
        'impRetenido',
        'impPagado',
        'regimen',
        'tasa'
    ];

    public function documento()
    {
        return $this->belongsTo('App\Ventas\Documento\Documento', 'documento_id');
    }
}
