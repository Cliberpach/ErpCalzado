<?php

namespace App\Http\Controllers\Api\Cart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class CartController extends Controller
{
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

            $stock = (int) DB::table('producto_color_tallas')
                ->where('producto_id', $productoId)
                ->where('color_id', $colorId)
                ->where('talla_id', $tallaId)
                ->where('almacen_id', 1)
                ->value('stock_logico') ?? 0;

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
}
