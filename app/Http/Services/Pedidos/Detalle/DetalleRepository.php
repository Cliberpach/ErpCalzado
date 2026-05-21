<?php

namespace App\Http\Services\Pedidos\Detalle;

use App\Ventas\PedidoDetalle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class DetalleRepository
{
    public function queryDetalle(array $filters): Builder
    {
        $estado      = $filters['pedido_detalle_estado'] ?? null;
        $cliente_id  = $filters['cliente_id'] ?? null;
        $modelo_id   = $filters['modelo_id'] ?? null;
        $producto_id = $filters['producto_id'] ?? null;

        $query = PedidoDetalle::from('pedidos_detalles as pd')
            ->select(
                DB::raw("CONCAT('PE-', p.id) as pedido_codigo"),
                'pd.orden_produccion_id',
                'p.id as pedido_id',
                'p.created_at as pedido_fecha',
                'p.cliente_id',
                'p.cliente_nombre',
                'c.documento as cliente_doc',
                'c.telefono_movil as cliente_telefono',
                'p.user_nombre as vendedor_nombre',
                'p.fecha_propuesta',
                'prod.modelo_id',
                'pd.producto_id',
                'pd.color_id',
                'pd.talla_id',
                'm.descripcion as modelo_nombre',
                'pd.producto_nombre',
                'pd.color_nombre',
                'pd.talla_nombre',
                DB::raw("ROUND(pd.cantidad, 2) as cantidad"),
                DB::raw("ROUND(pd.precio_unitario_nuevo, 2) as precio_unitario_nuevo"),
                DB::raw("ROUND(pd.importe_nuevo, 2) as importe_nuevo"),
                DB::raw("ROUND(pd.cantidad_atendida, 2) as cantidad_atendida"),
                DB::raw("ROUND(pd.cantidad_pendiente, 2) as cantidad_pendiente"),
                'pd.cantidad_enviada',
                'pd.cantidad_devuelta',
                'pd.cantidad_fabricacion',
                'p.pedido_nro as pedido_name_id'
            )
            ->join('pedidos as p', 'pd.pedido_id', '=', 'p.id')
            ->join('productos as prod', 'prod.id', '=', 'pd.producto_id')
            ->join('modelos as m', 'm.id', '=', 'prod.modelo_id')
            ->join('clientes as c', 'c.id', '=', 'p.cliente_id');

        if (!empty($estado)) {
            if ($estado === 'PENDIENTE') {
                $query->where('pd.cantidad_pendiente', '>', 0)
                      ->where('p.estado', '!=', 'FINALIZADO');
            }
            if ($estado === 'ATENDIDO') {
                $query->where('pd.cantidad_pendiente', '=', 0);
            }
            if ($estado === 'FABRICACION') {
                $query->where('pd.cantidad_fabricacion', '>', 0);
            }
        }

        if (!empty($cliente_id)) {
            $query->where('p.cliente_id', $cliente_id);
        }

        if (!empty($modelo_id)) {
            $query->where('prod.modelo_id', $modelo_id);
        }

        if (!empty($producto_id)) {
            $query->where('pd.producto_id', $producto_id);
        }

        return $query->orderBy('p.id', 'desc');
    }
}
