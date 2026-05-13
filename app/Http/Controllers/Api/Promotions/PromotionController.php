<?php

namespace App\Http\Controllers\Api\Promotions;

use App\Http\Controllers\Controller;
use App\Models\Mantenimiento\Promocion\Promocion;
use App\Models\Mantenimiento\Promocion\PromocionProducto;
use Throwable;

class PromotionController extends Controller
{
    public function getPromotions()
    {
        try {

            $promotions = Promocion::where('estado', 'ACTIVO')
                ->where('fecha_inicio', '<=', now()->toDateString())
                ->where('fecha_fin', '>=', now()->toDateString())
                ->orderBy('fecha_fin', 'asc')
                ->take(5)
                ->get();

            $promotionsIds = $promotions->pluck('id');

            $baseUrl = url('storage/');

            $products = PromocionProducto::from('promociones_productos as pp')
                ->join('productos as p', 'p.id', 'pp.producto_id')
                ->join('categorias as ca', 'ca.id', 'p.categoria_id')
                ->join('modelos as mo', 'mo.id', 'p.modelo_id')
                ->where('p.estado', 'ACTIVO')
                ->where('pp.estado', 1)
                ->whereIn('pp.promocion_id', $promotionsIds)
                ->select(
                    'pp.id as promotion_id',
                    'p.nombre as producto_nombre',
                    'ca.descripcion as categoria_nombre',
                    'mo.descripcion as modelo_nombre',
                    'p.precio_venta_1',
                    'p.img1_ruta',
                    'p.img2_ruta',
                    'p.img3_ruta',
                    'p.img4_ruta',
                    'p.img5_ruta'
                )
                ->take(10)
                ->get()
                ->map(function ($p) use ($baseUrl) {
                    return [
                        'promotion_id'      => $p->promotion_id,
                        'producto_nombre'   => $p->producto_nombre,
                        'categoria_nombre'  => $p->categoria_nombre,
                        'modelo_nombre'     => $p->modelo_nombre,
                        'precio_venta_1'    => $p->precio_venta_1,
                        'imagenes' => collect(['img1_ruta', 'img2_ruta', 'img3_ruta', 'img4_ruta', 'img5_ruta'])
                            ->map(
                                fn($field) => $p->$field
                                    ? $baseUrl . '/' . str_replace('storage/', '', $p->$field)
                                    : null
                            )
                            ->filter()
                            ->values(),
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'PROMOCIONES OBTENIDAS',
                'data' => [
                    'promotions'    =>  $promotions,
                    'products'      =>  $products
                ]
            ]);
        } catch (Throwable $th) {

            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'code' => $th->getCode()
            ], 500);
        }
    }
}
