<?php

namespace App\Http\Services\Caja\CajaMovimiento;

use App\DetallesMovimientoCaja;
use App\Mantenimiento\Colaborador\Colaborador;
use App\Mantenimiento\Empresa\Empresa;
use App\Pos\MovimientoCaja;
use Illuminate\Support\Facades\DB;

class CajaMovimientoRepository
{
    public function getMovimiento(int $id): MovimientoCaja
    {
        return MovimientoCaja::findOrFail($id);
    }

    public function getUsuarios(int $movimiento_id)
    {
        return DetallesMovimientoCaja::select(
                'u.id',
                'u.usuario',
                'detalles_movimiento_caja.fecha_entrada',
                'detalles_movimiento_caja.fecha_salida'
            )
            ->join('users as u', 'u.id', '=', 'detalles_movimiento_caja.usuario_id')
            ->join('user_persona as up', 'up.user_id', '=', 'u.id')
            ->where('detalles_movimiento_caja.movimiento_id', '=', $movimiento_id)
            ->get();
    }

    public function getColaborador(?int $colaborador_id): ?Colaborador
    {
        return Colaborador::find($colaborador_id);
    }

    public function getRecibos(int $movimiento_id): array
    {
        return DB::select('SELECT rc.*, c.nombre AS cliente_nombre
                            FROM recibos_caja AS rc
                            INNER JOIN clientes AS c ON c.id = rc.cliente_id
                            WHERE rc.movimiento_id = ?', [$movimiento_id]);
    }

    public function getTotalIngresosPorTipoPago(MovimientoCaja $movimiento)
    {
        return obtenerTotalIngresosPorTipoPago($movimiento);
    }

    public function getEmpresa(): Empresa
    {
        return Empresa::first();
    }
}
