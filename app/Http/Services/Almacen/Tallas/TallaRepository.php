<?php

namespace App\Http\Services\Almacen\Tallas;

use App\Almacenes\Talla;

class TallaRepository
{
    public function getTallas()
    {
        return Talla::where('estado', 'ACTIVO')->orderBy('id')->get();
    }
}
