<?php

namespace App\Http\Services\Ventas\Ventas;

use App\Ventas\Documento\Documento;
use Carbon\Carbon;

class VentaDto
{
    public function dtoStore(object $data, object $montos, object $datos_correlativo, string $legenda): array
    {
        $dto = [];

        // caja
        $dto['caja_id']            = $data->caja_movimiento->caja_id;
        $dto['caja_nombre']        = $data->caja_movimiento->caja_nombre;
        $dto['caja_movimiento_id'] = $data->caja_movimiento->movimiento_id;

        // fechas
        $dto['fecha_documento'] = Carbon::now()->toDateString();
        $dto['fecha_atencion']  = Carbon::now()->toDateString();

        if ($data->condicion->id != 1) {
            $dto['fecha_vencimiento'] = Carbon::now()->addDays($data->condicion->dias)->toDateString();
        } else {
            $dto['fecha_vencimiento'] = Carbon::now()->toDateString();
        }

        // pagos
        $lstPagos = $data->lstPagos ?? [];
        $isPay    = $data->isPay   ?? true;

        if ($isPay && !empty($lstPagos)) {
            $dto['estado_pago'] = 'PAGADA';
            $dto['importe']     = array_sum(array_column($lstPagos, 'montoPago'));
            $dto['tipo_pago_id'] = $lstPagos[0]['metodoPagoId'] ?? null;

            $p1 = $lstPagos[0];
            if (!empty($p1['metodoPagoId']) && !empty($p1['montoPago'])) {
                $dto['pago_1_tipo_pago_id']    = $p1['metodoPagoId'];
                $dto['pago_1_monto']           = $p1['montoPago'];
                $dto['pago_1_fecha_operacion'] = $p1['fechaOperacionPago'] ?? null;
                $dto['pago_1_nro_operacion']   = $p1['nroOperacionPago']   ?? null;
                if ($data->tipo_pago_1) {
                    $dto['pago_1_tipo_pago_nombre'] = $data->tipo_pago_1->descripcion;
                }
                if ($data->cuenta_pago_1) {
                    $dto['pago_1_cuenta_id']    = $p1['cuentaPagoId'];
                    $dto['pago_1_banco_nombre'] = $data->cuenta_pago_1->banco_nombre;
                    $dto['pago_1_nro_cuenta']   = $data->cuenta_pago_1->nro_cuenta;
                    $dto['pago_1_cci']          = $data->cuenta_pago_1->cci;
                    $dto['pago_1_celular']      = $data->cuenta_pago_1->celular;
                    $dto['pago_1_titular']      = $data->cuenta_pago_1->titular;
                    $dto['pago_1_moneda']       = $data->cuenta_pago_1->moneda;
                }
            }

            if (isset($lstPagos[1])) {
                $p2 = $lstPagos[1];
                if (!empty($p2['metodoPagoId']) && !empty($p2['montoPago'])) {
                    $dto['pago_2_tipo_pago_id']    = $p2['metodoPagoId'];
                    $dto['pago_2_monto']           = $p2['montoPago'];
                    $dto['pago_2_fecha_operacion'] = $p2['fechaOperacionPago'] ?? null;
                    $dto['pago_2_nro_operacion']   = $p2['nroOperacionPago']   ?? null;
                    if ($data->tipo_pago_2) {
                        $dto['pago_2_tipo_pago_nombre'] = $data->tipo_pago_2->descripcion;
                    }
                    if ($data->cuenta_pago_2) {
                        $dto['pago_2_cuenta_id']    = $p2['cuentaPagoId'];
                        $dto['pago_2_banco_nombre'] = $data->cuenta_pago_2->banco_nombre;
                        $dto['pago_2_nro_cuenta']   = $data->cuenta_pago_2->nro_cuenta;
                        $dto['pago_2_cci']          = $data->cuenta_pago_2->cci;
                        $dto['pago_2_celular']      = $data->cuenta_pago_2->celular;
                        $dto['pago_2_titular']      = $data->cuenta_pago_2->titular;
                        $dto['pago_2_moneda']       = $data->cuenta_pago_2->moneda;
                    }
                }
            }
        }

        // empresa
        $dto['ruc_empresa']                 = $data->empresa->ruc;
        $dto['empresa']                     = $data->empresa->razon_social;
        $dto['direccion_fiscal_empresa']    = $data->empresa->direccion_fiscal;
        $dto['empresa_id']                  = $data->empresa->id;

        // cliente
        $dto['tipo_documento_cliente'] = $data->cliente->tipo_documento;
        $dto['documento_cliente']      = $data->cliente->documento;
        $dto['direccion_cliente']      = $data->cliente->direccion;
        $dto['cliente']                = $data->cliente->nombre;
        $dto['cliente_id']             = $data->cliente->id;

        // tipo venta
        $dto['tipo_venta_id']     = $data->tipo_venta->id;
        $dto['tipo_venta_nombre'] = $data->tipo_venta->descripcion;
        $dto['tipo_venta_codigo'] = $data->tipo_venta->simbolo;

        // condición
        $dto['condicion_id']          = $data->condicion->id;
        $dto['condicion_pago_nombre'] = $data->condicion->descripcion;

        $dto['observacion']        = mb_strtoupper($data->observacion ?? '', 'UTF-8');
        $dto['user_id']            = $data->usuario->id;
        $dto['registrador_nombre'] = $data->usuario->usuario;

        // montos
        $dto['sub_total']            = $montos->monto_subtotal;
        $dto['monto_embalaje']       = $montos->monto_embalaje;
        $dto['monto_envio']          = $montos->monto_envio;
        $dto['total']                = $montos->monto_total;
        $dto['total_igv']            = $montos->monto_igv;
        $dto['total_pagar']          = $montos->monto_total_pagar;
        $dto['igv']                  = $data->empresa->igv;
        $dto['monto_descuento']      = $montos->monto_descuento;
        $dto['porcentaje_descuento'] = $montos->porcentaje_descuento;
        $dto['moneda']               = 1;

        // sunat
        $dto['mto_oper_gravadas_sunat'] = $montos->mtoOperGravadasSunat;
        $dto['mto_igv_sunat']           = $montos->mtoIgvSunat;
        $dto['total_impuestos_sunat']   = $montos->totalImpuestosSunat;
        $dto['valor_venta_sunat']       = $montos->valorVentaSunat;
        $dto['sub_total_sunat']         = $montos->subTotalSunat;
        $dto['mto_imp_venta_sunat']     = $montos->mtoImpVentaSunat;

        // serie, correlativo, legenda
        $dto['serie']       = $datos_correlativo->serie;
        $dto['correlativo'] = $datos_correlativo->correlativo;
        $dto['legenda']     = $legenda;

        $dto['sede_id']       = $data->sede_id;
        $dto['almacen_id']    = $data->almacen->id;
        $dto['almacen_nombre'] = $data->almacen->descripcion;

        // facturación de pedido (anticipo)
        if ($data->facturar && $data->pedido_id) {
            $dto['pedido_id']             = $data->pedido_id;
            $dto['es_anticipo']           = true;
            $dto['tipo_doc_venta_pedido'] = 'FACTURACIÓN';
            $dto['saldo_anticipo']        = $montos->monto_total_pagar;
        }

        // ticket crédito de pedido
        if ($data->ticket_credito && $data->pedido_id) {
            $dto['pedido_id']             = $data->pedido_id;
            $dto['tipo_doc_venta_pedido'] = 'CREDITO';
        }

        if ($data->modo === 'CONSUMO') {
            $dto['pedido_id'] = $data->pedido_id;
        }

        // pagadas con anticipo o consumo
        if ($data->facturado === 'SI' || $data->modo === 'CONSUMO') {
            $dto['estado_pago'] = 'PAGADA';
        }

        // anticipo consumido
        if ($data->anticipo_consumido_id) {
            $dto['anticipo_consumido_id']            = $data->anticipo_consumido_id;
            $dto['anticipo_monto_consumido']         = $data->anticipo_monto_consumido;
            $dto['anticipo_monto_consumido_sin_igv'] = $data->anticipo_monto_consumido / (($data->porcentaje_igv + 100) / 100);
            $dto['anticipo_consumido_serie']         = $data->doc_anticipo_serie;
            $dto['anticipo_consumido_correlativo']   = $data->doc_anticipo_correlativo;
        }

        // conversión de documento
        if ($data->documento_convertido) {
            $doc_convertido          = Documento::find($data->documento_convertido);
            $dto['convert_de_id']    = $data->documento_convertido;
            $dto['convert_de_serie'] = $doc_convertido->serie . '-' . $doc_convertido->correlativo;
            $dto['estado_pago']      = 'PAGADA';
        }

        // regularización
        if ($data->doc_regularizar_id) {
            $doc_regularizar              = Documento::find($data->doc_regularizar_id);
            $dto['regularizado_de_id']    = $doc_regularizar->id;
            $dto['regularizado_de_serie'] = $doc_regularizar->serie . '-' . $doc_regularizar->correlativo;
            $dto['estado_pago']           = 'PAGADA';
        }

        $dto['modo'] = $data->modo ?? 'VENTA';

        if ($data->modo) {
            $dto['tipo_doc_venta_pedido'] = 'ATENCION';
            $dto['pedido_id']             = $data->pedido_id ?? null;
        }
        if ($data->tipo_doc_venta_pedido === 'CONSUMO') {
            $dto['tipo_doc_venta_pedido'] = 'CONSUMO';
        }

        $dto['telefono'] = $data->telefono;

        return $dto;
    }
}
