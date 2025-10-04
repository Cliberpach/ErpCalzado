<?php

namespace App\Http\Services\Cuentas\Cliente;

use Exception;

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
}
