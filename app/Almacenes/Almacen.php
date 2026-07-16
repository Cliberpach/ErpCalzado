<?php

namespace App\Almacenes;

use App\Mantenimiento\Sedes\Sede;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Almacen extends Model
{
    protected $table = 'almacenes';
    protected $fillable = ['descripcion','estado','ubicacion'];
    public $timestamps = true;

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }
}
