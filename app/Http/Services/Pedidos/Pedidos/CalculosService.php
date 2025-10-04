<?php

namespace App\Http\Services\Pedidos\Pedidos;

use Exception;

class CalculosService
{
    public function calcularMontos($lstPedido, $amountsPedido)
    {
        if (count($lstPedido) === 0) {
            throw new Exception("EL DETALLE DEL PEDIDO ESTÁ VACÍO");
        }

        $monto_subtotal     =   0.0;
        $monto_embalaje     =   $amountsPedido->embalaje ?? 0;
        $monto_envio        =   $amountsPedido->envio ?? 0;
        $monto_total        =   0.0;
        $monto_igv          =   0.0;
        $monto_total_pagar  =   0.0;
        $monto_descuento    =   $amountsPedido->monto_descuento ?? 0;

        foreach ($lstPedido as $producto) {
            foreach ($producto->tallas as $talla) {
                $monto_subtotal +=  ($talla->cantidad * $producto->precio_venta_nuevo);
            }
        }

        $monto_total_pagar      =   $monto_subtotal + $monto_embalaje + $monto_envio;
        $monto_total            =   $monto_total_pagar / 1.18;
        $monto_igv              =   $monto_total_pagar - $monto_total;

        $porcentaje_descuento   =   ($monto_descuento / ($monto_total_pagar + $monto_descuento)) * 100;

        return (object)[
            'monto_embalaje'        =>  $monto_embalaje,
            'monto_envio'           =>  $monto_envio,
            'monto_subtotal'        =>  $monto_subtotal,
            'monto_igv'             =>  $monto_igv,
            'monto_total'           =>  $monto_total,
            'monto_total_pagar'     =>  $monto_total_pagar,
            'monto_descuento'       =>  $monto_descuento,
            'porcentaje_descuento'  =>  $porcentaje_descuento
        ];
    }
}
