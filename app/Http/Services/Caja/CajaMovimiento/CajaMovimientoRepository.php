<?php

namespace App\Http\Services\Caja\CajaMovimiento;

use App\DetallesMovimientoCaja;
use App\Http\Services\Ventas\ReglasVenta;
use App\Mantenimiento\Colaborador\Colaborador;
use App\Mantenimiento\Empresa\Empresa;
use App\Pos\MovimientoCaja;
use App\Ventas\TipoPago;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CajaMovimientoRepository
{
    public function getMovimientoConRelaciones(int $id): MovimientoCaja
    {
        return MovimientoCaja::with([
            'caja',
            'detalleMovimientoVentas.documento.notas',
            'detalleMovimientoVentas.documento.clienteEntidad',
            'detalleCuentaCliente.cuenta_cliente.documento.clienteEntidad',
            'detalleMoviemientoEgresos.egreso',
            'detalleCuentaProveedor.cuenta_proveedor.documento.proveedor',
        ])->findOrFail($id);
    }

    /**
     * Sólo lo que hace falta para armar el nombre del archivo del PDF, sin
     * arrastrar las relaciones pesadas de getMovimientoConRelaciones().
     */
    public function getMovimientoParaNombre(int $id): MovimientoCaja
    {
        return MovimientoCaja::select('id', 'fecha_apertura')->findOrFail($id);
    }

    public function getColaborador(?int $colaboradorId): ?Colaborador
    {
        return Colaborador::find($colaboradorId);
    }

    public function getEmpresa(): Empresa
    {
        return Empresa::first();
    }

    public function getTiposPago(): Collection
    {
        return TipoPago::where('estado', 'ACTIVO')->orderBy('id')->get();
    }

    /**
     * Dado un array de IDs (convert_en_id), devuelve un mapa id => [serie, correlativo, tipo]
     * para mostrar el documento destino en conversiones.
     */
    public function getDocumentosConvertidos(array $ids): array
    {
        if (empty($ids)) return [];

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $rows = DB::select(
            "SELECT cd.id, cd.serie, cd.correlativo,
                    NULLIF(cd.tipo_venta_nombre, '') AS tipo
             FROM cotizacion_documento cd
             WHERE cd.id IN ({$placeholders})",
            $ids
        );

        $map = [];
        foreach ($rows as $row) {
            $map[$row->id] = [
                'serie'       => $row->serie,
                'correlativo' => $row->correlativo,
                'tipo'        => $row->tipo ?? '-',
            ];
        }
        return $map;
    }

    public function getDocumentosNoPagados(int $movimientoId): array
    {
        return DB::select(
            'SELECT cd.serie, cd.correlativo
             FROM detalle_movimiento_venta dmv
             INNER JOIN cotizacion_documento cd ON cd.id = dmv.cdocumento_id
             WHERE cd.estado_pago = "PENDIENTE"
               AND dmv.mcaja_id = ?
               AND cd.convert_de_id IS NULL
               AND cd.estado = "ACTIVO"
             GROUP BY cd.serie, cd.correlativo',
            [$movimientoId]
        );
    }

    public function cerrarMovimiento(int $movimientoId, float $saldo): void
    {
        $movimiento = MovimientoCaja::with('caja')->findOrFail($movimientoId);

        $movimiento->estado_movimiento = 'CIERRE';
        $movimiento->fecha_cierre      = date('Y-m-d H:i:s');
        $movimiento->monto_final       = $saldo;
        $movimiento->save();

        $movimiento->caja->estado_caja = 'CERRADA';
        $movimiento->caja->save();

        DetallesMovimientoCaja::where('movimiento_id', $movimientoId)
            ->update(['fecha_salida' => date('Y-m-d H:i:s')]);
    }

    // ─── PRODUCTOS VENDIDOS EN LA CAJA ────────────────────────────────────────

    /**
     * Reglas de filtrado de VENTAS CONTADO de una caja.
     *
     * Punto único de verdad: replican exactamente lo que hace
     * CajaMovimientoCalculate::seccionVentasContado(), para que el desglose de
     * productos cuadre con el TOTAL de VENTAS CONTADO del mismo reporte.
     *
     *   condicion_id = 1        -> sólo contado
     *   convert_de_id IS NULL   -> el destino de una conversión NO se cuenta;
     *                              la venta se imputa a la nota de venta de origen.
     *                              Sin esto se contarían dos veces los mismos pares.
     *   estado <> 'ANULADO'     -> las anuladas no suman (igual que el TOTAL)
     *   cobrar = 'SI'           -> igual que el TOTAL
     *
     * Las dos reglas que no son propias de la caja (convert_de_id y estado)
     * viven en ReglasVenta, compartidas con el resto de informes de ventas. Lo
     * que sí es propio de este reporte —la caja, el contado y el cobrar— se
     * queda aquí.
     *
     * Usa los alias dmv (detalle_movimiento_venta) y cd (cotizacion_documento).
     * El único parámetro que espera es el id del movimiento de caja.
     */
    private function filtroVentasContado(): string
    {
        return " dmv.mcaja_id = ?
                 AND cd.condicion_id = 1
                 AND " . ReglasVenta::documentoVendible('cd') . "
                 AND dmv.cobrar = 'SI' ";
    }

    /**
     * Importe real de una línea de detalle (alias d).
     *
     * La regla vive en ReglasVenta::importeLinea(), compartida con el resto de
     * informes de ventas; aquí sólo se fija el alias que usa este repositorio.
     */
    private function importeLinea(): string
    {
        return ' ' . ReglasVenta::importeLinea('d') . ' ';
    }

    /**
     * Líneas de producto vendidas en la caja, agregadas por
     * categoría + modelo + color + talla.
     *
     * Consulta ÚNICA que alimenta tanto el resumen por categoría del reporte de
     * caja como el reporte de productos, para no duplicar las reglas de filtrado.
     * El pivote de tallas y los subtotales se hacen en PHP.
     *
     * La categoría se lee del maestro actual (productos -> categorias) porque el
     * detalle de venta no la guarda desnormalizada. No se filtra por estado de la
     * categoría: una venta histórica puede apuntar a una categoría ya anulada y
     * debe seguir apareciendo.
     *
     * @return array<int, \stdClass>
     */
    public function getDetalleProductosCaja(int $mcajaId): array
    {
        $sql = "SELECT COALESCE(NULLIF(cat.descripcion, ''), '(SIN CATEGORIA)') AS categoria,
                       COALESCE(NULLIF(d.nombre_modelo, ''), '(SIN MODELO)')    AS modelo,
                       COALESCE(NULLIF(d.nombre_color, ''),  '(SIN COLOR)')     AS color,
                       COALESCE(NULLIF(d.nombre_talla, ''),  'S/T')             AS talla,
                       SUM(d.cantidad)               AS pares,
                       SUM(" . $this->importeLinea() . ") AS monto
                FROM   detalle_movimiento_venta dmv
                JOIN   cotizacion_documento           cd ON cd.id = dmv.cdocumento_id
                JOIN   cotizacion_documento_detalles  d  ON d.documento_id = cd.id
                LEFT   JOIN productos  pr  ON pr.id  = d.producto_id
                LEFT   JOIN categorias cat ON cat.id = pr.categoria_id
                WHERE " . $this->filtroVentasContado() . "
                  AND  " . ReglasVenta::lineaActiva('d') . "
                GROUP  BY categoria, modelo, color, talla";

        return DB::select($sql, [$mcajaId]);
    }

    /**
     * Piezas que explican la diferencia entre el TOTAL de VENTAS CONTADO y la suma
     * de los productos, para el cuadre del reporte.
     *
     * Verificado sobre datos reales:
     *   total_pagar = SUM(importe de línea) + monto_envio + monto_embalaje
     * El envío y el embalaje se cobran en la cabecera y no tienen línea de detalle.
     * Además hay ventas sin ninguna línea de detalle utilizable (todas anuladas o
     * eliminadas): existen, no son anecdóticas, y se informan aparte.
     *
     * @return array<string, mixed>
     */
    public function getConciliacionCaja(int $mcajaId): array
    {
        $sinDetalle = "NOT EXISTS (
                           SELECT 1 FROM cotizacion_documento_detalles d
                           WHERE d.documento_id = v.id
                             AND " . ReglasVenta::lineaActiva('d') . ")";

        $sql = "SELECT ROUND(SUM(v.monto_envio), 2)    AS envio,
                       ROUND(SUM(v.monto_embalaje), 2) AS embalaje,
                       ROUND(SUM(v.total_pagar), 2)    AS total_contado,
                       SUM(CASE WHEN {$sinDetalle} THEN 1 ELSE 0 END) AS docs_sin_detalle,
                       ROUND(SUM(CASE WHEN {$sinDetalle} THEN v.total_pagar ELSE 0 END), 2)
                            AS monto_sin_detalle
                FROM (
                    SELECT DISTINCT cd.id, cd.monto_envio, cd.monto_embalaje, cd.total_pagar
                    FROM   detalle_movimiento_venta dmv
                    JOIN   cotizacion_documento cd ON cd.id = dmv.cdocumento_id
                    WHERE " . $this->filtroVentasContado() . "
                ) v";

        $filas = DB::select($sql, [$mcajaId]);
        $r     = isset($filas[0]) ? $filas[0] : null;

        return array(
            'envio'           => $r ? floatval($r->envio) : 0.0,
            'embalaje'        => $r ? floatval($r->embalaje) : 0.0,
            'totalContado'    => $r ? floatval($r->total_contado) : 0.0,
            'docsSinDetalle'  => $r ? intval($r->docs_sin_detalle) : 0,
            'montoSinDetalle' => $r ? floatval($r->monto_sin_detalle) : 0.0,
        );
    }
}
