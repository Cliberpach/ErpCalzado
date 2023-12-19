<?php

namespace App\Compras;

use Illuminate\Database\Eloquent\Model;

class Orden extends Model
{
    protected $table = 'ordenes';
    public $timestamps = true;
    protected $fillable = [
            'fecha_emision',
            'fecha_entrega',
            'empresa_id',
            'proveedor_id',
            'modo_compra',
            'condicion_id',
            'moneda',
            'observacion',
            'igv',
            'igv_check',
            'tipo_cambio',

            'sub_total',
            'total_igv',
            'total',

            'estado',

            'enviado',
            'usuario_id'
        ];

    public function empresa()
    {
        return $this->belongsTo('App\Mantenimiento\Empresa\Empresa');
    }

    public function proveedor()
    {
        return $this->belongsTo('App\Compras\Proveedor');
    }

    public function usuario()
    {
        return $this->belongsTo('App\User','usuario_id');
    }
}
