<?php

namespace App\Http\Controllers\Api\Cart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class CartController extends Controller
{
    /**
     * Suma stock_logico de un producto/color/talla en TODA la red — solo
     * almacenes PRINCIPAL de sedes activas (no cuenta REGALOS/FALLADO/
     * CAMBIOS/CUADRE DE STOCK, que no son stock vendible). Antes esto
     * filtraba a `almacen_id=1` (CENTRAL) fijo — con recojo en tienda ya
     * reservando directo de la sede elegida
     * (docs/PLANIFICATIONS/2026-07-15-plan-recojo-tienda.md), ese filtro
     * ocultaba/prometía stock incorrecto. Ver
     * docs/PLANIFICATIONS/2026-07-15-plan-pendientes.md Fase 1.2.
     */
    private function sumarStockLogicoRed(int $productoId, int $colorId, int $tallaId): int
    {
        return (int) DB::table('producto_color_tallas as pct')
            ->join('almacenes as a', function ($join) {
                $join->on('a.id', '=', 'pct.almacen_id')
                    ->where('a.tipo_almacen', 'PRINCIPAL')
                    ->where('a.estado', 'ACTIVO');
            })
            ->where('pct.producto_id', $productoId)
            ->where('pct.color_id', $colorId)
            ->where('pct.talla_id', $tallaId)
            ->sum('pct.stock_logico');
    }

    /**
     * Valida si hay stock suficiente para agregar al carrito.
     * GET /api/cart/validate-stock?producto_id=X&color_id=Y&talla_id=Z&cantidad=N
     */
    public function validateStock(Request $request)
    {
        try {
            $productoId = $request->get('producto_id');
            $colorId    = $request->get('color_id');
            $tallaId    = $request->get('talla_id');
            $cantidad   = max(1, (int) $request->get('cantidad', 1));

            if (!$productoId || !$colorId || !$tallaId) {
                return response()->json([
                    'success'    => true,
                    'disponible' => false,
                    'stock'      => 0,
                    'message'    => 'producto_id, color_id y talla_id son requeridos',
                ]);
            }

            $stock = $this->sumarStockLogicoRed((int) $productoId, (int) $colorId, (int) $tallaId);

            return response()->json([
                'success'    => true,
                'disponible' => $stock >= $cantidad,
                'stock'      => $stock,
            ]);
        } catch (Throwable $th) {
            return response()->json([
                'success'    => false,
                'disponible' => false,
                'message'    => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Mismo chequeo que validateStock() pero para todos los items del
     * carrito en UNA sola llamada — evita N requests (y N golpes al rate
     * limiter) desde /carrito de ecommerceMerris.
     *
     * POST /api/cart/validate-stock-lote
     * body: { items: [{ cart_key, producto_id, color_id, talla_id, cantidad }, ...] }
     */
    public function validateStockLote(Request $request)
    {
        try {
            $items = $request->input('items', []);
            if (!is_array($items)) {
                return response()->json(['success' => false, 'data' => []], 422);
            }

            $data = [];
            foreach ($items as $item) {
                $productoId = $item['producto_id'] ?? null;
                $colorId    = $item['color_id']    ?? null;
                $tallaId    = $item['talla_id']    ?? null;
                $cantidad   = max(1, (int) ($item['cantidad'] ?? 1));

                $stock = 0;
                if ($productoId && $colorId && $tallaId) {
                    $stock = $this->sumarStockLogicoRed((int) $productoId, (int) $colorId, (int) $tallaId);
                }

                $data[] = [
                    'cart_key'   => $item['cart_key'] ?? null,
                    'disponible' => $stock >= $cantidad,
                    'stock'      => $stock,
                ];
            }

            return response()->json(['success' => true, 'data' => $data]);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }
}
