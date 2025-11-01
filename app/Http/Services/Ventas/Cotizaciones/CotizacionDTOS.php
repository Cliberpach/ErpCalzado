<?php

namespace App\Http\Services\Ventas\Cotizaciones;

use App\Almacenes\Almacen;
use App\Almacenes\Color;
use App\Almacenes\Modelo;
use App\Almacenes\Producto;
use App\Almacenes\Talla;
use App\Models\Ventas\Cotizacion\Cotizacion;
use App\Models\Ventas\Cotizacion\CotizacionDetalle;
use App\User;
use App\Ventas\Cliente;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;

class CotizacionDTOS
{
    public function prepararDtoStore(array $datos): array
    {
        $almacen                =   Almacen::find($datos['almacen']);
        $registrador            =   User::find($datos['registrador_id']);

        //======== EMPRESA =======
        $dto['empresa_id']  =   1;

        //======== SEDE  =========
        $dto['sede_id']                 =   $datos['sede_id'];

        //======= ALMACÉN ==========
        $dto['almacen_id']         =   $almacen->id;
        $dto['almacen_nombre']     =   $almacen->descripcion;

        //=========  CLIENTE =========
        $cliente            =   Cliente::findOrFail($datos['cliente']);
        $dto['cliente_id']  =   $cliente->id;

        //========= REGISTRADOR ========
        $dto['registrador_id']     =   $datos['registrador_id'];
        $dto['registrador_nombre'] =   $registrador->usuario;

        //======== CONDICIÓN =======
        $dto['condicion_id']        =   $datos['condicion_id'];
        $dto['fecha_documento']     =   Carbon::now()->format('Y-m-d');
        $dto['fecha_atencion']      =   Carbon::now()->format('Y-m-d');

        //======== MONTOS =========
        $montos =   $datos['montos'];
        $dto['sub_total']            = $montos->monto_subtotal;
        $dto['monto_embalaje']       = $montos->monto_embalaje;
        $dto['monto_envio']          = $montos->monto_envio;
        $dto['total_igv']            = $montos->monto_igv;
        $dto['total']                = $montos->monto_total;
        $dto['total_pagar']          = $montos->monto_total_pagar;
        $dto['monto_descuento']      = $montos->monto_descuento;
        $dto['porcentaje_descuento'] = $montos->porcentaje_descuento;

        //======== MONEDA =======
        $dto['moneda']             =   4;
        $dto['igv']                =   $datos['porcentaje_igv'];
        $dto['igv_check']          =   "1";
        $dto['telefono']           =   $datos['telefono'];

        return $dto;
    }

    public function prepararDtoDetalle(array $lstItems, Cotizacion $cotizacion): array
    {
        $_dto    =   [];

        foreach ($lstItems as $item) {

            $existe_producto    =   Producto::findOrFail($item->producto_id);
            $existe_color       =   Color::findOrFail($item->color_id);
            $existe_talla       =   Talla::find($item->talla_id);

            if (!$existe_producto) {
                throw new Exception("EL PRODUCTO NO EXISTE EN LA BD!!!");
            }

            if (!$existe_color) {
                throw new Exception("EL COLOR NO EXISTE EN LA BD!!!");
            }

            if (!$existe_talla) {
                throw new Exception("LA TALLA NO EXISTE EN LA BD!!!");
            }

            //====== CALCULANDO MONTOS PARA EL DETALLE ======
            $dto                            = [];
            $dto['cotizacion_id']           = $cotizacion->id;
            $dto['almacen_id']              = $cotizacion->almacen_id;
            $dto['producto_id']             = $item->producto_id;
            $dto['color_id']                = $item->color_id;
            $dto['talla_id']                = $item->talla_id;
            $dto['almacen_nombre']          = $cotizacion->almacen_nombre;
            $dto['producto_nombre']         = $existe_producto->nombre;
            $dto['color_nombre']            = $existe_color->descripcion;
            $dto['talla_nombre']            = $existe_talla->descripcion;
            $dto['cantidad']                = $item->cantidad;
            $dto['precio_unitario']         = $item->precio_venta;
            $dto['importe']                 = floatval($item->cantidad) * floatval($item->precio_venta);
            $dto['precio_unitario_nuevo']   = floatval($item->precio_venta_nuevo);
            $dto['porcentaje_descuento']    = floatval($item->porcentaje_descuento);
            $dto['importe_nuevo']           = floatval($item->precio_venta_nuevo) * floatval($item->cantidad);
            $dto['monto_descuento']         = floatval($dto['importe_nuevo'] - $dto['importe']);
            $dto['tipo']                    = 'PRODUCTO';

            $_dto[] =   $dto;
        }


        if ($cotizacion->monto_embalaje != 0 && $cotizacion->monto_embalaje) {
            $almacen_ficticio               =   Almacen::where('tipo','FICTICIO')->where('estado','ANULADO')->where('descripcion','ALMACEN')->first();
            $producto_embalaje              =   Producto::where('tipo', 'FICTICIO')->where('nombre', 'EMBALAJE')->first();
            $color_ficticio                 =   Color::where('tipo', 'FICTICIO')->where('descripcion', 'SERVICIO')->first();
            $talla_ficticio                 =   Talla::where('tipo', 'FICTICIO')->where('descripcion', 'SERVICIO')->first();

            $dto                            = [];
            $dto['cotizacion_id']           = $cotizacion->id;
            $dto['almacen_id']              = $almacen_ficticio->id;
            $dto['producto_id']             = $producto_embalaje->id;
            $dto['color_id']                = $color_ficticio->id;
            $dto['talla_id']                = $talla_ficticio->id;
            $dto['almacen_nombre']          = $almacen_ficticio->descripcion;
            $dto['producto_nombre']         = $producto_embalaje->nombre;
            $dto['color_nombre']            = $color_ficticio->descripcion;
            $dto['talla_nombre']            = $talla_ficticio->descripcion;
            $dto['cantidad']                = 1;
            $dto['precio_unitario']         = $cotizacion->monto_embalaje;
            $dto['importe']                 = $cotizacion->monto_embalaje;
            $dto['precio_unitario_nuevo']   = floatval($cotizacion->monto_embalaje);
            $dto['porcentaje_descuento']    = 0;
            $dto['importe_nuevo']           = $cotizacion->monto_embalaje;
            $dto['monto_descuento']         = 0;
            $dto['tipo']                    = 'SERVICIO';

            $_dto[] =   $dto;

        }

        if ($cotizacion->monto_envio != 0 && $cotizacion->monto_envio) {
            $almacen_ficticio                   =   Almacen::where('tipo','FICTICIO')->where('estado','ANULADO')->where('descripcion','ALMACEN')->first();
            $producto_embalaje                  =   Producto::where('tipo', 'FICTICIO')->where('nombre', 'ENVIO')->first();
            $color_ficticio                     =   Color::where('tipo', 'FICTICIO')->where('descripcion', 'SERVICIO')->first();
            $talla_ficticio                     =   Talla::where('tipo', 'FICTICIO')->where('descripcion', 'SERVICIO')->first();

            $dto                            = [];
            $dto['cotizacion_id']           = $cotizacion->id;
            $dto['almacen_id']              = $almacen_ficticio->id;
            $dto['producto_id']             = $producto_embalaje->id;
            $dto['color_id']                = $color_ficticio->id;
            $dto['talla_id']                = $talla_ficticio->id;
            $dto['almacen_nombre']          = $almacen_ficticio->descripcion;
            $dto['producto_nombre']         = $producto_embalaje->nombre;
            $dto['color_nombre']            = $color_ficticio->descripcion;
            $dto['talla_nombre']            = $talla_ficticio->descripcion;
            $dto['cantidad']                = 1;
            $dto['precio_unitario']         = $cotizacion->monto_envio;
            $dto['importe']                 = $cotizacion->monto_envio;
            $dto['precio_unitario_nuevo']   = floatval($cotizacion->monto_envio);
            $dto['porcentaje_descuento']    = 0;
            $dto['importe_nuevo']           = $cotizacion->monto_envio;
            $dto['monto_descuento']         = 0;
            $dto['tipo']                    = 'SERVICIO';

            $_dto[] =   $dto;

        }

        return $_dto;
    }

    public function prepararDatosToPedido(array $datos): array
    {

        $cotizacion =   $datos['cotizacion'];

        $_datos['cliente_id']           =   $datos['cliente']->id;
        $_datos['cliente_nombre']       =   $datos['cliente']->nombre;
        $_datos['empresa_id']           =   $datos['empresa']->id;
        $_datos['razon_social']         =   $datos['empresa']->razon_social;
        $_datos['user_id']              =   Auth::user()->id;
        $_datos['user_nombre']          =   Auth::user()->nombre;
        $_datos['condicion_id']         =   $cotizacion->condicion_id;
        $_datos['moneda']               =   $cotizacion->moneda;

        $_datos['sub_total']            =   $cotizacion->sub_total;
        $_datos['total']                =   $cotizacion->total;
        $_datos['total_igv']            =   $cotizacion->total_igv;
        $_datos['total_pagar']          =   $cotizacion->total_pagar;
        $_datos['monto_embalaje']       =   $cotizacion->monto_embalaje;
        $_datos['monto_envio']          =   $cotizacion->monto_envio;
        $_datos['porcentaje_descuento'] =   $cotizacion->porcentaje_descuento;
        $_datos['monto_descuento']      =   $cotizacion->monto_descuento;
        $_datos['fecha_registro']       =   Carbon::now()->format('Y-m-d');
        $_datos['cotizacion_id']        =   $cotizacion->id;
        $_datos['sede_id']              =   $cotizacion->sede_id;
        $_datos['almacen_id']           =   $cotizacion->almacen_id;
        $_datos['registrador_id']       =   Auth::user()->id;
        $_datos['usuario_nombre']       =   Auth::user()->usuario;
        $_datos['fecha_propuesta']      =   $datos['fecha_propuesta'];
        $_datos['observacion']          =   $datos['observacion'];

        $detalle_cotizacion             =   CotizacionDetalle::where('cotizacion_id', $cotizacion->id)->where('estado', 'ACTIVO')->get();

        $_datos['detalle_cotizacion']   =   $detalle_cotizacion;

        return $_datos;
    }
}
