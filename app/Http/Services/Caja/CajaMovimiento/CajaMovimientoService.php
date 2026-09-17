<?php

namespace App\Http\Services\Caja\CajaMovimiento;

use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;

class CajaMovimientoService
{
    private CajaMovimientoRepository $repository;

    /** Sufijo que distingue cada reporte dentro del nombre del archivo. */
    public const REPORTE_DINERO = 'Dinero';
    public const REPORTE_CANTIDADES = 'Cantidades';

    public function __construct()
    {
        $this->repository = new CajaMovimientoRepository();
    }

    /**
     * Nombre que Chrome debe sugerir al guardar el PDF, sin extensión:
     * <RUC>_<Tipo>_Caja<ID>_<AAAA-MM-DD>. La fecha es la de APERTURA de la
     * caja, no la del día en que se imprime.
     */
    public function nombreArchivoReporte(int $id, string $tipo): string
    {
        return $this->formatoNombreArchivo(
            $this->repository->getEmpresa()->ruc,
            $tipo,
            $id,
            $this->repository->getMovimientoParaNombre($id)->fecha_apertura
        );
    }

    /**
     * Arma el nombre y le quita todo lo que no sea ASCII seguro: sin espacios,
     * tildes ni signos, para que ningún navegador ni sistema de archivos lo
     * recorte o lo escape.
     *
     * Es pública para que el listado pueda nombrar muchas filas con un único
     * RUC ya leído, en vez de consultar la empresa una vez por fila.
     */
    public function formatoNombreArchivo($ruc, string $tipo, int $id, $fechaApertura): string
    {
        $limpiar = function ($valor) {
            return preg_replace('/[^A-Za-z0-9]/', '', (string) $valor);
        };

        return $limpiar($ruc)
            . '_' . $limpiar($tipo)
            . '_Caja' . $id
            . '_' . Carbon::parse($fechaApertura)->format('Y-m-d');
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
            'tituloDocumento' => $this->formatoNombreArchivo(
                $empresa->ruc, self::REPORTE_DINERO, $id, $movimiento->fecha_apertura
            ),
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

    /**
     * Reporte de productos vendidos en la caja: categoría -> modelo -> color,
     * con una columna por talla. Va en A4 horizontal porque la tabla es ancha.
     *
     * Reutiliza exactamente las mismas consultas y reglas de filtrado que el
     * resumen por categoría del reporte de caja, así los dos PDF siempre dan
     * las mismas cifras.
     */
    public function reporteProductos(int $id)
    {
        $movimiento  = $this->repository->getMovimientoConRelaciones($id);
        $colaborador = $this->repository->getColaborador($movimiento->colaborador_id);
        $empresa     = $this->repository->getEmpresa();

        $calc      = new CajaMovimientoCalculate($this->repository->getTiposPago());
        $productos = $calc->seccionProductos(
            $this->repository->getDetalleProductosCaja($id),
            $this->repository->getConciliacionCaja($id)
        );

        $cabecera = [
            'cajaNombre'   => $movimiento->caja->nombre,
            'colaborador'  => $colaborador ? $colaborador->nombre : '-',
            'montoInicial' => floatval($movimiento->monto_inicial),
            'fecha'        => date_format($movimiento->created_at, 'd/m/Y'),
            'fechaReporte' => Carbon::now()->format('d/m/Y H:i'),
        ];

        return PDF::loadview('pos.MovimientoCaja.Reportes.productoscaja', [
            'empresa'         => $empresa,
            'tituloDocumento' => $this->formatoNombreArchivo(
                $empresa->ruc, self::REPORTE_CANTIDADES, $id, $movimiento->fecha_apertura
            ),
            'cabecera'        => $cabecera,
            'productos'       => $productos,
        ])
            ->setPaper('a4', 'landscape')
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
