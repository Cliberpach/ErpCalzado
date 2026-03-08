<?php

namespace App\Http\Services\Ventas\Cotizaciones;

use App\Models\Ventas\Cotizacion\Cotizacion;
use App\Ventas\Pedido;

class CotizacionManager
{
    private CotizacionService $s_cotizacion;

    public function __construct()
    {
        $this->s_cotizacion = new CotizacionService();
    }

    public function store(array $datos): Cotizacion
    {
        return $this->s_cotizacion->store($datos);
    }

    public function update(array $datos, int $id): Cotizacion
    {
        return $this->s_cotizacion->update($datos, $id);
    }

    public function generarPedido(array $datos): Pedido
    {
        return $this->s_cotizacion->generarPedido($datos);
    }

    public function getColoresTallas(int $almacen_id, int $producto_id)
    {
        return $this->s_cotizacion->getColoresTallas($almacen_id, $producto_id);
    }
}
