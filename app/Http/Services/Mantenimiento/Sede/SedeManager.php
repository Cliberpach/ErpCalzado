<?php

namespace App\Http\Services\Mantenimiento\Sede;

use App\Mantenimiento\Empresa\Numeracion;
use App\Mantenimiento\Sedes\Sede;

class SedeManager
{
    private SedeService $s_service;

    public function __construct()
    {
        $this->s_service = new SedeService();
    }

    public function store(array $data): Sede
    {
        return $this->s_service->store($data);
    }

    public function update(array $data, int $id): Sede
    {
        return $this->s_service->update($data, $id);
    }

    public function storeNumeracion(array $data): Numeracion
    {
        return $this->s_service->storeNumeracion($data);
    }
}
