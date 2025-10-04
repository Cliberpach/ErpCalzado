<?php

namespace App\Http\Services\Pedidos\Pedidos;

use App\Ventas\Pedido;

class PedidoManager
{
    private PedidoService $s_pedido;

    public function __construct() {
        $this->s_pedido = new PedidoService();
    }

    public function store(array $datos):array{
        return $this->s_pedido->store($datos);
    }

    public function facturar(array $datos):object {
        return $this->s_pedido->facturar($datos);
    }

    public function generarDocumentoVenta(array $datos):int {
        return $this->s_pedido->generarDocumentoVenta($datos);
    }
}
