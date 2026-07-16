<?php

namespace App\Almacenes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrasladoDetalle extends Model
{
    protected $table    =   'traslados_detalle';
    protected $guarded  =   [''];
    public $timestamps  =   true;

    public function traslado(): BelongsTo
    {
        return $this->belongsTo(Traslado::class, 'traslado_id');
    }
}
