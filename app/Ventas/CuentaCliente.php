<?php

namespace App\Ventas;

use App\Ventas\Documento\Documento;
use Illuminate\Database\Eloquent\Model;

class CuentaCliente extends Model
{
    protected $table = 'cuenta_cliente';
    public $timestamps = true;
    protected $fillable = [
            'cotizacion_documento_id',
            'numero_doc',
            'fecha_doc',
            'monto',
            'acta',
            'saldo',
            'estado',
        ];

    public function documento()
    {
        return $this->belongsTo('App\Ventas\Documento\Documento','cotizacion_documento_id','id');
    }

    public function detalles()
    {
        return $this->hasMany('App\Ventas\DetalleCuentaCliente');
    }

    protected static function booted()
    {
        static::updated(function(CuentaCliente $cuenta){
            if($cuenta->estado == 'PAGADO')
            {
                $documento = Documento::find($cuenta->cotizacion_documento_id);
                $documento->estado_pago = 'PAGADA';
                $documento->update();
            }
        });

    }
}
