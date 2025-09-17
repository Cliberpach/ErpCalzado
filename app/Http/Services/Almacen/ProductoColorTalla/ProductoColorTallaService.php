<?php

namespace App\Http\Services\Almacen\ProductoColorTalla;

use App\Ventas\Documento\Detalle;
use Illuminate\Support\Facades\DB;
use stdClass;

class ProductoColorTallaService
{

    public function __construct() {}

    public function getProductoColorTalla(int $almacen_id, int $producto_id, int $color_id, int $talla_id): ?\stdClass
    {
        $producto_color_talla =  DB::select(
            'SELECT
                                        pct.almacen_id,
                                        pct.producto_id,
                                        pct.color_id,
                                        pct.talla_id,
                                        pct.stock,
                                        pct.stock_logico,
                                        p.nombre AS producto_nombre,
                                        p.codigo AS producto_codigo,
                                        c.descripcion AS color_nombre,
                                        t.descripcion AS talla_nombre,
                                        m.descripcion AS modelo_nombre
                                        FROM producto_color_tallas AS pct
                                        INNER JOIN productos AS p ON p.id = pct.producto_id
                                        INNER JOIN colores AS c ON c.id = pct.color_id
                                        INNER JOIN tallas AS t ON t.id = pct.talla_id
                                        INNER JOIN modelos AS m ON m.id = p.modelo_id
                                        WHERE
                                        pct.almacen_id = ?
                                        AND pct.producto_id = ?
                                        AND pct.color_id = ?
                                        AND pct.talla_id = ?',
            [
                $almacen_id,
                $producto_id,
                $color_id,
                $talla_id
            ]
        );

        if (count($producto_color_talla) === 0 || count($producto_color_talla) > 1) {
            return null;
        }

        return $producto_color_talla[0];
    }

    public function decrementarStocks(int $almacen_id, int $producto_id, int $color_id, int $talla_id, float $cantidad)
    {
        DB::update(
            'UPDATE producto_color_tallas
                        SET
                        stock = stock - ?,
                        stock_logico = stock_logico - ?
                        WHERE
                        almacen_id = ?
                        AND producto_id = ?
                        AND color_id = ?
                        AND talla_id = ?',
            [
                $cantidad,
                $cantidad,
                $almacen_id,
                $producto_id,
                $color_id,
                $talla_id
            ]
        );
    }

        public function incrementarStocks(int $almacen_id, int $producto_id, int $color_id, int $talla_id, float $cantidad)
    {
        DB::update(
            'UPDATE producto_color_tallas
                        SET
                        stock = stock + ?,
                        stock_logico = stock_logico + ?
                        WHERE
                        almacen_id = ?
                        AND producto_id = ?
                        AND color_id = ?
                        AND talla_id = ?',
            [
                $cantidad,
                $cantidad,
                $almacen_id,
                $producto_id,
                $color_id,
                $talla_id
            ]
        );
    }

    public function analizarStockVenta(int $venta_id): array
    {
        $items  =   Detalle::where('documento_id', $venta_id)->get();

        $list   =   [];
        foreach ($items as $item) {

            $item_validado  =   (object)[];

            $item_bd    =   DB::table('producto_color_tallas as pct')
                ->select(
                    'pct.stock',
                    'pct.stock_logico'
                )
                ->where('pct.almacen_id', $item->almacen_id)
                ->where('pct.producto_id', $item->producto_id)
                ->where('pct.color_id', $item->color_id)
                ->where('pct.talla_id', $item->talla_id)
                ->first();

            $item_validado->almacen_id  =   $item->almacen_id;
            $item_validado->producto_id =   $item->producto_id;
            $item_validado->color_id    =   $item->color_id;
            $item_validado->talla_id    =   $item->talla_id;
            $item_validado->cantidad    =   floatval($item->cantidad);

            if (!$item_bd) {
                $item_validado->valido          =   false;
                $item_validado->stock           =   null;
                $item_validado->stock_logico    =   null;
                $item_validado->observacion     =   'NO EXISTE';
            }

            if (
                floatval($item_bd->stock) < floatval($item->cantidad) ||
                floatval($item_bd->stock_logico) < floatval($item->cantidad)
            ) {
                $item_validado->valido          =   false;
                $item_validado->stock           =   $item_bd->stock;
                $item_validado->stock_logico    =   $item_bd->stock_logico;
                $item_validado->observacion     =   'STOCK INSUFICIENTE';
            }

            if (
                floatval($item_bd->stock) >= floatval($item->cantidad) &&
                floatval($item_bd->stock_logico) >= floatval($item->cantidad)
            ) {
                $item_validado->valido          =   true;
                $item_validado->observacion     =   null;
                $item_validado->stock           =   $item_bd->stock;
                $item_validado->stock_logico    =   $item_bd->stock_logico;
            }

            $list[] =   $item_validado;
        }
        return $list;
    }
}
