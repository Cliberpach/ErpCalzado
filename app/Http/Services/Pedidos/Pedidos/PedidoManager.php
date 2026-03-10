<?php

namespace App\Http\Services\Pedidos\Pedidos;

class PedidoManager
{
    private PedidoService $s_pedido;

    public function __construct()
    {
        $this->s_pedido = new PedidoService();
    }

    public function facturar(array $datos): object
    {
        return $this->s_pedido->facturar($datos);
    }

    public function generarDocumentoVenta(array $datos): int
    {
        return $this->s_pedido->generarDocumentoVenta($datos);
    }

    public function getColoresTallas(int $almacen_id, int $producto_id)
    {
        return $this->s_pedido->getColoresTallas($almacen_id, $producto_id);
    }
}
