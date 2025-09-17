<?php

namespace App\Http\Services\Ventas\Ventas;

use App\Ventas\Documento\Documento;

class VentaManager
{
    private VentaService $s_venta;

    public function __construct() {
        $this->s_venta      =   new VentaService();
    }

    public function registrar(array $datos):Documento {
        return $this->s_venta->registrar($datos);
    }

    public function storePago(array $datos){
        $this->s_venta->storePago($datos);
    }

    public function update(array $datos,int $id):Documento{
        return $this->s_venta->update($datos,$id);
    }

}
