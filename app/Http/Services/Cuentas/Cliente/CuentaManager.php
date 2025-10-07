<?php

namespace App\Http\Services\Cuentas\Cliente;

use App\Ventas\Documento\Documento;

class CuentaManager
{
    private CuentaService $s_cuenta;

    public function __construct()
    {
        $this->s_cuenta     =   new CuentaService();
    }

    public function pagar(array $datos, int $cuenta_id)
    {
        $this->s_cuenta->pagar($datos, $cuenta_id);
    }

    public function decrementarSaldo(int $cuenta_id, float $monto)
    {
        $this->s_cuenta->decrementarSaldo($cuenta_id, $monto);
    }

    public function generarComprobantePago(array $datos):Documento{
        return $this->s_cuenta->generarComprobantePago($datos);
    }
}
