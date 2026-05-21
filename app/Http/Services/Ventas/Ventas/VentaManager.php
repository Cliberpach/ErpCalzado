<?php

namespace App\Http\Services\Ventas\Ventas;

use App\Ventas\Documento\Documento;

class VentaManager
{
    private VentaService $s_venta;

    public function __construct()
    {
        $this->s_venta      =   new VentaService();
    }

    public function store(array $datos): Documento
    {
        return $this->s_venta->store($datos);
    }

    public function storePago(array $datos)
    {
        $this->s_venta->storePago($datos);
    }

    public function getVoucherPdf(int $id, int $size): array
    {
        return $this->s_venta->getVoucherPdf($id, $size);
    }

    public function update(array $datos, int $id): Documento
    {
        return $this->s_venta->update($datos, $id);
    }

    public function getColoresTallas($almacen_id, $producto_id)
    {
        return $this->s_venta->getColoresTallas($almacen_id, $producto_id);
    }

    public function getStocksMatriz(array $params): object
    {
        return $this->s_venta->getStocksMatriz($params);
    }

    public function queryStockDisponible(array $filters): \Illuminate\Database\Query\Builder
    {
        return $this->s_venta->queryStockDisponible($filters);
    }
}
