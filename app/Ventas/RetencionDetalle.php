<?php

namespace App\Ventas;

use App\Ventas\Documento\Documento;
use Illuminate\Database\Eloquent\Model;

class RetencionDetalle extends Model
{
    protected $table = 'retencion_detalles';
    protected $fillable = [
        'retencion_id',
        'documento_id',
        'tipoDoc',
        'numDoc',
        'fechaEmision',
        'fechaRetencion',
        'moneda',
        'impTotal',
        'impPagar',
        'impRetenido',

        'moneda_pago',
        'importe_pago',
        'fecha_pago',

        'fecha_tipo_cambio',
        'factor',
        'monedaObj',
        'monedaRef',
    ];

    public function documento()
    {
        return $this->belongsTo('App\Ventas\Documento\Documento', 'documento_id');
    }

    public function retencion()
    {
        return $this->belongsTo(Retencion::class, 'retencion_id');
    }

    protected static function booted()
    {
        static::created(function (RetencionDetalle $detalle) {
            /*$documento = Documento::findOrFail($$detalle->documento_id);
            $impRetenido = $documento->total * ($documento->clienteEntidad->tasa_retencion / 100);
            $impPagar = $documento->total - $impRetenido;
            $documento->total = $impPagar;
            $documento->update();*/
        });
    }
}
