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

}
