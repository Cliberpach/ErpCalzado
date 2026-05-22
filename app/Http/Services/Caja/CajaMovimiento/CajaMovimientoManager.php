<?php

namespace App\Http\Services\Caja\CajaMovimiento;

class CajaMovimientoManager
{
    private CajaMovimientoService $service;

    public function __construct()
    {
        $this->service = new CajaMovimientoService();
    }

    public function reporteMovimiento(int $id)
    {
        return $this->service->reporteMovimiento($id);
    }
}
