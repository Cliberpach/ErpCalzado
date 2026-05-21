<?php

namespace App\Http\Services\Pedidos\Detalle;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class DetalleManager
{
    private DetalleService $d_service;

    public function __construct()
    {
        $this->d_service = new DetalleService();
    }

    public function queryDetalle(array $filters): Builder
    {
        return $this->d_service->queryDetalle($filters);
    }

    public function getDetalles(array $filters): Collection
    {
        return $this->d_service->getDetalles($filters);
    }
}
