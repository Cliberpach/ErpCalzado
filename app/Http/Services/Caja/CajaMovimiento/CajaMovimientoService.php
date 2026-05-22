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
        // ── 1. Queries ────────────────────────────────────────────────────────
        $movimiento  = $this->repository->getMovimientoConRelaciones($id);
        $colaborador = $this->repository->getColaborador($movimiento->colaborador_id);
        $empresa     = $this->repository->getEmpresa();
        $tiposPago   = $this->repository->getTiposPago();

        // IDs de documentos destino de conversión (para resolver serie-correlativo)
        $convertEnIds = $movimiento->detalleMovimientoVentas
            ->pluck('documento.convert_en_id')
            ->filter()
            ->unique()
            ->values()
            ->toArray();
        $docsConvertidosMap = $this->repository->getDocumentosConvertidos($convertEnIds);

        // ── 2. Cálculos (sin tocar la BD) ─────────────────────────────────────
        $calc = new CajaMovimientoCalculate($tiposPago);

        $ventasContado   = $calc->seccionVentasContado($movimiento, $docsConvertidosMap);
        $cobranzas       = $calc->seccionCobranzas($movimiento);
        $egresos         = $calc->seccionEgresos($movimiento);
        $pagosProveedor  = $calc->seccionPagosProveedor($movimiento);
        $conversiones    = $calc->seccionConversiones($movimiento, $docsConvertidosMap);
        $resumenEfectivo = $calc->resumenEfectivo($movimiento);
        $resumenElect    = $calc->resumenElectronico($movimiento);
        $resumenMetodos  = $calc->resumenPorMetodoPago($movimiento);

        // ── 3. Datos de cabecera ───────────────────────────────────────────────
        $cabecera = [
            'cajaNombre'   => $movimiento->caja->nombre,
            'colaborador'  => $colaborador ? $colaborador->nombre : '-',
            'montoInicial' => floatval($movimiento->monto_inicial),
            'fecha'        => date_format($movimiento->created_at, 'd/m/Y'),
            'fechaReporte' => Carbon::now()->format('d/m/Y H:i'),
        ];

        // ── 4. Render PDF ─────────────────────────────────────────────────────
        return PDF::loadview('pos.MovimientoCaja.Reportes.movimientocaja', [
            'empresa'         => $empresa,
            'cabecera'        => $cabecera,
            'ventasContado'   => $ventasContado,
            'cobranzas'       => $cobranzas,
            'egresos'         => $egresos,
            'pagosProveedor'  => $pagosProveedor,
            'conversiones'    => $conversiones,
            'resumenEfectivo' => $resumenEfectivo,
            'resumenElect'    => $resumenElect,
            'resumenMetodos'  => $resumenMetodos,
        ])
            ->setPaper('a4')
            ->setWarnings(false);
    }

    public function datosCierre(int $id): CajaMovimientoDto
    {
        $movimiento  = $this->repository->getMovimientoConRelaciones($id);
        $colaborador = $this->repository->getColaborador($movimiento->colaborador_id);
        $tiposPago   = $this->repository->getTiposPago();

        $calc            = new CajaMovimientoCalculate($tiposPago);
        $resumenMetodos  = $calc->resumenPorMetodoPago($movimiento);
        $resumenEfectivo = $calc->resumenEfectivo($movimiento);
        $resumenElect    = $calc->resumenElectronico($movimiento);

        $dto                 = new CajaMovimientoDto();
        $dto->caja           = $movimiento->caja->nombre;
        $dto->colaborador    = $colaborador ? $colaborador->nombre : '-';
        $dto->montoInicial   = floatval($movimiento->monto_inicial);
        $dto->resumenMetodos = $resumenMetodos;
        $dto->resumenEfectivo = $resumenEfectivo;
        $dto->totalVentaDia    = $resumenElect['totalVentaDia'];
        $dto->ingresos         = (float) array_sum(array_column($resumenMetodos, 'ingresos'));
        $dto->egresos          = (float) (
            array_sum(array_column($resumenMetodos, 'egresosCaja')) +
            array_sum(array_column($resumenMetodos, 'pagosProveedor'))
        );
        $dto->saldo            = $resumenEfectivo['saldoCajaDelDia'];
        $dto->saldoConsolidado = $dto->montoInicial + $dto->ingresos - $dto->egresos;

        return $dto;
    }

    public function ventasNoPagadas(int $movimientoId): array
    {
        return $this->repository->getDocumentosNoPagados($movimientoId);
    }

    public function cerrar(int $movimientoId, float $saldo): void
    {
        $this->repository->cerrarMovimiento($movimientoId, $saldo);
    }
}
