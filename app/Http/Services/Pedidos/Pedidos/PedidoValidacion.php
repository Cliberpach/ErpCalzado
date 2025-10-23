<?php

namespace App\Http\Services\Pedidos\Pedidos;

use App\Ventas\Documento\Documento;
use App\Ventas\Pedido;
use Exception;

class PedidoValidacion
{

    public function validacionGenerarComprobanteConsumo(array $datos): array
    {

        $pedido =   Pedido::findOrFail($datos['pedido_id']);
        if ($pedido->doc_consumo_id) {
            throw new Exception("EL PEDIDO YA TIENE COMPROBANTE DE CONSUMO!!!");
        }

        if ($pedido->doc_venta_credito_estado_pago !== 'PAGADO') {
            throw new Exception("EL ESTADO DE PAGO DEL PEDIDO ES: " . $pedido->doc_venta_credito_estado_pago);
        }

        if ($pedido->estado !== 'FINALIZADO') {
            throw new Exception("EL ESTADO DEL PEDIDO ES: " . $pedido->estado);
        }

        $anticipo_comprobante  =    Documento::where('pedido_id', $pedido->id)->where('tipo_doc_venta_pedido', 'ANTICIPO')->first();

        if (!$anticipo_comprobante) {
            throw new Exception("EL PEDIDO NO TIENE NINGÚN COMPROBANTE DE ANTICIPO GENERADO!!!");
        }

        $caja_movimiento    =   movimientoUser();
        if (count($caja_movimiento) === 0) {
            throw new Exception("NO PERTENECES A NINGUNA CAJA APERTURADA!!!");
        }

        //========== VALIDACIÓN MONTOS =======
        $suma_comprobantes_anticipos = Documento::where('pedido_id', $pedido->id)
            ->where('tipo_doc_venta_pedido', 'ANTICIPO')
            ->sum('total_pagar');

        if(floatval($suma_comprobantes_anticipos) != floatval($pedido->total_pagar)){
            throw new Exception("DEBE GENERAR COMPROBANTES DE ANTICIPO PARA TODOS LOS PAGOS DE LA CUENTA");
        }

        $dto    =   [
            'cliente_id'            =>  $anticipo_comprobante->cliente_id,
            'monto'                 =>  $pedido->total_pagar,
            'tipo_comprobante_id'   =>  $anticipo_comprobante->tipo_venta_id,
            'sede_id'               =>  $anticipo_comprobante->sede_id,
            'caja_movimiento_id'    =>  $caja_movimiento[0]->movimiento_id,
            'caja_id'               =>  $caja_movimiento[0]->caja_id,
            'observacion'           =>  'COMPROBANTE DE CONSUMO DE ANTICIPOS',
            'pedido_id'             =>  $pedido->id
        ];

        return $dto;
    }
}
