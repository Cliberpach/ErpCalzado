<?php

namespace App\Http\Controllers\Api\Almacenes\Talla;

use App\Almacenes\Talla;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Throwable;

class TallaController extends Controller
{
    public function getAll(Request $request)
    {
        try {

            $search = $request->get('search');

            $page = max((int) $request->get('page', 1), 1);

            $limit = max((int) $request->get('limit', 10), 1);

            $query = Talla::query()
                ->where('estado', 'ACTIVO')
                ->where('tipo', 'TALLA');

            if (!empty($search)) {

                $query->where('descripcion', 'LIKE', "%{$search}%");
            }

            $total = $query->count();

            $items = $query
                ->orderBy('descripcion')
                ->skip(($page - 1) * $limit)
                ->take($limit)
                ->get();

            $data = $items->map(function ($item) {

                return [
                    'id'      => $item->id,
                    'text'    => $item->nombre,
                    'subtext' => 'Talla disponible',
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
