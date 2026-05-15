<?php

namespace App\Http\Controllers\Api\Almacenes\Producto;

use App\Almacenes\Producto;
use App\Http\Controllers\Controller;
use App\Models\Almacenes\Color\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProductoController extends Controller
{
    public function getAll(Request $request)
    {
        $filtro_categoria       =   $request->get('categoria');
        $filter_color           =   $request->get('color');
        $filter_size            =   $request->get('size');
        $filter_search          =   $request->get('search');

        $filter_featured = $request->get('featured');
        $filter_sale     = $request->get('sale');
        $filter_outlet   = $request->get('outlet');
        $filter_new      = $request->get('new');

        $allowedPerPage         =   [12, 24, 48, 96];
        $perPage = (int) request('per_page', 48);
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 48;
        }

        $productos = DB::table('productos as p')
            ->join('categorias as c', 'c.id', '=', 'p.categoria_id')
            ->select(
                'p.id',
                'p.nombre',
                'p.precio_venta_1',
                'p.precio_venta_2',
                'p.precio_venta_3',
                'p.img1_ruta',
                'p.img2_ruta',
                'p.img3_ruta',
                'p.img4_ruta',
                'p.img5_ruta',
                'c.descripcion as categoria_nombre'
            )
            ->where('p.tipo', 'PRODUCTO')
            ->where('p.mostrar_en_web', true);

        if ($filtro_categoria) {
            $productos->where('c.descripcion', $filtro_categoria);
        }

        if ($filter_search) {
            $productos->where(function ($q) use ($filter_search) {
                $q->where('p.nombre', 'like', "%{$filter_search}%")
                    ->orWhere('c.descripcion', 'like', "%{$filter_search}%");
            });
        }

        if ($filter_color) {
            $productos->whereExists(function ($q) use ($filter_color) {
                $q->select(DB::raw(1))
                    ->from('producto_colores as pc')
                    ->join('colores as co', 'co.id', 'pc.color_id')
                    ->whereColumn('pc.producto_id', 'p.id')
                    ->where('co.descripcion', $filter_color)
                    ->where('pc.almacen_id', 1);
            });
        }

        if ($filter_size) {
            $productos->whereExists(function ($q) use ($filter_size) {
                $q->select(DB::raw(1))
                    ->from('producto_color_tallas as pct')
                    ->join('tallas as t', 't.id', '=', 'pct.talla_id')
                    ->whereColumn('pct.producto_id', 'p.id')
                    ->where('t.descripcion', $filter_size)
                    ->where('pct.stock', '>', 0)
                    ->where('pct.stock_logico', '>', 0);
            });
        }

        if ($filter_featured) {
            $productos->where('p.is_featured', 1);
        }

        if ($filter_sale) {
            $productos->where('p.is_sale', 1);
        }

        if ($filter_outlet) {
            $productos->where('p.is_outlet', 1);
        }

        if ($filter_new) {
            $productos->orderByDesc('p.created_at');
        }

        //return response()->json($productos->get());

        $productos = $productos->paginate($perPage);

        $ids = $productos->getCollection()->pluck('id');

        $colores = DB::table('producto_colores as pc')
            ->join('colores as c', 'c.id', '=', 'pc.color_id')
            ->whereIn('pc.producto_id', $ids)
            ->where('pc.almacen_id', 1)
            ->select(
                'c.id',
                'c.descripcion as nombre',
                'c.codigo',
                'pc.producto_id'
            )
            ->get()
            ->groupBy('producto_id');

        $data = $productos->getCollection()->map(function ($producto) use ($colores) {
            return [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'categoria_nombre' => $producto->categoria_nombre,

                'precio_venta_1' => floatval($producto->precio_venta_1),
                'precio_venta_2' => floatval($producto->precio_venta_2),
                'precio_venta_3' => floatval($producto->precio_venta_3),

                'img1_url' => $producto->img1_ruta ? asset($producto->img1_ruta) : null,
                'img2_url' => $producto->img2_ruta ? asset($producto->img2_ruta) : null,
                'img3_url' => $producto->img3_ruta ? asset($producto->img3_ruta) : null,
                'img4_url' => $producto->img4_ruta ? asset($producto->img4_ruta) : null,
                'img5_url' => $producto->img5_ruta ? asset($producto->img5_ruta) : null,

                'colores'       => isset($colores[$producto->id]) ? $colores[$producto->id]->values() : []
            ];
        });

        $productos->setCollection($data);

        return response()->json([
            'success' => true,
            'message' => 'PRODUCTOS OBTENIDOS',
            'data' => $productos->items(),
            'meta' => [
                'current_page' => $productos->currentPage(),
                'last_page' => $productos->lastPage(),
                'per_page' => $productos->perPage(),
                'total' => $productos->total(),
            ]
        ]);
    }

    public function getOne($id)
    {
        try {

            $producto = DB::table('productos as p')
                ->join('categorias as c', 'c.id', '=', 'p.categoria_id')
                ->select(
                    'p.id',
                    'p.nombre',
                    'p.descripcion',
                    'p.precio_venta_1',
                    'p.precio_venta_2',
                    'p.precio_venta_3',
                    'p.img1_ruta',
                    'p.img2_ruta',
                    'p.img3_ruta',
                    'p.img4_ruta',
                    'p.img5_ruta',
                    'c.descripcion as categoria_nombre'
                )
                ->where('p.tipo', 'PRODUCTO')
                ->where('p.mostrar_en_web', true)
                ->where('p.id', $id)
                ->first();

            if (!$producto) {
                return response()->json([
                    'success' => false,
                    'message' => 'PRODUCTO NO ENCONTRADO',
                ], 404);
            }

            $colores = DB::table('producto_colores as pc')
                ->join('colores as c', 'c.id', '=', 'pc.color_id')
                ->where('pc.producto_id', $producto->id)
                ->where('pc.almacen_id', 1)
                ->select('c.id', 'c.descripcion as nombre', 'c.codigo')
                ->get();

            $cant_tallas = 0;
            foreach ($colores as $color) {
                $tallas_color = DB::table('producto_color_tallas as pct')
                    ->join('tallas as t', 't.id', '=', 'pct.talla_id')
                    ->where('pct.producto_id', $producto->id)
                    ->where('pct.color_id', $color->id)
                    ->where('pct.almacen_id', 1)
                    ->where('pct.stock_logico', '>', 0)
                    ->where('pct.stock', '>=', 'pct.stock_logico')
                    ->select('t.id', 't.descripcion as nombre', 'pct.stock', 'pct.stock_logico')
                    ->get();

                $cant_tallas += count($tallas_color);
                $color->tallas = $tallas_color;
            }

            $data = [
                'id' => $producto->id,
                'disponible'    =>  $cant_tallas === 0 ? false : true,
                'nombre' => $producto->nombre,
                'descripcion' => $producto->descripcion,
                'categoria_nombre' => $producto->categoria_nombre,

                'precio_venta_1' => floatval($producto->precio_venta_1),
                'precio_venta_2' => floatval($producto->precio_venta_2),
                'precio_venta_3' => floatval($producto->precio_venta_3),

                'img1_url'      => $producto->img1_ruta ? asset($producto->img1_ruta) : null,
                'img2_url'      => $producto->img2_ruta ? asset($producto->img2_ruta) : null,
                'img3_url'      => $producto->img3_ruta ? asset($producto->img3_ruta) : null,
                'img4_url'      => $producto->img4_ruta ? asset($producto->img4_ruta) : null,
                'img5_url'      => $producto->img5_ruta ? asset($producto->img5_ruta) : null,
                'colores'       => $colores,
            ];

            return response()->json([
                'success' => true,
                'message' => 'PRODUCTO OBTENIDO',
                'data' => $data,
            ]);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'ERROR AL OBTENER EL PRODUCTO',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function getTallasByColor($producto, $color)
    {
        try {

            $producto = Producto::findOrFail($producto);

            if ($producto->mostrar_en_web == false) {
                return response()->json([
                    'success' => false,
                    'message' => 'PRODUCTO NO DISPONIBLE',
                ], 404);
            }
            if ($producto->estado != 'ACTIVO') {
                return response()->json([
                    'success' => false,
                    'message' => 'PRODUCTO NO DISPONIBLE',
                ], 404);
            }
            if ($producto->tipo != 'PRODUCTO') {
                return response()->json([
                    'success' => false,
                    'message' => 'PRODUCTO NO DISPONIBLE',
                ], 404);
            }

            $color =    Color::findOrFail($color);
            if ($color->estado != 'ACTIVO') {
                return response()->json([
                    'success' => false,
                    'message' => 'COLOR NO DISPONIBLE',
                ], 404);
            }
            if ($color->tipo != 'COLOR') {
                return response()->json([
                    'success' => false,
                    'message' => 'COLOR NO DISPONIBLE',
                ], 404);
            }

            $tallas = DB::table('producto_color_tallas as pct')
                ->join('tallas as t', 't.id', '=', 'pct.talla_id')
                ->where('pct.producto_id', $producto->id)
                ->where('pct.color_id', $color->id)
                ->where('pct.almacen_id', 1)
                ->where('pct.stock_logico', '>', 0)
                ->where('pct.stock', '>=', 'pct.stock_logico')
                ->select('t.id', 't.descripcion as nombre', 'pct.stock', 'pct.stock_logico')
                ->get();


            if ($tallas->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'NO HAY TALLAS DISPONIBLES PARA ESTE COLOR',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'TALLAS OBTENIDAS',
                'data' => $tallas,
            ]);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'ERROR AL OBTENER LAS TALLAS',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function searchProduct(Request $request)
    {
        try {
            $query          =   trim($request->get('q', ''));
            $warehouse_id   =   $request->get('warehouse_id');

            if (empty($query)) {
                return response()->json(['data' => []]);
            }

            $products = Producto::from('productos as p')
                ->join('categorias as c', 'c.id', 'p.categoria_id')
                ->join('marcas as m', 'm.id', 'p.marca_id')
                ->where('p.estado', 'ACTIVO')
                ->where(function ($q) use ($query) {
                    $q->where('p.nombre', 'LIKE', "%{$query}%")
                        ->orWhere('c.descripcion', 'LIKE', "%{$query}%")
                        ->orWhere('m.marca', 'LIKE', "%{$query}%");
                })
                ->limit(20)
                ->select(
                    'p.id',
                    'm.marca as marca_nombre',
                    'p.nombre as producto_nombre',
                    'c.descripcion as categoria_nombre',
                    'p.precio_venta_1',
                )->get();

            $data = $products->map(fn($p) => [
                'id' => $p->id,
                'text'  => "{$p->producto_nombre}",
                'subtext' => "{$p->categoria_nombre}-{$p->marca_nombre}",
            ]);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'ERROR AL OBTENER LAS TALLAS',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function getHomeProducts(Request $request)
    {
        try {
            $type  = $request->get('type', 'featured');
            $limit = min((int) $request->get('limit', 8), 12);

            $validTypes = ['featured', 'sale', 'outlet', 'new_arrivals'];
            if (!in_array($type, $validTypes)) {
                $type = 'featured';
            }

            $query = DB::table('productos as p')
                ->join('categorias as c', 'c.id', '=', 'p.categoria_id')
                ->join('marcas as m', 'm.id', '=', 'p.marca_id')
                ->select(
                    'p.id',
                    'p.nombre',
                    'p.precio_venta_1',
                    'p.precio_venta_2',
                    'p.precio_venta_3',
                    'p.img1_ruta',
                    'p.img2_ruta',
                    'p.img3_ruta',
                    'p.is_featured',
                    'p.is_sale',
                    'p.is_outlet',
                    'c.descripcion as categoria_nombre',
                    'm.marca as marca_nombre'
                )
                ->where('p.tipo', 'PRODUCTO')
                ->where('p.mostrar_en_web', true)
                ->where('p.estado', 'ACTIVO');

            if ($type === 'sale') {
                $query->where('p.is_sale', 1)->orderByDesc('p.updated_at');
            } elseif ($type === 'outlet') {
                $query->where('p.is_outlet', 1)->orderByDesc('p.updated_at');
            } elseif ($type === 'new_arrivals') {
                $query->orderByDesc('p.created_at');
            } else {
                $query->where('p.is_featured', 1)->orderByDesc('p.updated_at');
            }

            $products = $query->limit($limit)->get();

            $ids = $products->pluck('id');

            $colores = DB::table('producto_colores as pc')
                ->join('colores as co', 'co.id', '=', 'pc.color_id')
                ->whereIn('pc.producto_id', $ids)
                ->where('pc.almacen_id', 1)
                ->select('pc.producto_id', 'co.id', 'co.descripcion as nombre', 'co.codigo')
                ->get()
                ->groupBy('producto_id');

            $baseUrl = url('storage/');
            $imgUrl  = fn($ruta) => $ruta ? $baseUrl . '/' . str_replace('storage/', '', $ruta) : null;

            $data = $products->map(function ($p) use ($colores, $imgUrl) {
                return [
                    'id'               => $p->id,
                    'nombre'           => $p->nombre,
                    'categoria_nombre' => $p->categoria_nombre,
                    'marca_nombre'     => $p->marca_nombre,
                    'precio_venta_1'   => floatval($p->precio_venta_1),
                    'precio_venta_2'   => floatval($p->precio_venta_2),
                    'precio_venta_3'   => floatval($p->precio_venta_3),
                    'img1_url'         => $imgUrl($p->img1_ruta),
                    'img2_url'         => $imgUrl($p->img2_ruta),
                    'img3_url'         => $imgUrl($p->img3_ruta),
                    'is_featured'      => (bool) $p->is_featured,
                    'is_sale'          => (bool) $p->is_sale,
                    'is_outlet'        => (bool) $p->is_outlet,
                    'colores'          => isset($colores[$p->id]) ? $colores[$p->id]->values() : [],
                ];
            });

            return response()->json([
                'success' => true,
                'type'    => $type,
                'data'    => $data,
            ]);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function getListing(Request $request)
    {
        try {
            // --- Params ---
            $search      = trim($request->get('search', ''));
            $categoriaIds = array_values(array_filter((array) $request->get('categoria_ids', []), 'is_numeric'));
            $marcaIds     = array_values(array_filter((array) $request->get('marca_ids', []), 'is_numeric'));
            $colorIds     = array_values(array_filter((array) $request->get('color_ids', []), 'is_numeric'));
            $tallaIds     = array_values(array_filter((array) $request->get('talla_ids', []), 'is_numeric'));

            $sort = $request->get('sort', 'newest');
            if (!in_array($sort, ['newest', 'precio_asc', 'precio_desc', 'nombre_asc'])) {
                $sort = 'newest';
            }

            $perPage = (int) $request->get('per_page', 24);
            if (!in_array($perPage, [12, 24, 48])) { $perPage = 24; }
            $page = max(1, (int) $request->get('page', 1));

            // --- Base filtered query ---
            $base = DB::table('productos as p')
                ->join('categorias as c', 'c.id', '=', 'p.categoria_id')
                ->join('marcas as m', 'm.id', '=', 'p.marca_id')
                ->where('p.tipo', 'PRODUCTO')
                ->where('p.mostrar_en_web', true)
                ->where('p.estado', 'ACTIVO');

            if ($search !== '') {
                $base->where(function ($q) use ($search) {
                    $q->where('p.nombre', 'like', "%{$search}%")
                      ->orWhere('c.descripcion', 'like', "%{$search}%")
                      ->orWhere('m.marca', 'like', "%{$search}%");
                });
            }

            if (!empty($categoriaIds)) {
                $base->whereIn('p.categoria_id', $categoriaIds);
            }

            if (!empty($marcaIds)) {
                $base->whereIn('p.marca_id', $marcaIds);
            }

            if (!empty($colorIds)) {
                $base->whereExists(function ($q) use ($colorIds) {
                    $q->select(DB::raw(1))
                      ->from('producto_colores as pc')
                      ->whereColumn('pc.producto_id', 'p.id')
                      ->whereIn('pc.color_id', $colorIds)
                      ->where('pc.almacen_id', 1);
                });
            }

            if (!empty($tallaIds)) {
                $base->whereExists(function ($q) use ($tallaIds) {
                    $q->select(DB::raw(1))
                      ->from('producto_color_tallas as pct')
                      ->whereColumn('pct.producto_id', 'p.id')
                      ->whereIn('pct.talla_id', $tallaIds)
                      ->where('pct.almacen_id', 1)
                      ->where('pct.stock_logico', '>', 0);
                });
            }

            // --- All matching IDs (base for facets + total count) ---
            $allIds = (clone $base)->select('p.id')->distinct()->pluck('id')->toArray();
            $total  = count($allIds);

            // --- Empty result fast path ---
            if ($total === 0) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'products'   => [],
                        'pagination' => ['total' => 0, 'per_page' => $perPage, 'current_page' => 1, 'last_page' => 1, 'from' => 0, 'to' => 0],
                        'facets'     => ['categorias' => [], 'marcas' => [], 'colores' => [], 'tallas' => []],
                    ],
                ]);
            }

            // --- Facets (computed from the filtered set) ---
            $facetCategorias = DB::table('productos as p')
                ->join('categorias as c', 'c.id', '=', 'p.categoria_id')
                ->whereIn('p.id', $allIds)
                ->select('c.id', 'c.descripcion as label', DB::raw('COUNT(DISTINCT p.id) as count'))
                ->groupBy('c.id', 'c.descripcion')
                ->orderBy('c.descripcion')
                ->get();

            $facetMarcas = DB::table('productos as p')
                ->join('marcas as m', 'm.id', '=', 'p.marca_id')
                ->whereIn('p.id', $allIds)
                ->select('m.id', 'm.marca as label', DB::raw('COUNT(DISTINCT p.id) as count'))
                ->groupBy('m.id', 'm.marca')
                ->orderBy('m.marca')
                ->get();

            $facetColores = DB::table('producto_colores as pc')
                ->join('colores as co', 'co.id', '=', 'pc.color_id')
                ->whereIn('pc.producto_id', $allIds)
                ->where('pc.almacen_id', 1)
                ->select('co.id', 'co.descripcion as label', 'co.codigo', DB::raw('COUNT(DISTINCT pc.producto_id) as count'))
                ->groupBy('co.id', 'co.descripcion', 'co.codigo')
                ->orderBy('co.descripcion')
                ->get();

            $facetTallas = DB::table('producto_color_tallas as pct')
                ->join('tallas as t', 't.id', '=', 'pct.talla_id')
                ->whereIn('pct.producto_id', $allIds)
                ->where('pct.almacen_id', 1)
                ->where('pct.stock_logico', '>', 0)
                ->select('t.id', 't.descripcion as label', DB::raw('COUNT(DISTINCT pct.producto_id) as count'))
                ->groupBy('t.id', 't.descripcion')
                ->orderBy('t.descripcion')
                ->get();

            // --- Paginated products query ---
            $lastPage = max(1, (int) ceil($total / $perPage));
            $page     = min($page, $lastPage);
            $offset   = ($page - 1) * $perPage;

            $productsQuery = (clone $base)->select(
                'p.id', 'p.nombre',
                'p.precio_venta_1', 'p.precio_venta_2', 'p.precio_venta_3',
                'p.img1_ruta', 'p.img2_ruta', 'p.img3_ruta',
                'p.is_featured', 'p.is_sale', 'p.is_outlet', 'p.created_at',
                'c.descripcion as categoria_nombre', 'm.marca as marca_nombre'
            );

            if ($sort === 'precio_asc') {
                $productsQuery->orderBy('p.precio_venta_1');
            } elseif ($sort === 'precio_desc') {
                $productsQuery->orderByDesc('p.precio_venta_1');
            } elseif ($sort === 'nombre_asc') {
                $productsQuery->orderBy('p.nombre');
            } else {
                $productsQuery->orderByDesc('p.created_at');
            }

            $products = $productsQuery->offset($offset)->limit($perPage)->get();

            // --- Enrich colors (batch, no N+1) ---
            $pageIds = $products->pluck('id')->toArray();
            $colores = DB::table('producto_colores as pc')
                ->join('colores as co', 'co.id', '=', 'pc.color_id')
                ->whereIn('pc.producto_id', $pageIds)
                ->where('pc.almacen_id', 1)
                ->select('pc.producto_id', 'co.id', 'co.descripcion as nombre', 'co.codigo')
                ->get()
                ->groupBy('producto_id');

            $baseUrl = url('storage/');
            $imgUrl  = fn($ruta) => $ruta ? $baseUrl . '/' . str_replace('storage/', '', $ruta) : null;

            $data = $products->map(function ($p) use ($colores, $imgUrl) {
                return [
                    'id'               => $p->id,
                    'nombre'           => $p->nombre,
                    'categoria_nombre' => $p->categoria_nombre,
                    'marca_nombre'     => $p->marca_nombre,
                    'precio_venta_1'   => floatval($p->precio_venta_1),
                    'precio_venta_2'   => floatval($p->precio_venta_2),
                    'precio_venta_3'   => floatval($p->precio_venta_3),
                    'img1_url'         => $imgUrl($p->img1_ruta),
                    'img2_url'         => $imgUrl($p->img2_ruta),
                    'img3_url'         => $imgUrl($p->img3_ruta),
                    'is_featured'      => (bool) $p->is_featured,
                    'is_sale'          => (bool) $p->is_sale,
                    'is_outlet'        => (bool) $p->is_outlet,
                    'colores'          => isset($colores[$p->id]) ? $colores[$p->id]->values() : [],
                ];
            });

            return response()->json([
                'success' => true,
                'data'    => [
                    'products'   => $data,
                    'pagination' => [
                        'total'        => $total,
                        'per_page'     => $perPage,
                        'current_page' => $page,
                        'last_page'    => $lastPage,
                        'from'         => $offset + 1,
                        'to'           => min($offset + $perPage, $total),
                    ],
                    'facets' => [
                        'categorias' => $facetCategorias,
                        'marcas'     => $facetMarcas,
                        'colores'    => $facetColores,
                        'tallas'     => $facetTallas,
                    ],
                ],
            ]);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }

    public function getProductsByTag()
    {
        $baseUrl = url('storage/');

        $format = function ($products) use ($baseUrl) {
            return $products->map(function ($p) use ($baseUrl) {
                return [
                    'id' => $p->id,
                    'nombre' => $p->nombre,
                    'precio' => $p->precio_venta_1,
                    'imagenes' => [
                        $p->img1_ruta ? $baseUrl . '/' . str_replace('storage/', '', $p->img1_ruta) : null,
                        $p->img2_ruta ? $baseUrl . '/' . str_replace('storage/', '', $p->img2_ruta) : null,
                        $p->img3_ruta ? $baseUrl . '/' . str_replace('storage/', '', $p->img3_ruta) : null,
                        $p->img4_ruta ? $baseUrl . '/' . str_replace('storage/', '', $p->img4_ruta) : null,
                        $p->img5_ruta ? $baseUrl . '/' . str_replace('storage/', '', $p->img5_ruta) : null,
                    ],
                ];
            });
        };

        return response()->json([
            'featured' => $format(
                Producto::where('is_featured', 1)
                    ->where('estado', 'ACTIVO')
                    ->where('tipo', 'PRODUCTO')
                    ->where('mostrar_en_web', true)
                    ->limit(4)
                    ->get()
            ),

            'new' => $format(
                Producto::where('estado', 'ACTIVO')
                    ->where('tipo', 'PRODUCTO')
                    ->where('mostrar_en_web', true)
                    ->latest()
                    ->limit(4)
                    ->get()
            ),

            'sale' => $format(
                Producto::where('is_sale', 1)
                    ->where('estado', 'ACTIVO')
                    ->where('tipo', 'PRODUCTO')
                    ->where('mostrar_en_web', true)
                    ->limit(4)
                    ->get()
            ),

            'outlet' => $format(
                Producto::where('is_outlet', 1)
                    ->where('estado', 'ACTIVO')
                    ->where('tipo', 'PRODUCTO')
                    ->where('mostrar_en_web', true)
                    ->limit(4)
                    ->get()
            ),
        ]);
    }
}
