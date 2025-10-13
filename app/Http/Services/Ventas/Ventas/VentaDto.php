<?php

namespace App\Http\Services\Ventas\Ventas;

use App\Almacenes\Almacen;
use App\Almacenes\Color;
use App\Almacenes\Modelo;
use App\Almacenes\Producto;
use App\Almacenes\Talla;
use App\Ventas\Documento\Detalle;
use App\Ventas\Documento\Documento;
use App\Ventas\Pedido;
use Illuminate\Support\Facades\Auth;

class VentaDto
{
    public function prepararDtoFromAnticipoConsumo(object $datos_validados): array
    {
        $dto    =   [];

        //========== CAJA ===========
        $dto['caja_id']             =   $datos_validados->caja_id;
        $dto['caja_nombre']         =   $datos_validados->caja_nombre;
        $dto['caja_movimiento_id']  =   $datos_validados->caja_movimiento_id;

        //========= FECHAS ========
        $dto['fecha_documento']     =   $datos_validados->fecha_documento;
        $dto['fecha_atencion']      =   $datos_validados->fecha_atencion;

        //====== CONDICIÓN ========
        $dto['fecha_vencimiento']       =   $datos_validados->fecha_vencimiento;
        $dto['condicion_id']            =   $datos_validados->condicion_id;
        $dto['condicion_pago_nombre']   =   $datos_validados->condicion_pago_nombre;

        //======== PAGO ========
        $dto['estado_pago']             =   'PAGADA';

        //======== EMPRESA ========
        $dto['ruc_empresa']                 =   $datos_validados->ruc_empresa;
        $dto['empresa']                     =   $datos_validados->empresa;
        $dto['direccion_fiscal_empresa']    =   $datos_validados->direccion_fiscal_empresa;
        $dto['empresa_id']                  =   $datos_validados->empresa_id;

        //========= CLIENTE =======
        $dto['tipo_documento_cliente']  = $datos_validados->cliente->tipo_documento;
        $dto['documento_cliente']       = $datos_validados->cliente->documento;
        $dto['direccion_cliente']       = $datos_validados->cliente->direccion;
        $dto['cliente']                 = $datos_validados->cliente->nombre;
        $dto['cliente_id']              = $datos_validados->cliente->id;

        //======= TIPO VENTA ==========
        $dto['tipo_venta_id']           = $datos_validados->tipo_comprobante->id;   //boleta,factura,nota_venta
        $dto['tipo_venta_nombre']       = $datos_validados->tipo_comprobante->descripcion;

        //======== AUDITORIA =======
        $dto['observacion']         =   mb_strtoupper($datos_validados->observacion, 'UTF-8');
        $dto['user_id']             =   Auth::user()->id;
        $dto['registrador_nombre']  =   Auth::user()->usuario;

        //========= MONTOS Y MONEDA ========
        $montos =   $datos_validados->montos;

        $dto['sub_total']               =   $montos->monto_subtotal;
        $dto['monto_embalaje']          =   $montos->monto_embalaje;
        $dto['monto_envio']             =   $montos->monto_envio;
        $dto['total']                   =   $montos->monto_total;
        $dto['total_igv']               =   $montos->monto_igv;
        $dto['total_pagar']             =   $montos->monto_total_pagar;
        $dto['igv']                     =   $datos_validados->igv;
        $dto['monto_descuento']         =   $montos->monto_descuento;
        $dto['porcentaje_descuento']    =   $montos->porcentaje_descuento;
        $dto['moneda']                  =   1;

        //======== SUNAT ========
        $dto['mto_oper_gravadas_sunat'] =   $montos->mtoOperGravadasSunat;
        $dto['mto_igv_sunat']           =   $montos->mtoIgvSunat;
        $dto['total_impuestos_sunat']   =   $montos->totalImpuestosSunat;
        $dto['valor_venta_sunat']       =   $montos->valorVentaSunat;
        $dto['sub_total_sunat']         =   $montos->subTotalSunat;
        $dto['mto_imp_venta_sunat']     =   $montos->mtoImpVentaSunat;

        //======= SERIE Y CORRELATIVO ======
        $datos_correlativo  =   $datos_validados->datos_correlativo;
        $dto['serie']       =   $datos_correlativo->serie;
        $dto['correlativo'] =   $datos_correlativo->correlativo;

        //======= LEGENDA ======
        $dto['legenda']     =   $datos_validados->legenda;

        //========== SEDE =========
        $dto['sede_id']         =   $datos_validados->sede_id;

        //======== ALMACÉN ========
        $dto['almacen_id']      =   $datos_validados->almacen->id;
        $dto['almacen_nombre']  =   $datos_validados->almacen->descripcion;

        //========= ANTICIPO ========
        $dto['es_anticipo']     =   $datos_validados->es_anticipo;
        $dto['saldo_anticipo']  =   $montos->monto_total_pagar;

        //======= PEDIDO ========
        $dto['pedido_id']               =   $datos_validados->pedido_id;
        $dto['tipo_doc_venta_pedido']   =   $datos_validados->tipo_doc_venta_pedido;

        //========= MODO DESPACHO ==========
        $dto['modo']                    =   'VENTA';

        return $dto;
    }

    public function prepararDtoDetalleFromAnticipo(Documento $venta): array
    {
        $dto    =   [];

        $almacen    =   Almacen::where('tipo', 'FICTICIO')->where('estado', 'ANULADO')->first();
        $producto   =   Producto::where('tipo', 'FICTICIO')->where('estado', 'ANULADO')->first();
        $color      =   Color::where('tipo', 'FICTICIO')->where('estado', 'ANULADO')->first();
        $talla      =   Talla::where('tipo', 'FICTICIO')->where('estado', 'ANULADO')->first();
        $modelo     =   Modelo::where('tipo', 'FICTICIO')->where('estado', 'ANULADO')->first();

        $dto['almacen_id']          =   $almacen->id;
        $dto['almacen_nombre']      =   $almacen->descripcion;
        $dto['documento_id']        =   $venta->id;
        $dto['producto_id']         =   $producto->id;
        $dto['color_id']            =   $color->id;
        $dto['talla_id']            =   $talla->id;
        $dto['codigo']              =   $producto->codigo;
        $dto['unidad']              =   'NIU';
        $dto['nombre_producto']     =   $producto->nombre;
        $dto['nombre_color']        =   $color->descripcion;
        $dto['nombre_talla']        =   $talla->descripcion;
        $dto['nombre_modelo']       =   $modelo->descripcion;
        $dto['cantidad']            =   1;
        $dto['precio_unitario']     =   $venta->total_pagar;
        $dto['importe']             =   $venta->total_pagar;
        $dto['porcentaje_descuento']    =   0;
        $dto['precio_unitario_nuevo']   =   $venta->total_pagar;
        $dto['importe_nuevo']           =   $venta->total_pagar;
        $dto['monto_descuento']         =   0;
        $dto['cantidad_sin_cambio']     =   1;
        $dto['cantidad_cambiada']       =   0;
        $dto['estado_cambio_talla']     =   'SIN CAMBIOS';

        return $dto;
    }

    public function prepararDtoDetalleFromConsumo(Documento $venta, int $pedido_id): array
    {
        $dto    =   [];

        $pedido         =   Pedido::findOrFail($pedido_id);
        $almacen        =   Almacen::where('tipo', 'FICTICIO')->where('estado', 'ANULADO')->first();
        $producto       =   Producto::where('tipo', 'FICTICIO')->where('estado', 'ANULADO')->first();
        $color          =   Color::where('tipo', 'FICTICIO')->where('estado', 'ANULADO')->first();
        $talla          =   Talla::where('tipo', 'FICTICIO')->where('estado', 'ANULADO')->first();
        $modelo         =   Modelo::where('tipo', 'FICTICIO')->where('estado', 'ANULADO')->first();
        $lst_anticipos  =   Documento::where('pedido_id', $pedido_id)->where('tipo_doc_venta_pedido', 'ANTICIPO')->where('estado', 'ACTIVO')->get();
        $detalle_pedido =   Detalle::where('documento_id', $pedido->doc_venta_credito_id)->get()->toArray();

        foreach ($lst_anticipos as $anticipo) {
            $anticipo_dto   =   [];
            $anticipo_dto['almacen_id']          =   $almacen->id;
            $anticipo_dto['almacen_nombre']      =   $almacen->descripcion;
            $anticipo_dto['documento_id']        =   $venta->id;
            $anticipo_dto['producto_id']         =   $producto->id;
            $anticipo_dto['color_id']            =   $color->id;
            $anticipo_dto['talla_id']            =   $talla->id;
            $anticipo_dto['codigo_producto']     =   $producto->codigo;
            $anticipo_dto['unidad']              =   'NIU';
            $anticipo_dto['nombre_producto']     =   'ANTICIPO: FACTURA NRO. ' . $anticipo->serie . '-' . $anticipo->correlativo;
            $anticipo_dto['nombre_color']        =   $color->descripcion;
            $anticipo_dto['nombre_talla']        =   $talla->descripcion;
            $anticipo_dto['nombre_modelo']       =   $modelo->descripcion;
            $anticipo_dto['cantidad']            =   1;
            $anticipo_dto['precio_unitario']     =   $anticipo->total_pagar;
            $anticipo_dto['importe']             =   $anticipo->total_pagar;
            $anticipo_dto['porcentaje_descuento']    =   0;
            $anticipo_dto['precio_unitario_nuevo']   =   $anticipo->total_pagar;
            $anticipo_dto['importe_nuevo']           =   $anticipo->total_pagar;
            $anticipo_dto['monto_descuento']         =   0;
            $anticipo_dto['cantidad_sin_cambio']     =   1;
            $anticipo_dto['cantidad_cambiada']       =   0;
            $anticipo_dto['estado_cambio_talla']     =   'SIN CAMBIOS';
            $anticipo_dto['anticipo_id']             =   $anticipo->id;
            $anticipo_dto['anticipo_serie']          =   $anticipo->serie . '-' . $anticipo->correlativo;
            $anticipo_dto['tipo']                    =   'SERVICIO';

            $dto[]  =   $anticipo_dto;
        }

        foreach ($detalle_pedido as $item) {
            $_item   =   $item;
            unset($_item['id']);
            unset($_item['estado']);
            unset($_item['eliminado']);
            unset($_item['created_at']);
            unset($_item['updated_at']);
            $_item['documento_id']  =   $venta->id;

            $dto[]  =   $_item;
        }

        return $dto;
    }
}
