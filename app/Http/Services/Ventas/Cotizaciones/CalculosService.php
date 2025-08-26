<?php

namespace App\Http\Services\Ventas\Cotizaciones;

class CalculosService
{
    public function calcularMontos(array $datos): object
    {
        $lstCotizacion      =   $datos['lstCotizacion'];
        $monto_subtotal     =   0.0;
        $monto_embalaje     =   $montos_cotizacion->embalaje ?? 0;
        $monto_envio        =   $montos_cotizacion->envio ?? 0;
        $monto_total        =   0.0;
        $monto_igv          =   0.0;
        $monto_total_pagar  =   0.0;
        $monto_descuento    =   $montos_cotizacion->monto_descuento ?? 0;

        foreach ($lstCotizacion as $producto) {
            if (floatval($producto->porcentaje_descuento) == 0) {
                $monto_subtotal +=  ($producto->cantidad * $producto->precio_venta);
            } else {
                $monto_subtotal +=  ($producto->cantidad * $producto->precio_venta_nuevo);
            }
        }

        $monto_total_pagar      =   $monto_subtotal + $monto_embalaje + $monto_envio;
        $monto_total            =   $monto_total_pagar / 1.18;
        $monto_igv              =   $monto_total_pagar - $monto_total;
        $porcentaje_descuento   =   ($monto_descuento * 100) / ($monto_total_pagar);

        return (object)[
        'monto_subtotal'=>$monto_subtotal,
        'monto_embalaje'=>$monto_embalaje,
        'monto_envio'=>$monto_envio,
        'monto_igv'=>$monto_igv,
        'monto_total'=>$monto_total,
        'monto_descuento'=>$monto_descuento,
        'porcentaje_descuento'=>$porcentaje_descuento,
        'monto_total_pagar' =>  $monto_total_pagar];

    }
}
