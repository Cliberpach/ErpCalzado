<?php

namespace App\Mantenimiento\Empresa;

use Illuminate\Database\Eloquent\Model;

class Facturacion extends Model
{
    protected $table = 'empresa_facturaciones';
    public $timestamps = true;
    protected $fillable = [
            'empresa_id',
            'ruta_certificado_pfx',
            'nombre_certificado_pfx',
            'contra_certificado',
            'certificado', // CERTIFICADO EN BASE64 DE PDX A PEM
            'sol_user',
            'sol_pass',
            'plan',
            'ambiente',
            'logo',
            'ruta_certificado_pem',
            'token_code',
            'fe_id',
            'estado',
            'sede_id'

        ];

    public function empresa()
    {
        return $this->belongsTo('App\Mantenimiento\Empresa\Empresa');
    }

}
