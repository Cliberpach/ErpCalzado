<?php

namespace App\Http\Services\Caja\CajaMovimiento;

use App\Http\Services\Caja\CajaMovimiento\CajaMovimientoDto;

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

    public function reporteProductos(int $id)
    {
        return $this->service->reporteProductos($id);
    }

    public function nombreArchivoReporte(int $id, string $tipo): string
    {
        return $this->service->nombreArchivoReporte($id, $tipo);
    }

    public function formatoNombreArchivo($ruc, string $tipo, int $id, $fechaApertura): string
    {
        return $this->service->formatoNombreArchivo($ruc, $tipo, $id, $fechaApertura);
    }

    public function datosCierre(int $id): CajaMovimientoDto
    {
        return $this->service->datosCierre($id);
    }

    public function ventasNoPagadas(int $movimientoId): array
    {
        return $this->service->ventasNoPagadas($movimientoId);
    }

    public function cerrar(int $movimientoId, float $saldo): void
    {
        $this->service->cerrar($movimientoId, $saldo);
    }
}
