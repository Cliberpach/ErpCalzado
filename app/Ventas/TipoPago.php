<?php

namespace App\Ventas;

use Illuminate\Database\Eloquent\Model;

class TipoPago extends Model
{
    protected $table = 'tipos_pago';
    protected $fillable = [
        //DATOS DE LA EMPRESA
        'descripcion',
        'simbolo',
        'editable',
        'estado'
    ];



    public function documentos()
    {
        return $this->hasMany('App\Ventas\Documento\Documento','tipo_pago_id');
    }
}
