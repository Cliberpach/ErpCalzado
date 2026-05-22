<?php

namespace App\Http\Services\Almacen\Almacen;

use App\Models\Almacenes\Almacen\Almacen;

class AlmacenRepository
{
    public function store(array $dto): Almacen
    {
        return Almacen::create($dto);
    }
}
