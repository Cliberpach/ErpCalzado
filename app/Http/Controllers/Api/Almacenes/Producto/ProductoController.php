<?php

namespace App\Http\Controllers\Api\Almacenes\Producto;

use App\Almacenes\Categoria;
use App\Almacenes\Producto;
use App\Http\Controllers\Controller;
use App\Models\Almacenes\Color\Color;
use Carbon\Carbon;
use Exception;
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
        $filtro_categoria    =   $request->get('categoria');

        $perPage = $request->get('per_page', 10);

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
            $productos->where('c.id', $filtro_categoria);
        }

        $productos = $productos->paginate($perPage);

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
                    ->where('pct.stock','>=','pct.stock_logico')
                    ->select('t.id', 't.descripcion as nombre', 'pct.stock', 'pct.stock_logico')
                    ->get();

                $cant_tallas += count($tallas_color);
                $color->tallas = $tallas_color;
            }

            $data = [
                'id' => $producto->id,
                'disponible'    =>  $cant_tallas === 0?false:true,
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

            $producto = Producto::findOrFail($producto->id);

            if($producto->mostrar_en_web == false){
                return response()->json([
                    'success' => false,
                    'message' => 'PRODUCTO NO DISPONIBLE',
                ], 404);
            }
            if($producto->estado != 'ACTIVO'){
                return response()->json([
                    'success' => false,
                    'message' => 'PRODUCTO NO DISPONIBLE',
                ], 404);
            }
            if($producto->tipo != 'PRODUCTO'){
                return response()->json([
                    'success' => false,
                    'message' => 'PRODUCTO NO DISPONIBLE',
                ], 404);
            }

            $color =    Color::findOrFail($color);
            if($color->estado != 'ACTIVO'){
                return response()->json([
                    'success' => false,
                    'message' => 'COLOR NO DISPONIBLE',
                ], 404);
            }
            if($color->tipo != 'COLOR'){
                return response()->json([
                    'success' => false,
                    'message' => 'COLOR NO DISPONIBLE',
                ], 404);
            }

            $tallas = DB::table('producto_color_tallas as pct')
                ->join('tallas as t', 't.id', '=', 'pct.talla_id')
                ->where('pct.producto_id', $producto->id)
                ->where('pct.color_id', $color)
                ->where('pct.almacen_id', 1)
                ->where('pct.stock_logico', '>', 0)
                ->where('pct.stock','>=','pct.stock_logico')
                ->select('t.id', 't.descripcion as nombre', 'pct.stock', 'pct.stock_logico')
                ->get();

            if($tallas->isEmpty()){
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
}
