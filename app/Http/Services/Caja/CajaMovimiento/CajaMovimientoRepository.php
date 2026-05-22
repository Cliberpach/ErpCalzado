<?php

namespace App\Http\Services\Caja\CajaMovimiento;

use App\DetallesMovimientoCaja;
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
            "SELECT cd.id, cd.serie, cd.correlativo, tv.descripcion AS tipo
             FROM cotizacion_documento cd
             LEFT JOIN tabla_detalles tv ON tv.id = cd.tipo_venta_id
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
}
