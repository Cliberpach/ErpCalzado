<?php

namespace App\Mantenimiento\Empresa;

use Illuminate\Database\Eloquent\Model;

class Banco extends Model
{
    protected $table = 'banco_empresas';
    public $timestamps = true;
    protected $fillable = [
            'empresa_id',
            'descripcion',
            'tipo_moneda',
            'num_cuenta',
            'cci',
            'itf'
        ];

    public function empresa()
    {
        return $this->belongsTo('App\Mantenimiento\Empresa\Empresa');
    }

}
