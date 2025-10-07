<?php

namespace App\Http\Services\Cuentas\Cliente;

use App\Mantenimiento\Tabla\Detalle;
use App\Pos\MovimientoCaja;
use App\Ventas\Cliente;
use App\Ventas\DetalleCuentaCliente;
use Exception;
use Illuminate\Support\Facades\DB;

class CuentaValidacion
{

    public function validacionPago(array $datos)
    {
        $cuenta_cliente =   $datos['cuenta_cliente'];
        $cuenta_banco   =   $datos['cuenta_banco'];

        $movimiento     =    movimientoUser();

        if(count($movimiento) === 0){
            throw new Exception("DEBES PERTENECER A UNA CAJA PARA REALIZAR UN PAGO");
        }

        if ($datos['modo_pago'] != 1 && !$cuenta_banco) {
            throw new Exception("DEBE SELECCIONAR UNA CUENTA BANCARIA");
        }

        if (floatval($cuenta_cliente->saldo) - floatval($datos['cantidad']) < 0) {
            throw new Exception(
                "El pago excede el saldo pendiente: saldo pendiente S/ " . number_format($cuenta_cliente->saldo, 2, '.', ',') .
                    ", estás intentando pagar S/ " . number_format($datos['cantidad'], 2, '.', ',') . "."
            );
        }

        if ($datos['pago'] === "TODO") {
            if (floatval($cuenta_cliente->saldo) != floatval($datos['cantidad'])) {
                throw new Exception('Ocurrió un error, al parecer ingreso un monto diferente al saldo.');
            }
        }
    }

    public function validacionGenerarComprobante(array $datos):array{
        $pago   =   DetalleCuentaCliente::findOrFail($datos['pago_id']);
        if(!$pago){
            throw new Exception("NO EXISTE EL PAGO EN LA BD");
        }
        if($pago->comprobante_id){
            throw new Exception("EL PAGO YA TIENE COMPROBANTE GENERADO: ".$pago->comprobante_nro);
        }

        $venta_cuenta   =   DB::select(
                                'SELECT cd.sede_id,cd.pedido_id
                                FROM cuenta_cliente AS cc
                                INNER JOIN cotizacion_documento AS cd ON cd.id = cc.cotizacion_documento_id
                                WHERE cc.id = ?',[$pago->cuenta_cliente_id]
                            )[0];

        $caja_movimiento   =    MovimientoCaja::findOrFail($pago->mcaja_id);

        $datos['pago']                  =   $pago;
        $datos['sede']                  =   $venta_cuenta->sede_id;
        $datos['pedido_id']             =   $venta_cuenta->pedido_id;
        $datos['caja_movimiento_id']    =   $caja_movimiento->id;
        $datos['caja_id']               =   $caja_movimiento->caja_id;

        return $datos;
    }
}
