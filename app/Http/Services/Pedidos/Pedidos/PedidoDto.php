<?php

namespace App\Http\Services\Pedidos\Pedidos;

use App\Ventas\Documento\Documento;
use App\Ventas\Pedido;

class PedidoDto
{
    public function getDtoDocumentoAtencion(array $datos):array{

        $dto                        =   [];
        $dto                        =   array_merge($datos,$dto);

        $pedido                     =   Pedido::findOrFail($datos['pedido_id']);

        $dto['pedido']                  =   $pedido;
        $dto['sede_id']                 =   $pedido->sede_id;
        $dto['almacenSeleccionado']     =   $pedido->almacen_id;
        $dto['tipo_doc_venta_pedido']   =   'ATENCION';
        $dto['metodoPagoId']            =   null;
        $dto['cuentaPagoId']            =   null;

        if ($pedido->facturado === 'SI') {
            $dto['facturado']           =   'SI';
            $dto['generar_recibo_caja'] =   'NO';
            $dto['user_id']             =   $pedido->user_id;
            $dto['cliente_id']          =   $pedido->cliente_id;
        }
        return $dto;

    }

}
