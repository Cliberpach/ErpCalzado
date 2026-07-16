<?php

namespace App\Models\Ventas\ReservaWeb;

use App\Almacenes\Talla;
use App\Almacenes\TrasladoDetalle;
use App\Models\Almacenes\Color\Color;
use App\Models\Almacenes\Producto\Producto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReservaWebDetalle extends Model
{
    protected $table = 'reservas_web_detalle';

    protected $fillable = [
        'reserva_web_id',
        'producto_id',
        'color_id',
        'talla_id',
        'cantidad',
        'cantidad_pendiente',
        'precio_venta_1',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    public function talla(): BelongsTo
    {
        return $this->belongsTo(Talla::class, 'talla_id');
    }

    /** Traslados creados desde "Cubrir stock" para completar esta línea. */
    public function traslados(): HasMany
    {
        return $this->hasMany(TrasladoDetalle::class, 'reserva_web_detalle_id');
    }
}
