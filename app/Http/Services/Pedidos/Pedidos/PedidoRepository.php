<?php

namespace App\Http\Services\Pedidos\Pedidos;

use App\Almacenes\Almacen;
use App\Almacenes\Color;
use App\Almacenes\Modelo;
use App\Almacenes\Producto;
use App\Almacenes\Talla;
use App\User;
use App\Ventas\Documento\Documento;
use App\Ventas\Pedido;
use App\Ventas\PedidoDetalle;

class PedidoRepository
{
    public function insertarPedido(array $datos): Pedido
    {
        $cliente    =   $datos['cliente'];
        $empresa    =   $datos['empresa'];
        $montos     =   $datos['montos'];

        $pedido                     =   new Pedido();
        $pedido->cliente_id         =   $cliente->id;


        $pedido->cliente_nombre     =   $cliente->nombre;
        $pedido->cliente_telefono   =   $cliente->telefono_movil;
        //==========================================//

        $pedido->empresa_id         =  1;

        $pedido->empresa_nombre     =   $empresa->razon_social;
        //==========================================//

        $pedido->condicion_id       =   $datos['condicion_id'];
        $pedido->user_id            =   $datos['registrador_id'];

        //======== OBTENIENDO EL NOMBRE COMPLETO DEL USUARIO ===========
        $pedido->user_nombre        =   User::find($datos['registrador_id'])->usuario;

        //=============================================================
        $pedido->moneda                 =   1;
        $pedido->fecha_registro         =   now()->toDateString();
        $pedido->fecha_propuesta        =   $datos['fecha_propuesta'];

        $pedido->monto_embalaje         =   $montos->monto_embalaje;
        $pedido->monto_envio            =   $montos->monto_envio;
        $pedido->sub_total              =   $montos->monto_subtotal;
        $pedido->total_igv              =   $montos->monto_igv;
        $pedido->total                  =   $montos->monto_total;
        $pedido->total_pagar            =   $montos->monto_total_pagar;
        $pedido->monto_descuento        =   $montos->monto_descuento;
        $pedido->porcentaje_descuento   =   $montos->porcentaje_descuento;

        $pedido->sede_id        =   $datos['sede_id'];
        $pedido->almacen_id     =   $datos['almacen'];
        $pedido->almacen_nombre =   Almacen::findOrFail($pedido->almacen_id)->descripcion;
        $pedido->telefono       =   $datos['telefono'];
        $pedido->save();

        return $pedido;
    }

    public function insertarDetallePedido(array $datos, Pedido $pedido)
    {
        $lstPedido  =   $datos['lstPedido'];
        foreach ($lstPedido as $producto) {

            $producto_bd    =   Producto::findOrFail($producto->producto_id);
            $modelo         =   Modelo::findOrFail($producto_bd->modelo_id);

            foreach ($producto->tallas as  $talla) {
                //===== CALCULANDO MONTOS PARA EL DETALLE =====
                $importe        =   floatval($talla->cantidad) * floatval($producto->precio_venta);
                $importe_nuevo  =   floatval($talla->cantidad) * floatval($producto->precio_venta_nuevo);


                $detalle                        = new PedidoDetalle();
                $detalle->almacen_id            = $datos['almacen'];
                $detalle->pedido_id             = $pedido->id;
                $detalle->producto_id           = $producto->producto_id;
                $detalle->color_id              = $producto->color_id;
                $detalle->talla_id              = $talla->talla_id;
                $detalle->producto_codigo       = $producto->producto_codigo;
                $detalle->producto_nombre       = $producto->producto_nombre;
                $detalle->color_nombre          = $producto->color_nombre;
                $detalle->talla_nombre          = $talla->talla_nombre;
                $detalle->modelo_nombre         = $modelo->descripcion;
                $detalle->cantidad              = $talla->cantidad;
                $detalle->cantidad_atendida     = 0;
                $detalle->cantidad_pendiente    = $talla->cantidad;
                $detalle->precio_unitario       = $producto->precio_venta;
                $detalle->importe               = $importe;
                $detalle->porcentaje_descuento  = floatval($producto->porcentaje_descuento);
                $detalle->precio_unitario_nuevo = floatval($producto->precio_venta_nuevo);
                $detalle->importe_nuevo         = $importe_nuevo;
                $detalle->monto_descuento       = floatval($importe) * floatval($producto->porcentaje_descuento) / 100;
                $detalle->save();
            }
        }

        if ($pedido->monto_embalaje != 0 && $pedido->monto_embalaje) {
            $producto_embalaje                  =   Producto::where('tipo', 'FICTICIO')->where('nombre', 'EMBALAJE')->first();
            $color_ficticio                     =   Color::where('tipo', 'FICTICIO')->where('descripcion', 'SERVICIO')->first();
            $talla_ficticio                     =   Talla::where('tipo', 'FICTICIO')->where('descripcion', 'SERVICIO')->first();
            $modelo_ficticio                    =   Modelo::where('tipo', 'FICTICIO')->where('descripcion', 'SERVICIO')->first();

            $detalle                        = new PedidoDetalle();
            $detalle->almacen_id            = $pedido->almacen_id;
            $detalle->pedido_id             = $pedido->id;
            $detalle->producto_id           = $producto_embalaje->id;
            $detalle->color_id              = $color_ficticio->id;
            $detalle->talla_id              = $talla_ficticio->id;
            $detalle->producto_codigo       = 'EMBALAJE';
            $detalle->producto_nombre       = $producto_embalaje->nombre;;
            $detalle->color_nombre          = $color_ficticio->descripcion;
            $detalle->talla_nombre          = $talla_ficticio->descripcion;
            $detalle->modelo_nombre         = $modelo_ficticio->descripcion;
            $detalle->cantidad              = 1;
            $detalle->cantidad_atendida     = 0;
            $detalle->cantidad_pendiente    = 1;
            $detalle->precio_unitario       = $pedido->monto_embalaje;
            $detalle->importe               = $pedido->monto_embalaje;
            $detalle->porcentaje_descuento  = 0;
            $detalle->precio_unitario_nuevo = $pedido->monto_embalaje;
            $detalle->importe_nuevo         = $pedido->monto_embalaje;
            $detalle->monto_descuento       = 0;
            $detalle->tipo                  = 'SERVICIO';
            $detalle->estado                = 'ACTIVO';
            $detalle->save();
        }

        if ($pedido->monto_envio != 0 && $pedido->monto_envio) {
            $producto_envio                     =   Producto::where('tipo', 'FICTICIO')->where('nombre', 'ENVIO')->first();
            $color_ficticio                     =   Color::where('tipo', 'FICTICIO')->where('descripcion', 'SERVICIO')->first();
            $talla_ficticio                     =   Talla::where('tipo', 'FICTICIO')->where('descripcion', 'SERVICIO')->first();
            $modelo_ficticio                    =   Modelo::where('tipo', 'FICTICIO')->where('descripcion', 'SERVICIO')->first();

            $detalle                        = new PedidoDetalle();
            $detalle->almacen_id            = $pedido->almacen_id;
            $detalle->pedido_id             = $pedido->id;
            $detalle->producto_id           = $producto_envio->id;
            $detalle->color_id              = $color_ficticio->id;
            $detalle->talla_id              = $talla_ficticio->id;
            $detalle->producto_codigo       = 'ENVIO';
            $detalle->producto_nombre       = $producto_envio->nombre;;
            $detalle->color_nombre          = $color_ficticio->descripcion;
            $detalle->talla_nombre          = $talla_ficticio->descripcion;
            $detalle->modelo_nombre         = $modelo_ficticio->descripcion;
            $detalle->cantidad              = 1;
            $detalle->cantidad_atendida     = 0;
            $detalle->cantidad_pendiente    = 1;
            $detalle->precio_unitario       = $pedido->monto_envio;
            $detalle->importe               = $pedido->monto_envio;
            $detalle->porcentaje_descuento  = 0;
            $detalle->precio_unitario_nuevo = $pedido->monto_envio;
            $detalle->importe_nuevo         = $pedido->monto_envio;
            $detalle->monto_descuento       = 0;
            $detalle->tipo                  = 'SERVICIO';
            $detalle->estado                = 'ACTIVO';
            $detalle->save();
        }
    }

    public function enlazarPedidoVentaCredito(Pedido $pedido, Documento $venta_credito)
    {
        $pedido->doc_venta_credito_id           =   $venta_credito->id;
        $pedido->doc_venta_credito_serie        =   $venta_credito->serie;
        $pedido->doc_venta_credito_correlativo  =   $venta_credito->correlativo;
        $pedido->doc_venta_credito_estado_pago  =   'PENDIENTE';
        $pedido->doc_venta_credito_monto_pagado =   0;
        $pedido->doc_venta_credito_saldo        =   $pedido->total_pagar;
        $pedido->update();
    }
}
