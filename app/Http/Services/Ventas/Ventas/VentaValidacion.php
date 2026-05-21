<?php

namespace App\Http\Services\Ventas\Ventas;

use App\Almacenes\Almacen;
use App\Mantenimiento\Condicion;
use App\Mantenimiento\Cuenta\Cuenta;
use App\Mantenimiento\Empresa\Empresa;
use App\Mantenimiento\Sedes\Sede;
use App\Mantenimiento\TipoPago\TipoPago;
use App\User;
use App\Ventas\Cliente;
use App\Ventas\Documento\Documento;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class VentaValidacion
{

    public static function validacionStore($datos)
    {
        //========= VALIDAR LA SEDE ========
        if (empty($datos['sede_id'])) {
            throw new Exception("FALTA EL PARÁMETRO SEDE EN LA PETICIÓN!!!");
        }

        $sede   =   Sede::findOrFail($datos['sede_id']);

        if (!$sede) {
            throw new Exception("NO EXISTE LA SEDE EN LA BD!!!");
        }

        //======== VALIDAR DETALLE VENTA ======
        $lstVenta = json_decode($datos['productos_tabla']);
        if (count($lstVenta) === 0) {
            throw new Exception("EL DETALLE DE LA VENTA ESTÁ VACÍO!!!");
        }

        //========= VALIDANDO SI EL USUARIO ESTÁ EN UNA CAJA ABIERTA =======
        $caja_movimiento = movimientoUser();
        if (count($caja_movimiento) == 0) {
            throw new Exception("DEBES FORMAR PARTE DE UNA CAJA ABIERTA!!!");
        }

        //========= VALIDANDO TIPO COMPROBANTE ========
        $cliente = Cliente::find($datos['cliente_id']);
        $tipo_comprobante = DB::select(
            'SELECT td.* FROM tabladetalles AS td WHERE td.id = ?',
            [$datos['tipo_venta']]
        )[0];

        if ($cliente->tipo_documento !== 'RUC' && $tipo_comprobante->simbolo === '01') {
            throw new Exception("SE REQUIERE RUC PARA GENERAR FACTURA ELECTRÓNICA!!!");
        }
        if ($cliente->tipo_documento !== 'DNI' && $tipo_comprobante->simbolo === '03') {
            throw new Exception("SE REQUIERE DNI PARA GENERAR BOLETA ELECTRÓNICA!!!");
        }

        //====== CONDICIÓN PAGO =======
        $condicion_id = explode('-', $datos['condicion_id'], 2)[0];
        $condicion  = Condicion::find($condicion_id);

        $almacen = Almacen::find($datos['almacenSeleccionado']);

        // lstPagos puede venir como JSON string (FormData) o como array
        $lstPagos  = isset($datos['lstPagos'])
            ? (is_string($datos['lstPagos']) ? json_decode($datos['lstPagos'], true) : $datos['lstPagos'])
            : [];
        $isPay     = filter_var($datos['isPay'] ?? true, FILTER_VALIDATE_BOOLEAN);

        $tipo_pago_1 = null;
        $cuenta_pago_1 = null;
        $tipo_pago_2 = null;
        $cuenta_pago_2 = null;

        if ($condicion_id == 1 && $isPay && !empty($lstPagos)) {
            $p1 = $lstPagos[0];
            $tipo_pago_1   = TipoPago::find($p1['metodoPagoId'] ?? null);
            $cuenta_pago_1 = Cuenta::find($p1['cuentaPagoId'] ?? null);

            if (isset($lstPagos[1])) {
                $p2 = $lstPagos[1];
                $tipo_pago_2   = TipoPago::find($p2['metodoPagoId'] ?? null);
                $cuenta_pago_2 = Cuenta::find($p2['cuentaPagoId'] ?? null);
            }
        }

        $datos_validados = (object)[
            'sede_id'                   => $datos['sede_id'],
            'tipo_venta'                => $tipo_comprobante,
            'condicion'                 => $condicion,
            'cliente'                   => $cliente,
            'porcentaje_igv'            => Empresa::find(1)->igv,
            'almacen'                   => $almacen,
            'lstVenta'                  => $lstVenta,
            'monto_embalaje'            => $datos['monto_embalaje'] ?? null,
            'monto_envio'               => $datos['monto_envio'] ?? null,
            'empresa'                   => Empresa::find(1),
            'observacion'               => $datos['observacion'] ?? null,
            'usuario'                   => Auth::user(),
            'caja_movimiento'           => $caja_movimiento[0],
            'anticipo_consumido_id'     => $datos['anticipo_consumido_id'] ?? null,
            'anticipo_monto_consumido'  => $datos['anticipo_monto_consumido'] ?? 0,

            'facturar'                  =>  $datos['facturar'] ?? null,
            'pedido_id'                 =>  $datos['pedido_id'] ?? null,
            'modo'                      =>  $datos['modo'] ?? null,
            'facturado'                 =>  $datos['facturado'] ?? null,

            'anticipo_consumido_id'     =>  $datos['anticipo_consumido_id'] ?? null,
            'anticipo_monto_consumido'  =>  $datos['anticipo_monto_consumido'] ?? null,
            'doc_anticipo_serie'        =>  $datos['doc_anticipo_serie'] ?? null,
            'doc_anticipo_correlativo'  =>  $datos['doc_anticipo_correlativo'] ?? null,

            'documento_convertido'      =>  $datos['documento_convertido'] ?? null,
            'doc_regularizar_id'        =>  $datos['doc_regularizar_id'] ?? null,

            'tipo_doc_venta_pedido'     =>  $datos['tipo_doc_venta_pedido'] ?? null,

            'regularizar'               =>  $datos['regularizar'] ?? null,

            'ticket_credito'            =>  $datos['ticket_credito'] ?? null,

            'telefono'                  =>  $datos['telefono'] ?? null,

            //===== DATOS DE PAGO ========
            'lstPagos'                  =>  $lstPagos,
            'lstImgsPagos'              =>  $datos['lstImgsPagos'] ?? [],
            'isPay'                     =>  $isPay,
            'tipo_pago_1'               =>  $tipo_pago_1,
            'cuenta_pago_1'             =>  $cuenta_pago_1,
            'tipo_pago_2'               =>  $tipo_pago_2,
            'cuenta_pago_2'             =>  $cuenta_pago_2,

            'atencion'                  =>  $datos['atencion'] ?? null
        ];


        return $datos_validados;
    }

    public static function comprobanteActivo($sede_id, $tipo_comprobante)
    {

        $existe =   DB::select('SELECT
                    enf.*
                    FROM empresa_numeracion_facturaciones AS enf
                    WHERE
                    enf.empresa_id = 1
                    AND enf.sede_id = ?
                    AND enf.tipo_comprobante = ?', [$sede_id, $tipo_comprobante->id]);

        if (count($existe) === 0) {
            throw new Exception($tipo_comprobante->descripcion . ', NO ESTÁ ACTIVO EN LA EMPRESA!!!');
        }
    }

    public function validacionPagos(array $lst_pagos, float $total_pagar)
    {
        $errores     = [];
        $suma_montos = 0;
        $metodos     = [];

        foreach ($lst_pagos as $index => $pago) {
            $key = fn($campo) => "pagos_{$index}_{$campo}";

            if (empty($pago['metodoPagoId'])) {
                $errores[$key('metodoPagoId')][] = 'Seleccione un método de pago.';
            }

            if (empty($pago['fechaOperacionPago'])) {
                $errores[$key('fechaOperacionPago')][] = 'Ingrese la fecha.';
            }

            if (empty($pago['montoPago']) || floatval($pago['montoPago']) <= 0) {
                $errores[$key('montoPago')][] = 'El monto debe ser mayor a 0.';
            } else {
                $suma_montos += floatval($pago['montoPago']);
            }

            if (!empty($pago['metodoPagoId']) && $pago['metodoPagoId'] != 1) {
                if (empty($pago['cuentaPagoId'])) {
                    $errores[$key('cuentaPagoId')][] = 'Seleccione una cuenta.';
                }
                if (empty($pago['nroOperacionPago'])) {
                    $errores[$key('nroOperacionPago')][] = 'Ingrese nro de operación.';
                }
            }

            $metodos[] = $pago['metodoPagoId'] ?? null;
        }

        if (count($metodos) !== count(array_unique($metodos))) {
            $errores['pagos_general_error'][] = 'No pueden repetirse los métodos de pago.';
        }

        if (round($suma_montos, 2) !== round($total_pagar, 2)) {
            $errores['pagos_general_error'][] = 'La suma de pagos no coincide con el total.';
        }

        if (!empty($errores)) {
            $validator = Validator::make([], []);
            foreach ($errores as $campo => $mensajes) {
                foreach ($mensajes as $msg) {
                    $validator->errors()->add($campo, $msg);
                }
            }
            throw new ValidationException($validator);
        }

        return true;
    }

    public static function validacionUpdate($datos, $id)
    {

        $documento          =   Documento::find($id);
        if (!$documento) {
            throw new Exception("NO EXISTE EL DOCUMENTO DE VENTA EN LA BD");
        }
        if ($documento->estado_pago !== 'PENDIENTE') {
            throw new Exception("LOS DOCUMENTOS CON ESTADO DE PAGO : " . $documento->estado_pago . ", NO PUEDEN EDITARSE");
        }
        if ($documento->sunat == '1') {
            throw new Exception("LOS DOCUMENTOS ENVIADOS A SUNAT NO PUEDEN EDITARSE!!!");
        }
        if ($documento->sunat == '2') {
            throw new Exception("LOS DOCUMENTOS ANULADOS NO PUEDEN EDITARSE!!!");
        }
        if ($documento->cambio_talla == '1') {
            throw new Exception("LOS DOCUMENTOS CON CAMBIO DE TALLA NO PUEDEN EDITARSE!!!");
        }
        if ($documento->convert_en_id) {
            throw new Exception("LOS DOCUMENTOS CONVERTIDOS NO PUEDEN EDITARSE");
        }
        if ($documento->convert_de_id) {
            throw new Exception("LOS DOCUMENTOS RESULTANTES DE UNA CONVERSIÓN NO PUEDEN EDITARSE");
        }

        //========= ALMACÉN =======
        $almacen    =   Almacen::find($datos['almacen']);

        //========== CLIENTE =======
        $cliente            =   Cliente::find($datos['cliente']);
        $tipo_comprobante   =   DB::select('SELECT
                                td.*
                                FROM tabladetalles AS td
                                WHERE td.id = ?', [$datos['tipo_venta']])[0];

        if ($cliente->tipo_documento !== 'RUC' && $tipo_comprobante->simbolo === '01') {
            throw new Exception("SE REQUIERE RUC PARA GENERAR FACTURA ELECTRÓNICA!!!");
        }
        if ($cliente->tipo_documento !== 'DNI' && $tipo_comprobante->simbolo === '03') {
            throw new Exception("SE REQUIERE DNI PARA GENERAR BOLETA ELECTRÓNICA!!!");
        }

        //======== CONDICIÓN =======
        $condicion      =   Condicion::findOrFail($datos['condicion_id']);

        //========= VALIDAR DETALLE VENTA ======
        $lstVenta   =   json_decode($datos['lstVenta']);

        if (count($lstVenta) === 0) {
            throw new Exception("EL DETALLE DE LA VENTA ESTÁ VACÍO!!!");
        }

        //===== VALIDAR MONTOS =====
        $amounts    =   json_decode($datos['amounts']);

        $tipo_pago_1 = null;
        $cuenta_pago_1 = null;
        if ($condicion->id == 1) {
            $tipo_pago_1    =   TipoPago::find($datos['metodo_pago_1'])??null;
            $cuenta_pago_1  =   Cuenta::find($datos['cuenta_1']) ?? null;
        }

        return (object)[

            'facturar'                  =>  $datos['facturar'] ?? null,
            'ticket_credito'            =>  $datos['ticket_credito'] ?? null,
            'documento_convertido'      =>  $datos['documento_convertido'] ?? null,
            'regularizar'               =>  $datos['regularizar'] ?? null,
            'modo'                      =>  $datos['monto'] ?? null,

            'telefono'                  =>  $datos['telefono'] ?? null,
            'sede_id'                   =>  $documento->sede_id,
            'usuario'                   =>  User::findOrFail($documento->user_id),

            'documento'                 =>  $documento,
            'lstVenta'                  =>  $lstVenta,
            'monto_embalaje'            =>  $amounts->embalaje,
            'monto_envio'               =>  $amounts->envio,
            'condicion'                 =>  $condicion,
            'cliente'                   =>  $cliente,
            'tipo_venta'                =>  $tipo_comprobante,
            'almacen'                   =>  $almacen,
            'anticipo_consumido_id'     =>  $datos['anticipo_consumido_id'] ?? null,
            'anticipo_monto_consumido'  =>  $datos['anticipo_monto_consumido'] ?? 0,
            'observacion'               =>  mb_strtoupper($datos['observacion'], 'UTF-8'),

            //===== DATOS DE PAGO ========
            'metodoPagoId'              =>  $datos['metodo_pago_1'] ?? null,
            'cuentaPagoId'              =>  $datos['cuenta_1'] ?? null,
            'montoPago'                 =>  $datos['monto_1'] ?? null,
            'nroOperacionPago'          =>  $datos['nro_operacion_1'] ?? null,
            'imgPago'                   =>  $datos['img_pago_1'] ?? null,
            'fechaOperacionPago'        =>  $datos['fecha_operacion_1'] ?? null,

            'tipo_pago_1'               =>  $tipo_pago_1,
            'cuenta_pago_1'             =>  $cuenta_pago_1
        ];
    }
}
