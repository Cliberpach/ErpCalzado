<?php

namespace App\Http\Services\Almacen\Almacen;

use App\Models\Almacenes\Almacen\Almacen;

class AlmacenManager
{
    private AlmacenService $s_service;

    public function __construct()
    {
        $this->s_service = new AlmacenService();
    }

    public function store(array $data): Almacen
    {
        return $this->s_service->store($data);
    }
}
