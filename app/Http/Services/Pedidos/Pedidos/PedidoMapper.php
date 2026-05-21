<?php

namespace App\Http\Services\Pedidos\Pedidos;

class PedidoMapper
{
    public function formatearColoresTallas($colores, $stocks, $tallas)
    {
        $producto = [];

        // Verifica si $colores no está vacío
        if (count($colores) > 0) {
            $producto['id']     = $colores[0]->producto_id;
            $producto['nombre'] = $colores[0]->producto_nombre;
        } else {
            // Maneja el caso cuando $colores está vacío
            $producto['id']     = null;
            $producto['nombre'] = null;
        }

        $lstColores = [];

        //======== RECORRIENDO COLORES =======
        foreach ($colores as $color) {
            $item_color = [];
            $item_color['id']       =   $color->color_id;
            $item_color['nombre']   =   $color->color_nombre;

            //======== OBTENIENDO TALLAS DEL COLOR =======
            $lstTallas = [];

            foreach ($tallas as $talla) {
                $item_talla = [];
                $item_talla['id'] = $talla->id;
                $item_talla['nombre'] = $talla->descripcion;

                $stock_filtrado = collect($stocks)->filter(function ($stock) use ($producto, $color, $talla) {
                    return $stock->producto_id == $producto['id'] &&
                        $stock->color_id == $color->color_id &&
                        $stock->talla_id == $talla->id;
                });

                $first_stock = $stock_filtrado->first();

                $item_talla['stock'] = $first_stock->stock ?? 0;
                $item_talla['stock_logico'] = $first_stock->stock_logico ?? 0;

                $lstTallas[] = $item_talla;
            }

            $item_color['tallas'] = $lstTallas;
            $lstColores[] = $item_color;
        }

        $producto['colores'] = $lstColores;

        return $producto;
    }
}
