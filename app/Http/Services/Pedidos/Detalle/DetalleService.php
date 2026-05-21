<?php

namespace App\Http\Services\Pedidos\Detalle;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class DetalleService
{
    private DetalleRepository $d_repository;

    public function __construct()
    {
        $this->d_repository = new DetalleRepository();
    }

    public function queryDetalle(array $filters): Builder
    {
        return $this->d_repository->queryDetalle($filters);
    }

    public function getDetalles(array $filters): Collection
    {
        return $this->d_repository->queryDetalle($filters)->get();
    }
}
