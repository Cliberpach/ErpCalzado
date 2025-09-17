<?php

namespace App\Http\Services\Produccion\Orden;

use App\Almacenes\Almacen;
use App\Almacenes\Color;
use App\Almacenes\Modelo;
use App\Almacenes\Producto;
use App\Almacenes\Talla;
use App\Pedidos\OrdenProduccion;
use App\Pedidos\OrdenProduccionDetalle;
use App\Ventas\Pedido;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrdenProduccionService
{


    public function __construct() {}

    public function registrar(array $items, int $pedido_id)
    {
        $pedido =   Pedido::findOrFail($pedido_id);

        //===== CREAMOS LA ORDEN DE PRODUCCIÓN CABEZERA ========
        $orden_produccion                             =   new OrdenProduccion();
        $orden_produccion->user_id                    =   Auth::user()->id;
        $orden_produccion->user_nombre                =   Auth::user()->usuario;
        $orden_produccion->fecha_propuesta_atencion   =   $pedido->fecha_propuesta;
        $orden_produccion->observacion                =   'GENERADO AL PAGAR RE-' . $pedido->id;
        $orden_produccion->tipo                       =   "STOCK";
        $orden_produccion->save();

        //======= GUARDANDO DETALLES DE LA ORDEN DE PRODUCCIÓN =====
        foreach ($items as $item) {

            //====== OBTENIENDO MODELO ID =======
            $almacen    =   Almacen::findOrFail($item->almacen_id);
            $producto   =   Producto::findOrFail($item->producto_id);
            $modelo     =   Modelo::findOrFail($producto->modelo_id);
            $color      =   Color::findOrFail($item->color_id);
            $talla      =   Talla::findOrFail($item->talla_id);

            $orden_produccion_detalle                           =   new OrdenProduccionDetalle();
            $orden_produccion_detalle->orden_produccion_id      =   $orden_produccion->id;
            $orden_produccion_detalle->modelo_id                =   $modelo->id;
            $orden_produccion_detalle->almacen_id               =   $item->almacen_id;
            $orden_produccion_detalle->producto_id              =   $item->producto_id;
            $orden_produccion_detalle->color_id                 =   $item->color_id;
            $orden_produccion_detalle->talla_id                 =   $item->talla_id;
            $orden_produccion_detalle->pedido_id                =   $pedido_id;
            $orden_produccion_detalle->almacen_nombre           =   $almacen->descripcion;
            $orden_produccion_detalle->modelo_nombre            =   $modelo->descripcion;
            $orden_produccion_detalle->producto_nombre          =   $producto->nombre;
            $orden_produccion_detalle->color_nombre             =   $color->descripcion;
            $orden_produccion_detalle->talla_nombre             =   $talla->descripcion;
            $orden_produccion_detalle->cantidad                 =   $item->cantidad;
            $orden_produccion_detalle->save();
        }
    }
}
