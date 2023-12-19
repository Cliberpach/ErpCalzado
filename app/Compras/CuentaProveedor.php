<?php

namespace App\Compras;

use Illuminate\Database\Eloquent\Model;

class CuentaProveedor extends Model
{
    protected $table = 'cuenta_proveedor';
    public $timestamps = true;
    protected $fillable = [
            'compra_documento_id',
            'acta',
            'saldo',
            'monto',
            'estado',
        ];

    public function documento()
    {
        return $this->belongsTo('App\Compras\Documento\Documento','compra_documento_id');
    }
    public function detallePago(){
        return $this->hasMany(DetalleCuentaProveedor::class,'cuenta_proveedor_id');
    }

}
