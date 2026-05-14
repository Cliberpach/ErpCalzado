<?php

namespace App\Http\Controllers\Api\Promotions;

use App\Http\Controllers\Controller;
use App\Models\Mantenimiento\Promocion\Promocion;
use App\Models\Mantenimiento\Promocion\PromocionProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class PromotionController extends Controller
{
    public function getPromotions(Request $request)
    {
        try {

            $limit  = $request->get('limit', 12);
            $offset = $request->get('offset', 0);

            // filtros
            $filters = $request->get('filters', []);

            $promoFilter    = $filters['promo'] ?? null;
            $categoryFilter = $filters['categories'] ?? [];
            $searchFilter   = $filters['search'] ?? null;
            $sizeFilter     = $filters['sizes'] ?? [];
            $colorFilter    = $filters['colors'] ?? [];
            $brandFilter    = $filters['brands'] ?? [];

            // promociones activas
            $promotions = Promocion::where('estado', 'ACTIVO')
                ->where('fecha_inicio', '<=', now()->toDateString())
                ->where('fecha_fin', '>=', now()->toDateString())
                ->orderBy('fecha_fin', 'asc')
                ->take(5)
                ->get();

            $promotionsIds = $promotions->pluck('id');

            $baseUrl = url('storage/');

            // =========================
            // QUERY BASE (PRODUCTO NIVEL)
            // =========================
            $query = PromocionProducto::from('promociones_productos as pp')
                ->join('promociones as pr', 'pr.id', '=', 'pp.promocion_id')
                ->join('productos as p', 'p.id', '=', 'pp.producto_id')
                ->join('categorias as ca', 'ca.id', '=', 'p.categoria_id')
                ->join('modelos as mo', 'mo.id', '=', 'p.modelo_id')
                ->join('marcas as ma', 'ma.id', '=', 'p.marca_id')

                // solo para validar stock (NO para select final)
                ->join('producto_color_tallas as pct', 'pct.producto_id', '=', 'p.id')

                ->where('pct.stock', '>', 0)
                ->where('pct.stock_logico', '>', 0)
                ->where('pct.almacen_id', 1)

                ->where('p.estado', 'ACTIVO')
                ->where('pp.estado', 1)
                ->whereIn('pp.promocion_id', $promotionsIds)

                ->distinct()

                ->select(
                    'p.id as producto_id',
                    'p.nombre as producto_nombre',
                    'ca.descripcion as categoria_nombre',
                    'mo.descripcion as modelo_nombre',
                    'ma.marca as marca_nombre',
                    'p.precio_venta_1',
                    'p.img1_ruta',
                    'p.img2_ruta',
                    'p.img3_ruta',
                    'p.img4_ruta',
                    'p.img5_ruta'
                );

            // =========================
            // FILTROS
            // =========================

            if ($promoFilter && strtoupper($promoFilter) !== 'TODOS') {
                $query->where('pr.nombre', $promoFilter);
            }

            if (!empty($categoryFilter)) {
                $query->whereIn('ca.descripcion', (array) $categoryFilter);
            }

            if (!empty($brandFilter)) {
                $query->whereIn('ma.marca', (array) $brandFilter);
            }

            if ($searchFilter) {
                $query->where(function ($q) use ($searchFilter) {
                    $q->where('p.nombre', 'LIKE', "%{$searchFilter}%")
                        ->orWhere('mo.descripcion', 'LIKE', "%{$searchFilter}%")
                        ->orWhere('ca.descripcion', 'LIKE', "%{$searchFilter}%");
                });
            }

            // filtros EXISTS (AMAZON STYLE)
            if (!empty($sizeFilter)) {
                $query->whereExists(function ($q) use ($sizeFilter) {
                    $q->select(DB::raw(1))
                        ->from('producto_color_tallas as pct2')
                        ->join('tallas as t', 't.id', '=', 'pct2.talla_id')
                        ->whereColumn('pct2.producto_id', 'p.id')
                        ->whereIn('t.descripcion', (array) $sizeFilter);
                });
            }

            if (!empty($colorFilter)) {
                $query->whereExists(function ($q) use ($colorFilter) {
                    $q->select(DB::raw(1))
                        ->from('producto_color_tallas as pct3')
                        ->join('colores as co', 'co.id', '=', 'pct3.color_id')
                        ->whereColumn('pct3.producto_id', 'p.id')
                        ->whereIn('co.descripcion', (array) $colorFilter);
                });
            }

            // =========================
            // TOTAL
            // =========================
            $total = (clone $query)->count();

            // =========================
            // PRODUCTS
            // =========================
            $products = $query
                ->orderBy('p.id', 'desc')
                ->skip($offset)
                ->take($limit)
                ->get()
                ->map(function ($p) use ($baseUrl) {

                    return [
                        'id' => $p->producto_id,
                        'name' => $p->producto_nombre,
                        'category' => $p->categoria_nombre,
                        'model' => $p->modelo_nombre,
                        'brand' => $p->marca_nombre,
                        'price' => $p->precio_venta_1,

                        'images' => collect([
                            $p->img1_ruta,
                            $p->img2_ruta,
                            $p->img3_ruta,
                            $p->img4_ruta,
                            $p->img5_ruta
                        ])
                            ->filter()
                            ->map(
                                fn($img) =>
                                $baseUrl . '/' . str_replace('storage/', '', $img)
                            )
                            ->values()
                    ];
                });

            // =========================
            // FILTROS DINAMICOS (AMAZON STYLE)
            // =========================

            $filtersQuery = clone $query;

            $categories = (clone $filtersQuery)
                ->select('ca.descripcion')
                ->distinct()
                ->pluck('ca.descripcion');

            $brands = (clone $filtersQuery)
                ->select('ma.marca')
                ->distinct()
                ->pluck('ma.marca');

            $sizes = (clone $filtersQuery)
                ->join('producto_color_tallas as pct', 'pct.producto_id', '=', 'p.id')
                ->join('tallas as t', 't.id', '=', 'pct.talla_id')
                ->distinct()
                ->pluck('t.descripcion');

            $colors = (clone $filtersQuery)
                ->join('producto_color_tallas as pct', 'pct.producto_id', '=', 'p.id')
                ->join('colores as co', 'co.id', '=', 'pct.color_id')
                ->distinct()
                ->pluck('co.descripcion');

            // =========================
            // RESPONSE
            // =========================

            return response()->json([
                'success' => true,
                'data' => [
                    'promotions' => $promotions,
                    'products' => $products,
                    'filters' => [
                        'categories' => $categories,
                        'brands' => $brands,
                        'sizes' => $sizes,
                        'colors' => $colors,
                    ],
                    'meta' => [
                        'total' => $total,
                        'limit' => (int) $limit,
                        'offset' => (int) $offset,
                        'has_more' => ($offset + $limit) < $total
                    ]
                ]
            ]);
        } catch (Throwable $th) {

            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
