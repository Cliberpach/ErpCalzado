<?php

namespace App\Http\Controllers\Api\Promotions;

use App\Http\Controllers\Controller;
use App\Models\Mantenimiento\Promocion\Promocion;
use App\Models\Mantenimiento\Promocion\PromocionProducto;
use Illuminate\Http\Request;
use Throwable;

class PromotionController extends Controller
{
    public function getPromotions(Request $request)
    {
        try {

            $limit = $request->get('limit', 10);
            $offset = $request->get('offset', 0);
            $promoFilter = $request->get('promo');

            $promotions = Promocion::where('estado', 'ACTIVO')
                ->where('fecha_inicio', '<=', now()->toDateString())
                ->where('fecha_fin', '>=', now()->toDateString())
                ->orderBy('fecha_fin', 'asc')
                ->take(5)
                ->get();

            $promotionsIds = $promotions->pluck('id');

            $baseUrl = url('storage/');

            $query = PromocionProducto::from('promociones_productos as pp')
                ->join('promociones as pr','pr.id','pp.promocion_id')
                ->join('productos as p', 'p.id', 'pp.producto_id')
                ->join('categorias as ca', 'ca.id', 'p.categoria_id')
                ->join('modelos as mo', 'mo.id', 'p.modelo_id')
                ->where('p.estado', 'ACTIVO')
                ->where('pp.estado', 1)
                ->whereIn('pp.promocion_id', $promotionsIds)
                ->select(
                    'pp.promocion_id as promotion_id',
                    'p.nombre as producto_nombre',
                    'ca.descripcion as categoria_nombre',
                    'mo.descripcion as modelo_nombre',
                    'p.precio_venta_1',
                    'p.img1_ruta',
                    'p.img2_ruta',
                    'p.img3_ruta',
                    'p.img4_ruta',
                    'p.img5_ruta'
                );

            if ($promoFilter) {
                $query->where('pr.nombre', $promoFilter);
            }

            $total = $query->count();

            $products = $query
                ->skip($offset)
                ->take($limit)
                ->get()
                ->map(function ($p) use ($baseUrl) {
                    return [
                        'promotion_id'     => $p->promotion_id,
                        'producto_nombre'  => $p->producto_nombre,
                        'categoria_nombre' => $p->categoria_nombre,
                        'modelo_nombre'    => $p->modelo_nombre,
                        'precio_venta_1'   => $p->precio_venta_1,
                        'imagenes' => collect(['img1_ruta', 'img2_ruta', 'img3_ruta', 'img4_ruta', 'img5_ruta'])
                            ->map(fn($f) => $p->$f ? $baseUrl . '/' . str_replace('storage/', '', $p->$f) : null)
                            ->filter()
                            ->values(),
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'PROMOCIONES OBTENIDAS',
                'data' => [
                    'promotions' => $promotions,
                    'products'   => $products,
                    'meta' => [
                        'total'  => $total,
                        'limit'  => $limit,
                        'offset' => $offset
                    ]
                ]
            ]);
        } catch (Throwable $th) {

            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
