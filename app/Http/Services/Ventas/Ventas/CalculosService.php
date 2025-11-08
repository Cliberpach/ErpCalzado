<?php

namespace App\Http\Services\Ventas\Ventas;

use Exception;

class CalculosService
{

    /*
{#2086
  +"monto_subtotal": 38.0
  +"monto_total_pagar": 38.0
  +"monto_total": 32.203389830508
  +"monto_igv": 5.7966101694915
  +"porcentaje_descuento": 0.0
  +"monto_descuento": 0.0
  +"monto_embalaje": "0.00"
  +"monto_envio": "0.00"
}
*/
    public function calcularMontos($lstVenta, $datos_validados)
    {
        $monto_embalaje =   $datos_validados->monto_embalaje;
        $monto_envio    =   $datos_validados->monto_envio;

        //======= CALCULANDO MONTOS ========
        $monto_subtotal     =   0.0;
        $monto_embalaje     =   $monto_embalaje ?? 0;
        $monto_envio        =   $monto_envio ?? 0;
        $monto_total        =   0.0;
        $monto_igv          =   0.0;
        $monto_total_pagar  =   0.0;
        $monto_descuento    =   0;
        $monto_anticipo     =   $datos_validados->anticipo_monto_consumido ?? 0;
        $monto_subtotal_ant =   0;

        foreach ($lstVenta as $producto) {
            foreach ($producto->tallas as $talla) {
                if (floatval($producto->porcentaje_descuento) == 0) {
                    $monto_subtotal     +=  ($talla->cantidad * $producto->precio_venta);
                } else {
                    $monto_subtotal     +=  ($talla->cantidad * $producto->precio_venta_nuevo);
                }
                $monto_subtotal_ant +=  ($talla->cantidad * $producto->precio_venta);
            }
        }

        $monto_total_pagar      =   $monto_subtotal + $monto_embalaje + $monto_envio - $monto_anticipo;
        $monto_total            =   $monto_total_pagar / 1.18;
        $monto_igv              =   $monto_total_pagar - $monto_total;
        $porcentaje_descuento   =   ($monto_descuento * 100) / ($monto_total_pagar + $monto_anticipo);
        $monto_descuento        =   $monto_subtotal_ant - $monto_subtotal;

        //========= CALCULAR LOS MONTOS DE SUNAT =========
        if ($monto_anticipo == 0) {
            $mtoOperGravadasSunat   =   (($monto_subtotal + $monto_embalaje + $monto_envio) / 1.18);
            $mtoIgvSunat            =   $mtoOperGravadasSunat * 0.18;
            $totalImpuestosSunat    =   $mtoIgvSunat;
            $valorVentaSunat        =   $mtoOperGravadasSunat;
            $subTotalSunat          =   $mtoOperGravadasSunat + $mtoIgvSunat;
            $mtoImpVentaSunat       =   $subTotalSunat;
        } else {
            $mtoOperGravadasSunat   =   (($monto_subtotal + $monto_embalaje + $monto_envio) / 1.18) - ($monto_anticipo / 1.18);
            $mtoIgvSunat            =   $mtoOperGravadasSunat * 0.18;
            $valorVentaSunat        =   ($monto_subtotal + $monto_embalaje + $monto_envio) / 1.18;
            $totalImpuestosSunat    =   $mtoIgvSunat;
            $subTotalSunat          =   $valorVentaSunat + ($valorVentaSunat * 0.18);
            $mtoImpVentaSunat       =   $subTotalSunat - ($monto_anticipo / 1.18);
        }


        $montos =   (object) [
            'monto_subtotal'        =>  $monto_subtotal,
            'monto_total_pagar'     =>  $monto_total_pagar,
            'monto_total'           =>  $monto_total,
            'monto_igv'             =>  $monto_igv,
            'porcentaje_descuento'  =>  $porcentaje_descuento,
            'monto_descuento'       =>  $monto_descuento,
            'monto_embalaje'        =>  $monto_embalaje,
            'monto_envio'           =>  $monto_envio,

            'mtoOperGravadasSunat'  =>  $mtoOperGravadasSunat,
            'mtoIgvSunat'           =>  $mtoIgvSunat,
            'totalImpuestosSunat'   =>  $totalImpuestosSunat,
            'valorVentaSunat'       =>  $valorVentaSunat,
            'subTotalSunat'         =>  $subTotalSunat,
            'mtoImpVentaSunat'      =>  $mtoImpVentaSunat
        ];

        return $montos;
    }
}
