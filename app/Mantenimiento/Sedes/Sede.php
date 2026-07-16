<?php

namespace App\Mantenimiento\Sedes;

use App\Almacenes\Almacen;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sede extends Model
{
    protected $table    = 'empresa_sedes';
    public $timestamps  = true;
    protected $guarded  = [''];

    public function almacenes(): HasMany
    {
        return $this->hasMany(Almacen::class, 'sede_id');
    }
}
