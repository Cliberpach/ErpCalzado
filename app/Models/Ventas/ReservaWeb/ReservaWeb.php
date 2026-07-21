<?php

namespace App\Models\Ventas\ReservaWeb;

use App\Mantenimiento\Sedes\Sede;
use App\Ventas\EnvioVenta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ReservaWeb extends Model
{
    protected $table = 'reservas_web';

    protected $fillable = [
        'codigo_pedido_ecommerce',
        'cliente_nombre',
        'cliente_email',
        'cliente_telefono',
        'cliente_direccion',
        'department_id',
        'province_id',
        'district_id',
        'doc_tipo',
        'doc_numero',
        'desea_factura',
        'razon_social',
        'ruc',
        'almacen_id',
        'sede_recojo_id',
        'total',
        'documento_id',
        'comprobante_numero',
        'metodo_pago',
        'pago_titular',
        'pago_tarjeta_last4',
        'pago_banco',
        'pago_cuotas',
        'pago_referencia',
        'estado',
        'estado_envio',
        'modo_confirmacion',
        'fecha_reserva',
        'fecha_resolucion',
        'usuario_id',
        'motivo_anulacion',
    ];

    protected $casts = [
        'fecha_reserva'    => 'datetime',
        'fecha_resolucion' => 'datetime',
        'desea_factura'    => 'boolean',
    ];

    public function detalle(): HasMany
    {
        return $this->hasMany(ReservaWebDetalle::class, 'reserva_web_id');
    }

    public function sedeRecojo(): BelongsTo
    {
        return $this->belongsTo(Sede::class, 'sede_recojo_id');
    }

    /**
     * Fase 2 de docs/PLANIFICATIONS/2026-07-17-flujo-envio-domicilio.md
     * (ecommerceMerris): no hay FK real, `documento_id` es el vínculo
     * compartido con el `EnvioVenta` que generó esa misma venta (recojo
     * automático o domicilio armado a mano por el staff).
     */
    public function envioVenta(): HasOne
    {
        return $this->hasOne(EnvioVenta::class, 'documento_id', 'documento_id');
    }

    /** Fase recojo en tienda: true si algún detalle todavía tiene stock por cubrir. */
    public function getTienePendienteAttribute(): bool
    {
        return $this->detalle->sum('cantidad_pendiente') > 0;
    }
}
