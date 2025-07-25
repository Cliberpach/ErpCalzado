<?php

namespace App\Http\Controllers;

use App\Almacenes\Color;
use App\Almacenes\Kardex;
use App\Almacenes\Modelo;
use App\Almacenes\Producto;
use App\Almacenes\ProductoColorTalla;
use App\Almacenes\Talla;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Luecano\NumeroALetras\NumeroALetras;
use Throwable;

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


/*
{#1540
  +"success": true
  +"data": {#1537
    +"success": true
    +"data": {#1517
      +"numero": "77777777"
      +"nombre_completo": "ALVA LUJAN, LUIS DANIEL"
      +"nombres": "LUIS DANIEL"
      +"apellido_paterno": "ALVA"
      +"apellido_materno": "LUJAN"
      +"codigo_verificacion": 9
      +"ubigeo_sunat": ""
      +"ubigeo": array:3 [
        0 => null
        1 => null
        2 => null
      ]
      +"direccion": ""
    }
    +"time": 0.041380882263184
    +"source": "apiperu.dev"
  }
}

------

{#1540
  +"success": true
  +"data": {#1537
    +"success": false
    +"message": "No se encontraron registros"
    +"time": 0.040093898773193
    +"source": "apiperu.dev"
  }
}
*/
    public static function apiDni($dni)
    {

        try {
            $url = "https://apiperu.dev/api/dni/".$dni;
            $client = new \GuzzleHttp\Client(['verify'=>false]);
            $token = 'c36358c49922c564f035d4dc2ff3492fbcfd31ee561866960f75b79f7d645d7d';
            $response = $client->get($url, [
                'headers' => [
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json',
                            'Authorization' => "Bearer {$token}"
                        ]
            ]);
            $estado     =   $response->getStatusCode();
            $data       =   json_decode($response->getBody()->getContents());


            return response()->json(['success'=>true,'data'=>$data]);
        } catch (Throwable $th) {
            return response()->json(['success'=>false,'data'=>$th->getMessage()]);
        }

    }

    public static function apiRuc($ruc)
    {

        try {
            $url = "https://apiperu.dev/api/ruc/".$ruc;
            $client = new \GuzzleHttp\Client(['verify'=>false]);
            $token = 'c36358c49922c564f035d4dc2ff3492fbcfd31ee561866960f75b79f7d645d7d';
            $response = $client->get($url, [
                'headers' => [
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json',
                            'Authorization' => "Bearer {$token}"
                        ]
            ]);
            $estado     =   $response->getStatusCode();
            $data       =   json_decode($response->getBody()->getContents());


            return response()->json(['success'=>true,'data'=>$data]);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'data'=>$th->getMessage()]);
        }

    }

    public static function convertNumeroLetras($monto){
        $formatter          =   new NumeroALetras();
        $montoFormateado    =   number_format($monto, 2, '.', '');
        $partes             =   explode('.', $montoFormateado);
        $parteEntera        =   $partes[0];
        $decimales          =   $partes[1] ?? '00';
        $legend             =   'SON ' . $formatter->toWords((int)$parteEntera) . ' CON ' . $decimales . '/100 SOLES';
        return $legend;
    }

    public static function formatearArrayDetalleObjetos($detalles){
        $detalleFormateado=[];
        $productosProcesados = [];
        foreach ($detalles as $detalle) {
            $cod   =   $detalle->producto_id.'-'.$detalle->color_id;
            if (!in_array($cod, $productosProcesados)) {
                $producto=[];
                //======== obteniendo todas las detalle talla de ese producto_color =================
                $producto_color_tallas = $detalles->filter(function ($detalleFiltro) use ($detalle) {
                    return $detalleFiltro->producto_id == $detalle->producto_id && $detalleFiltro->color_id == $detalle->color_id;
                });

                $producto_nombre    =   $detalle->producto_nombre?$detalle->producto_nombre:Producto::find($detalle->producto_id)->nombre;
                $color_nombre       =   $detalle->color_nombre?$detalle->color_nombre:Color::find($detalle->color_id)->descripcion;
                $modelo_nombre      =   $detalle->color_nombre?$detalle->color_nombre:Modelo::find(Producto::find($detalle->producto_id)->modelo_id)->descripcion;

                $producto['producto_codigo']        =   $detalle->producto_codigo;
                $producto['producto_id']            =   $detalle->producto_id;
                $producto['color_id']               =   $detalle->color_id;
                $producto['producto_nombre']        =   $producto_nombre;
                $producto['color_nombre']           =   $color_nombre;
                $producto['modelo_nombre']          =   $modelo_nombre;
                $producto['precio_unitario']        =   $detalle->precio_unitario;
                $producto['porcentaje_descuento']   =   $detalle->porcentaje_descuento;
                $producto['precio_unitario_nuevo']  =   $detalle->precio_unitario_nuevo;
                $producto['monto_descuento']        =   $detalle->monto_descuento;
                $producto['precio_venta']           =   $detalle->precio_unitario;
                $producto['precio_venta_nuevo']     =   $detalle->precio_unitario_nuevo;

                $tallas             =   [];
                $subtotal           =   0.0;
                $subtotal_with_desc =   0.0;
                $cantidadTotal=0;
                foreach ($producto_color_tallas as $producto_color_talla) {
                    $talla=[];
                    $talla['talla_id']              =   $producto_color_talla->talla_id;


                    $talla['cantidad']              =   $producto_color_talla->cantidad;
                    $subtotal                       +=  $talla['cantidad']*$producto['precio_unitario_nuevo'];
                    $cantidadTotal                  +=  $talla['cantidad'];

                    $talla_nombre                   =   $producto_color_talla->talla_nombre?$producto_color_talla->talla_nombre:Talla::find($producto_color_talla->talla_id)->descripcion;
                    $talla['talla_nombre']          =   $talla_nombre;

                   array_push($tallas,(object)$talla);
                }

                $producto['tallas']                 =   $tallas;
                $producto['subtotal']               =   $subtotal;
                $producto['cantidad_total']         =   $cantidadTotal;
                array_push($detalleFormateado,(object)$producto);
                $productosProcesados[] = $detalle->producto_id.'-'.$detalle->color_id;
            }
        }
        return $detalleFormateado;
    }


    public static function formatearArrayDetalle($detalles){

        $detalleFormateado=[];
        $productosProcesados = [];
        foreach ($detalles as $detalle) {

            $cod   =   $detalle->producto_id.'-'.$detalle->color_id;
            if (!in_array($cod, $productosProcesados)) {
                $producto=[];

                //======== obteniendo todas las detalle talla de ese producto_color =================
                $producto_color_tallas = $detalles->filter(function ($detalleFiltro) use ($detalle) {
                    return $detalleFiltro->producto_id == $detalle->producto_id && $detalleFiltro->color_id == $detalle->color_id;
                });

                $productoBD   =   Producto::find($detalle->producto_id);
                $colorBD      =   Color::find($detalle->color_id);
                $tallaBD      =   Talla::find($detalle->talla_id);

                $producto['producto_id']        = $detalle->producto_id;
                $producto['color_id']           = $detalle->color_id;
                $producto['producto_nombre']    = $detalle->producto_nombre?$detalle->producto_nombre:$productoBD->nombre;
                $producto['color_nombre']       = $detalle->color_nombre?$detalle->color_nombre:$colorBD->descripcion;


                $tallas=[];
                $subtotal=0.0;
                $cantidadTotal=0;
                foreach ($producto_color_tallas as $producto_color_talla) {
                    $talla=[];
                    $talla['talla_id']      =   $producto_color_talla->talla_id;
                    $talla['cantidad']      =   (int)$producto_color_talla->cantidad;
                    $talla['talla_nombre']  =   $producto_color_talla->talla_nombre?$producto_color_talla->talla_nombre:$tallaBD->descripcion;
                    $cantidadTotal          +=  $talla['cantidad'];
                   array_push($tallas,$talla);
                }

                $producto['tallas']=$tallas;
                $producto['subtotal']=$subtotal;
                $producto['cantidad_total']=$cantidadTotal;
                array_push($detalleFormateado,$producto);
                $productosProcesados[] = $detalle->producto_id.'-'.$detalle->color_id;
            }
        }
        return $detalleFormateado;
    }

}
