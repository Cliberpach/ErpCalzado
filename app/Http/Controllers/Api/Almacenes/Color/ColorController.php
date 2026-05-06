<?php

namespace App\Http\Controllers\Api\Almacenes\Color;

use App\Http\Controllers\Controller;
use App\Models\Almacenes\Color\Color;
use Illuminate\Http\Request;
use Throwable;

class ColorController extends Controller
{
    public function getAll(Request $request)
    {
        try {

            $search = $request->get('search');

            $page = max((int) $request->get('page', 1), 1);

            $limit = max((int) $request->get('limit', 10), 1);

            $query = Color::query()
                ->where('estado', 'ACTIVO')
                ->where('tipo', 'COLOR');

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
                    'text'    => $item->descripcion,
                    'subtext' => 'Color disponible',
                ];
            });

            return response()->json([

                'success' => true,
                'message' => 'COLORES OBTENIDOS',

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
