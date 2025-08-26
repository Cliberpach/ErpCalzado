<?php

namespace App\Http\Services\Ventas\Cotizaciones;

use App\Ventas\Cotizacion;

class CotizacionManager
{
    private CotizacionService $s_cotizacion;

    public function __construct() {
        $this->s_cotizacion = new CotizacionService();
    }

    public function store(array $datos):Cotizacion {
        return $this->s_cotizacion->store($datos);
    }

    public function update(array $datos, int $id):Cotizacion{
        return $this->s_cotizacion->update($datos,$id);
    }
}
