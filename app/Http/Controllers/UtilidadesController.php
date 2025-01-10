<?php

namespace App\Http\Controllers;

use App\Almacenes\Kardex;
use App\Almacenes\ProductoColorTalla;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UtilidadesController extends Controller
{

    public static function validarItem($item,$almacen_id){

        $item_bd    =   DB::select('select 
                        pct.producto_id,
                        pct.color_id,
                        pct.talla_id,
                        pct.almacen_id,
                        p.nombre as producto_nombre,
                        c.descripcion as color_nombre,
                        t.descripcion as talla_nombre,
                        a.descripcion as almacen_nombre 
                        from producto_color_tallas as pct
                        inner join productos as p on p.id = pct.producto_id
                        inner join colores as c on c.id = pct.color_id
                        inner join tallas as t on t.id = pct.talla_id
                        inner join almacenes as a on a.id = pct.almacen_id
                        where pct.producto_id = ?
                        and pct.color_id = ?
                        and pct.talla_id = ?
                        and pct.almacen_id = ?',
                        [$item->producto_id,
                        $item->color_id,
                        $item->talla_id,
                        $almacen_id]);

        if(count($item_bd) === 0){
            throw new Exception("NO EXISTE EL PRODUCTO COLOR TALLA EN EL ALMACÉN ORIGEN
            (".$item->producto_id."-".$item->color_id."-".$item->talla_id.")");
        }

        return $item_bd[0];
    }

    public static function getStockItem($item){

        $item_bd    =   DB::select('select 
                        pct.stock
                        from producto_color_tallas as pct
                        where 
                        pct.producto_id = ?
                        and pct.color_id = ?
                        and pct.talla_id = ?
                        and pct.almacen_id = ?',
                        [$item->producto_id,
                        $item->color_id,
                        $item->talla_id,
                        $item->almacen_id]);

        return $item_bd[0]->stock;

    }

    public static function guardarItemKardex($item){
        $kardex                             =   new Kardex();
        $kardex->producto_id                =   $item->producto_id;
        $kardex->color_id                   =   $item->color_id;
        $kardex->talla_id                   =   $item->talla_id;
        $kardex->numero_doc                 =   $item->numero_doc;
        $kardex->fecha                      =   $item->fecha;
        $kardex->cantidad                   =   $item->cantidad;
        $kardex->descripcion                =   $item->descripcion;
        $kardex->precio                     =   $item->precio;
        $kardex->importe                    =   $item->importe;
        $kardex->stock                      =   $item->stock;
        $kardex->documento_id               =   $item->documento_id;
        $kardex->sede_id                    =   $item->sede_id;
        $kardex->almacen_id                 =   $item->almacen_id;
        $kardex->usuario_registrador_id     =   $item->usuario_registrador_id;
        $kardex->usuario_registrador_nombre =   $item->usuario_registrador_nombre;
        $kardex->producto_nombre            =   $item->producto_nombre;
        $kardex->color_nombre               =   $item->color_nombre;
        $kardex->talla_nombre               =   $item->talla_nombre;
        $kardex->almacen_nombre             =   $item->almacen_nombre;
        $kardex->save();
    }


    public static function restarStockItem($item,$cantidad){
        ProductoColorTalla::where('producto_id', $item->producto_id)
        ->where('color_id', $item->color_id)
        ->where('talla_id', $item->talla_id)
        ->where('almacen_id', $item->almacen_id)
        ->update([
            'stock'         =>  DB::raw("stock - $cantidad"),
            'stock_logico'  =>  DB::raw("stock_logico - $cantidad"),
            'estado'        =>  '1',  
        ]);
    }

    public static function sumarStockItem($item,$cantidad){
        ProductoColorTalla::where('producto_id', $item->producto_id)
        ->where('color_id', $item->color_id)
        ->where('talla_id', $item->talla_id)
        ->where('almacen_id', $item->almacen_id)
        ->update([
            'stock'         =>  DB::raw("stock + $cantidad"),
            'stock_logico'  =>  DB::raw("stock_logico + $cantidad"),
            'estado'        =>  '1',  
        ]);
    }

}
