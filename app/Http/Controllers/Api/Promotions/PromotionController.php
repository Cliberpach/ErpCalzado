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
    public function getHomePromotions()
    {
        try {
            $today   = now()->toDateString();
            $baseUrl = url('storage/');

            $promotions = DB::table('promociones as pr')
                ->where('pr.estado', 'ACTIVO')
                ->where(function ($q) use ($today) {
                    $q->whereNull('pr.fecha_inicio')
                      ->orWhere('pr.fecha_inicio', '<=', $today);
                })
                ->where(function ($q) use ($today) {
                    $q->whereNull('pr.fecha_fin')
                      ->orWhere('pr.fecha_fin', '>=', $today);
                })
                ->orderBy('pr.fecha_fin', 'asc')
                ->limit(4)
                ->select(
                    'pr.id',
                    'pr.nombre',
                    'pr.descripcion',
                    'pr.tipo_promocion',
                    'pr.valor',
                    'pr.cantidad_minima',
                    'pr.fecha_fin'
                )
                ->get();

            $ids = $promotions->pluck('id');

            // Total de productos por promoción
            $counts = DB::table('promociones_productos')
                ->whereIn('promocion_id', $ids)
                ->where('estado', 1)
                ->select('promocion_id', DB::raw('COUNT(*) as total'))
                ->groupBy('promocion_id')
                ->get()
                ->keyBy('promocion_id');

            // Primera imagen de producto por promoción
            $images = DB::table('promociones_productos as pp')
                ->join('productos as p', 'p.id', '=', 'pp.producto_id')
                ->whereIn('pp.promocion_id', $ids)
                ->where('pp.estado', 1)
                ->whereNotNull('p.img1_ruta')
                ->orderBy('pp.promocion_id')
                ->orderBy('pp.id')
                ->select('pp.promocion_id', 'p.img1_ruta')
                ->get()
                ->unique('promocion_id')
                ->keyBy('promocion_id');

            $data = $promotions->map(function ($promo) use ($counts, $images, $baseUrl) {
                $img  = isset($images[$promo->id]) ? $images[$promo->id]->img1_ruta : null;
                $ruta = $img ? $baseUrl . '/' . str_replace('storage/', '', $img) : null;

                return [
                    'id'              => $promo->id,
                    'nombre'          => $promo->nombre,
                    'descripcion'     => $promo->descripcion,
                    'tipo_promocion'  => $promo->tipo_promocion,
                    'valor'           => floatval($promo->valor),
                    'cantidad_minima' => (int) $promo->cantidad_minima,
                    'fecha_fin'       => $promo->fecha_fin,
                    'total_productos' => isset($counts[$promo->id]) ? (int) $counts[$promo->id]->total : 0,
                    'imagen'          => $ruta,
                ];
            });

            return response()->json([
                'success' => true,
                'data'    => $data,
            ]);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

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
            // QUERY BASE (PRODUCTOS)
            // =========================
            $query = PromocionProducto::from('promociones_productos as pp')
                ->join('promociones as pr', 'pr.id', '=', 'pp.promocion_id')
                ->join('productos as p', 'p.id', '=', 'pp.producto_id')
                ->join('categorias as ca', 'ca.id', '=', 'p.categoria_id')
                ->join('modelos as mo', 'mo.id', '=', 'p.modelo_id')
                ->join('marcas as ma', 'ma.id', '=', 'p.marca_id')
                ->join('producto_color_tallas as pct', 'pct.producto_id', '=', 'p.id')
                ->where('pct.stock', '>', 0)
                ->where('pct.stock_logico', '>', 0)
                ->where('pct.almacen_id', 1)
                ->where('p.estado', 'ACTIVO')
                ->where('pp.estado', 1)
                ->whereIn('pp.promocion_id', $promotionsIds)
                ->distinct();

            // =========================
            // FILTROS (PRODUCTOS)
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
            // PRODUCTS (SIN DUPLICAR VARIANTS)
            // =========================
            $products = (clone $query)
                ->select(
                    'p.id',
                    'p.nombre',
                    'ca.descripcion as category',
                    'mo.descripcion as model',
                    'ma.marca as brand',
                    'p.precio_venta_1',
                    'p.img1_ruta',
                    'p.img2_ruta',
                    'p.img3_ruta',
                    'p.img4_ruta',
                    'p.img5_ruta'
                )
                ->groupBy(
                    'p.id',
                    'p.nombre',
                    'ca.descripcion',
                    'mo.descripcion',
                    'ma.marca',
                    'p.precio_venta_1',
                    'p.img1_ruta',
                    'p.img2_ruta',
                    'p.img3_ruta',
                    'p.img4_ruta',
                    'p.img5_ruta'
                )
                ->orderBy('p.id', 'desc')
                ->skip($offset)
                ->take($limit)
                ->get();

            // =========================
            // VARIANTS (AMAZON STYLE)
            // =========================
            $variants = DB::table('producto_color_tallas as pct')
                ->join('tallas as t', 't.id', '=', 'pct.talla_id')
                ->join('colores as co', 'co.id', '=', 'pct.color_id')
                ->select(
                    'pct.producto_id as product_id',
                    DB::raw("CONCAT(pct.producto_id, '-', pct.talla_id, '-', pct.color_id) as variant_id"),
                    't.descripcion as size',
                    'co.descripcion as color',
                    DB::raw('SUM(pct.stock) as stock')
                )
                ->where('pct.almacen_id', 1)
                ->where('pct.stock', '>', 0)
                ->groupBy('pct.producto_id', 'pct.talla_id', 'pct.color_id')
                ->get()
                ->groupBy('product_id');

            // attach variants to products
            $products = $products->map(function ($p) use ($variants, $baseUrl) {
                return [
                    'id' => $p->id,
                    'name' => $p->nombre,
                    'category' => $p->category,
                    'model' => $p->model,
                    'brand' => $p->brand,
                    'price' => $p->precio_venta_1,
                    'images' => array_values(array_filter([
                        $p->img1_ruta ? $baseUrl . '/' . str_replace('storage/', '', $p->img1_ruta) : null,
                        $p->img2_ruta ? $baseUrl . '/' . str_replace('storage/', '', $p->img2_ruta) : null,
                        $p->img3_ruta ? $baseUrl . '/' . str_replace('storage/', '', $p->img3_ruta) : null,
                        $p->img4_ruta ? $baseUrl . '/' . str_replace('storage/', '', $p->img4_ruta) : null,
                        $p->img5_ruta ? $baseUrl . '/' . str_replace('storage/', '', $p->img5_ruta) : null,
                    ])),
                    'variants' => $variants[$p->id] ?? []
                ];
            });

            // =========================
            // FILTERS (DINÁMICOS)
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
                ->join('producto_color_tallas as pct2', 'pct2.producto_id', '=', 'p.id')
                ->join('tallas as t', 't.id', '=', 'pct2.talla_id')
                ->distinct()
                ->pluck('t.descripcion');

            $colors = (clone $filtersQuery)
                ->join('producto_color_tallas as pct3', 'pct3.producto_id', '=', 'p.id')
                ->join('colores as co', 'co.id', '=', 'pct3.color_id')
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
