<?php

namespace App\Http\Services\Pedidos\Pedidos;

use App\Almacenes\Color;
use App\Almacenes\Modelo;
use App\Almacenes\Producto;
use App\Almacenes\Talla;
use App\Ventas\Pedido;
use App\Ventas\PedidoDetalle;

class PedidoRepository
{
    public function insertarPedido(array $datos): Pedido
    {
        $pedido                         =   new Pedido();
        $pedido->cliente_id             =   $datos['cliente_id'];
        $pedido->cliente_nombre         =   $datos['cliente_nombre'];
        $pedido->empresa_id             =   $datos['empresa_id'];
        $pedido->empresa_nombre         =   $datos['razon_social'];
        $pedido->user_id                =   $datos['registrador_id'];
        $pedido->user_nombre            =   $datos['usuario_nombre'];
        $pedido->condicion_id           =   $datos['condicion_id'];
        $pedido->moneda                 =   $datos['moneda'];
        $pedido->sub_total              =   $datos['sub_total'];
        $pedido->total                  =   $datos['total'];
        $pedido->total_igv              =   $datos['total_igv'];
        $pedido->total_pagar            =   $datos['total_pagar'];
        $pedido->monto_embalaje         =   $datos['monto_embalaje'];
        $pedido->monto_envio            =   $datos['monto_envio'];
        $pedido->porcentaje_descuento   =   $datos['porcentaje_descuento'];
        $pedido->monto_descuento        =   $datos['monto_descuento'];
        $pedido->fecha_registro         =   $datos['fecha_registro'];
        $pedido->cotizacion_id          =   $datos['cotizacion_id'];
        $pedido->sede_id                =   $datos['sede_id'];
        $pedido->almacen_id             =   $datos['almacen_id'];
        $pedido->fecha_propuesta        =   $datos['fecha_propuesta'];
        $pedido->observacion            =   $datos['observacion'];
        $pedido->save();

        return $pedido;
    }

    public function insertarDetallePedido(array $datos,Pedido $pedido)
    {
        $detalle_cotizacion =   $datos['detalle_cotizacion'];

        foreach ($detalle_cotizacion as $item) {

            $producto   =   Producto::findOrFail($item->producto_id);
            $color      =   Color::findOrFail($item->color_id);
            $talla      =   Talla::findOrFail($item->talla_id);
            $modelo     =   Modelo::findOrFail($producto->modelo_id);

            $detalle_pedido                 =   new PedidoDetalle();
            $detalle_pedido->pedido_id      =   $pedido->id;
            $detalle_pedido->almacen_id     =   $pedido->almacen_id;
            $detalle_pedido->producto_id    =   $item->producto_id;
            $detalle_pedido->color_id       =   $item->color_id;
            $detalle_pedido->talla_id       =   $item->talla_id;

            $detalle_pedido->producto_codigo = $producto->codigo;
            $detalle_pedido->unidad          = 'NIU';
            $detalle_pedido->producto_nombre = $producto->nombre;


            $detalle_pedido->color_nombre   =  $color->descripcion;

            $detalle_pedido->talla_nombre   =   $talla->descripcion;

            $detalle_pedido->modelo_nombre = $modelo->descripcion;

            $detalle_pedido->cantidad               =   $item->cantidad;
            $detalle_pedido->precio_unitario        =   $item->precio_unitario;
            $detalle_pedido->importe                =   $item->importe;
            $detalle_pedido->porcentaje_descuento   =   $item->porcentaje_descuento;
            $detalle_pedido->precio_unitario_nuevo  =   $item->precio_unitario_nuevo;
            $detalle_pedido->importe_nuevo          =   $item->importe_nuevo;
            $detalle_pedido->monto_descuento        =   $item->monto_descuento;
            $detalle_pedido->cantidad_atendida      =   0;
            $detalle_pedido->cantidad_pendiente     =   $item->cantidad;
            $detalle_pedido->save();
        }
    }
}
