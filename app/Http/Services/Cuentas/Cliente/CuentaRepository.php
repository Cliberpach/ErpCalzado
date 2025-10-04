<?php

namespace App\Http\Services\Cuentas\Cliente;

use App\Ventas\CuentaCliente;
use App\Ventas\DetalleCuentaCliente;

class CuentaRepository
{

    public function registrarPago(array $datos): DetalleCuentaCliente
    {
        $pago                       =   new DetalleCuentaCliente();
        $pago->cuenta_cliente_id    =   $datos['cuenta_cliente']->id;
        $pago->mcaja_id             =   movimientoUser()[0]->movimiento_id;
        $pago->monto                =   $datos['cantidad'];
        $pago->importe              =   $datos['importe_venta'] ?? 0;
        $pago->efectivo             =   $datos['efectivo_venta'] ?? 0;
        $pago->tipo_pago_id         =   $datos['modo_pago'];
        $pago->observacion          =   $datos['pago'] . ' - ' . $datos['observacion'];
        $pago->fecha                =   $datos['fecha'];
        $pago->nro_operacion        =   $datos['nro_operacion'] ? mb_strtoupper($datos['nro_operacion']) : null;

        if ($datos['cuenta_banco']) {
            $cuenta_banco               =   $datos['cuenta_banco'];
            $pago->cuenta_id            =   $cuenta_banco->id;
            $pago->cuenta_nro_cuenta    =   $cuenta_banco->nro_cuenta;
            $pago->cuenta_cci           =   $cuenta_banco->cci;
            $pago->cuenta_banco_nombre  =   $cuenta_banco->banco_nombre;
            $pago->cuenta_moneda        =   $cuenta_banco->moneda;
        }

        $pago->save();

        $cuenta_cliente         =   $datos['cuenta_cliente'];
        $cuenta_cliente->saldo  -= floatval($datos['cantidad']);
        if (floatval($cuenta_cliente->saldo) == 0) {
            $cuenta_cliente->estado = 'PAGADO';
        }
        $cuenta_cliente->update();

        $pago->saldo = $cuenta_cliente->saldo;
        $pago->update();

        return $pago;
    }

    public function eliminarPorVentaId(int $id){
        $cuenta             =   CuentaCliente::where('cotizacion_documento_id',$id)->first();
        $cuenta->estado     =   'ANULADO';
        $cuenta->update();
    }
}
