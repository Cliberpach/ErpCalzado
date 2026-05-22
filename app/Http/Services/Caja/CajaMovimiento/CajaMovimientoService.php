<?php

namespace App\Http\Services\Caja\CajaMovimiento;

use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;

class CajaMovimientoService
{
    private CajaMovimientoRepository $repository;

    public function __construct()
    {
        $this->repository = new CajaMovimientoRepository();
    }

    public function reporteMovimiento(int $id)
    {
        $movimiento = $this->repository->getMovimiento($id);
        $usuarios   = $this->repository->getUsuarios($id);
        $colaborador = $this->repository->getColaborador($movimiento->colaborador_id);
        $recibos    = $this->repository->getRecibos($id);
        $totalIngresosPorTipoPago = $this->repository->getTotalIngresosPorTipoPago($movimiento);
        $empresa    = $this->repository->getEmpresa();
        $fecha      = Carbon::now()->toDateString();

        return PDF::loadview('pos.MovimientoCaja.Reportes.movimientocaja', [
            'movimiento'               => $movimiento,
            'empresa'                  => $empresa,
            'fecha'                    => $fecha,
            'usuarios'                 => $usuarios,
            'totalIngresosPorTipoPago' => $totalIngresosPorTipoPago,
            'recibos'                  => $recibos,
            'colaborador'              => $colaborador,
        ])
            ->setPaper('a4')
            ->setWarnings(false);
    }
}
