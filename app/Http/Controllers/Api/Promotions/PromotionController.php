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

            $limit  = $request->get('limit', 12);
            $offset = $request->get('offset', 0);

            // FILTROS
            $promoFilter     = $request->get('promo');
            $categoryFilter  = $request->get('categoria');
            $searchFilter    = $request->get('search');
            $sizeFilter      = $request->get('talla');
            $colorFilter     = $request->get('color');   // 👈 nuevo
            $brandFilter     = $request->get('marca');   // 👈 nuevo

            // PROMOCIONES ACTIVAS
            $promotions = Promocion::where('estado', 'ACTIVO')
                ->where('fecha_inicio', '<=', now()->toDateString())
                ->where('fecha_fin', '>=', now()->toDateString())
                ->orderBy('fecha_fin', 'asc')
                ->take(5)
                ->get();

            $promotionsIds = $promotions->pluck('id');

            $baseUrl = url('storage/');

            // QUERY BASE
            $query = PromocionProducto::from('promociones_productos as pp')

                ->join('promociones as pr', 'pr.id', '=', 'pp.promocion_id')

                ->join('productos as p', 'p.id', '=', 'pp.producto_id')

                ->join('categorias as ca', 'ca.id', '=', 'p.categoria_id')

                ->join('modelos as mo', 'mo.id', '=', 'p.modelo_id')

                ->join('marcas as ma', 'ma.id', '=', 'p.marca_id')

                ->join('producto_color_tallas as pct', 'pct.producto_id', '=', 'p.id')

                ->join('tallas as t', 't.id', '=', 'pct.talla_id')

                ->join('colores as co', 'co.id', '=', 'pct.color_id')

                ->where('pct.stock', '>', 0)
                ->where('pct.stock_logico', '>', 0)
                ->where('pct.almacen_id', 1)

                ->where('p.estado', 'ACTIVO')
                ->where('pp.estado', 1)

                ->whereIn('pp.promocion_id', $promotionsIds)

                ->select(
                    'pp.promocion_id as promotion_id',
                    'pr.nombre as promotion_nombre',
                    'p.id as producto_id',
                    'p.nombre as producto_nombre',
                    'ca.descripcion as categoria_nombre',
                    'mo.descripcion as modelo_nombre',
                    'ma.marca as marca_nombre',
                    'co.descripcion as color_nombre',
                    't.descripcion as talla_nombre',
                    'p.precio_venta_1',
                    'p.img1_ruta',
                    'p.img2_ruta',
                    'p.img3_ruta',
                    'p.img4_ruta',
                    'p.img5_ruta'
                )
                ->distinct();

            /*
        |--------------------------------------------------------------------------
        | FILTROS
        |--------------------------------------------------------------------------
        */

            if ($promoFilter && strtoupper($promoFilter) !== 'TODOS') {
                $query->where('pr.nombre', $promoFilter);
            }

            if ($categoryFilter) {
                $query->where('ca.descripcion', $categoryFilter);
            }

            if ($sizeFilter) {
                $query->where('t.descripcion', $sizeFilter);
            }

            if ($colorFilter) {
                $query->where('co.descripcion', $colorFilter);
            }

            if ($brandFilter) {
                $query->where('ma.marca', $brandFilter);
            }

            if ($searchFilter) {
                $query->where(function ($q) use ($searchFilter) {
                    $q->where('p.nombre', 'LIKE', "%{$searchFilter}%")
                        ->orWhere('mo.descripcion', 'LIKE', "%{$searchFilter}%")
                        ->orWhere('ca.descripcion', 'LIKE', "%{$searchFilter}%");
                });
            }

            /*
        |--------------------------------------------------------------------------
        | CLON PARA FILTROS DINAMICOS
        |--------------------------------------------------------------------------
        */
            $filtersQuery = clone $query;

            /*
        |--------------------------------------------------------------------------
        | TOTAL
        |--------------------------------------------------------------------------
        */
            $total = $query->count();

            /*
        |--------------------------------------------------------------------------
        | PRODUCTOS
        |--------------------------------------------------------------------------
        */
            $products = $query
                ->orderBy('p.id', 'desc')
                ->skip($offset)
                ->take($limit)
                ->get()
                ->map(function ($p) use ($baseUrl) {

                    return [
                        'promotion_id'     => $p->promotion_id,
                        'promotion_nombre' => $p->promotion_nombre,
                        'producto_id'      => $p->producto_id,
                        'producto_nombre'  => $p->producto_nombre,
                        'categoria_nombre' => $p->categoria_nombre,
                        'modelo_nombre'    => $p->modelo_nombre,
                        'marca_nombre'     => $p->marca_nombre,
                        'color_nombre'     => $p->color_nombre,
                        'talla_nombre'     => $p->talla_nombre,
                        'precio_venta_1'   => $p->precio_venta_1,

                        'imagenes' => collect([
                            'img1_ruta',
                            'img2_ruta',
                            'img3_ruta',
                            'img4_ruta',
                            'img5_ruta'
                        ])
                            ->map(fn($f) => $p->$f
                                ? $baseUrl . '/' . str_replace('storage/', '', $p->$f)
                                : null)
                            ->filter()
                            ->values(),
                    ];
                });

            /*
        |--------------------------------------------------------------------------
        | FILTROS DINAMICOS
        |--------------------------------------------------------------------------
        */

            $categories = (clone $filtersQuery)
                ->select('ca.descripcion')
                ->distinct()
                ->orderBy('ca.descripcion')
                ->pluck('ca.descripcion')
                ->values();

            $sizes = (clone $filtersQuery)
                ->select('t.descripcion')
                ->distinct()
                ->orderBy('t.descripcion')
                ->pluck('t.descripcion')
                ->values();

            $colors = (clone $filtersQuery)
                ->select('co.descripcion')
                ->distinct()
                ->orderBy('co.descripcion')
                ->pluck('co.descripcion')
                ->values();

            $brands = (clone $filtersQuery)
                ->select('ma.marca')
                ->distinct()
                ->orderBy('ma.marca')
                ->pluck('ma.marca')
                ->values();

            /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

            return response()->json([
                'success' => true,
                'message' => 'PROMOCIONES OBTENIDAS',
                'data' => [
                    'promotions' => $promotions,
                    'products'   => $products,

                    'filters' => [
                        'categories' => $categories,
                        'sizes'      => $sizes,
                        'colors'     => $colors,
                        'brands'     => $brands,
                    ],

                    'meta' => [
                        'total'    => $total,
                        'limit'    => (int) $limit,
                        'offset'   => (int) $offset,
                        'has_more' => ($offset + $limit) < $total
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
