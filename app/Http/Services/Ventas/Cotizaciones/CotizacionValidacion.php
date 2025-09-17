<?php

namespace App\Http\Services\Ventas\Cotizaciones;

use App\Mantenimiento\Empresa\Empresa;
use App\Ventas\Cliente;
use App\Ventas\Cotizacion;
use App\Ventas\Pedido;
use Exception;

class CotizacionValidacion
{
    public function validacionGenerarPedido(array $datos)
    {
        $cotizacion_id  =   $datos['cotizacion_id'];

        if (!$cotizacion_id) {
            throw new Exception("FALTA EL PARÁMETRO COTIZACIÓN ID EN LA PETICIÓN");
        }

        $cotizacion =   Cotizacion::findOrFail($cotizacion_id);
        if (!$cotizacion) {
            throw new Exception("LA COTIZACIÓN NO EXISTE");
        }
        if ($cotizacion->estado != 'VIGENTE') {
            throw new Exception("LA COTIZACIÓN NO ESTÁ VIGENTE");
        }

        $pedido =   Pedido::find($cotizacion->pedido_id);
        if ($pedido) {
            throw new Exception("LA COTIZACIÓN YA FUE CONVERTIDA A PEDIDO");
        }

        $datos['cotizacion']    =   $cotizacion;
        $datos['cliente']       =   Cliente::findOrFail($cotizacion->cliente_id);
        $datos['empresa']       =   Empresa::findOrFail($cotizacion->empresa_id);
        return $datos;
    }
}
