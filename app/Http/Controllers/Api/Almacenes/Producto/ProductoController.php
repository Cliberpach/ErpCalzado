<?php

namespace App\Http\Controllers\Api\Almacenes\Producto;

use App\Almacenes\Categoria;
use App\Almacenes\Producto;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;
use Throwable;

class ProductoController extends Controller
{

    public function getAll(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $productos = DB::table('productos as p')
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
                'p.img5_ruta'
            )
            ->where('p.tipo', 'PRODUCTO')
            ->where('p.mostrar_en_web', true)
            ->paginate($perPage);

        $ids = $productos->getCollection()->pluck('id');

        $colores = DB::table('producto_colores as pc')
            ->join('colores as c', 'c.id', '=', 'pc.color_id')
            ->whereIn('pc.producto_id', $ids)
            ->where('pc.almacen_id', 1)
            ->select('c.id', 'c.descripcion as nombre', 'c.codigo', 'pc.producto_id')
            ->get()
            ->groupBy('producto_id');

        $data = $productos->getCollection()->map(function ($producto) use ($colores) {
            return [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
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
}
