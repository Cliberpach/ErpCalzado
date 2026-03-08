<?php

namespace App\Http\Services\Ventas\Cotizaciones;

use App\Almacenes\Color;
use App\Almacenes\Talla;
use App\Models\Almacenes\Producto\Producto;
use App\Models\Ventas\Cotizacion\Cotizacion;
use App\Models\Ventas\Cotizacion\CotizacionDetalle;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Schema;

class CotizacionRepository
{
    public function registrarCotizacion(array $dto): Cotizacion
    {
        return Cotizacion::create($dto);
    }

    public function registrarDetalleCotizacion(array $dto_detalle)
    {
        CotizacionDetalle::insert($dto_detalle);
    }

    public function actualizarCotizacion(int $id, array $dto): Cotizacion
    {

        $cotizacion                     =   Cotizacion::findOrFail($id);
        $cotizacion->update($dto);

        return $cotizacion;
    }

    public function eliminarDetalleCotizacion(int $id)
    {
        CotizacionDetalle::where('cotizacion_id', $id)->delete();
    }

    public function getPreciosVentaProducto(int $producto_id)
    {
        $columns = Schema::getColumnListing('productos');

        $precioColumns = collect($columns)
            ->filter(function ($col) {
                return str_starts_with($col, 'precio_venta');
            })
            ->toArray();

        $selectColumns = array_merge([
            'p.id as producto_id',
            'p.nombre as producto_nombre'
        ], $precioColumns);

        $precios_venta = Producto::from('productos as p')
            ->where('p.id', $producto_id)
            ->where('p.estado', 'ACTIVO')
            ->select($selectColumns)
            ->first();

        return $precios_venta;
    }

    
}
