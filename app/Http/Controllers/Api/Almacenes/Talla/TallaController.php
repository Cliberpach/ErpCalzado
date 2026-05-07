<?php

namespace App\Http\Controllers\Api\Almacenes\Talla;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class TallaController extends Controller
{
    public function getAll(Request $request)
    {
        try {

            $search   = $request->get('search');
            $color_id = $request->get('color_id');
            $product  = $request->get('product');

            $page  = max((int) $request->get('page', 1), 1);
            $limit = max((int) $request->get('limit', 10), 1);

            $query = DB::table('producto_color_tallas as pct')
                ->join('tallas as t', 't.id', '=', 'pct.talla_id')
                ->where('t.estado', 'ACTIVO')
                ->where('t.tipo', 'TALLA')
                ->select(
                    't.id',
                    't.descripcion',
                    DB::raw('SUM(pct.stock) as total_stock')
                )
                ->groupBy('t.id', 't.descripcion')
                ->havingRaw('SUM(pct.stock) > 0')

                ->when($color_id, function ($q) use ($color_id) {
                    $q->where('pct.color_id', $color_id);
                })

                ->when($search, function ($q) use ($search) {
                    $q->where('t.descripcion', 'LIKE', "%{$search}%");
                })

                ->when($product, function ($q) use ($product) {

                    $q->whereExists(function ($sub) use ($product) {

                        $sub->select(DB::raw(1))
                            ->from('productos as p')
                            ->whereColumn('p.id', 'pct.producto_id')
                            ->where('p.nombre', 'LIKE', "%{$product}%");
                    });
                });

            $total = (clone $query)->count();

            $items = $query
                ->orderBy('t.descripcion')
                ->skip(($page - 1) * $limit)
                ->take($limit)
                ->get();

            $data = $items->map(function ($item) {

                return [
                    'id'      => $item->id,
                    'text'    => $item->descripcion,
                    'subtext' => 'Stock: ' . $item->total_stock,
                ];
            });

            return response()->json([

                'success' => true,
                'message' => 'TALLAS OBTENIDAS',

                'data' => $data,

                'pagination' => [

                    'page'    => $page,
                    'limit'   => $limit,
                    'total'   => $total,

                    'hasMore' => ($page * $limit) < $total
                ]
            ]);
        } catch (Throwable $th) {

            return response()->json([

                'success' => false,
                'message' => $th->getMessage(),
                'code'    => $th->getCode()

            ], 500);
        }
    }
}
