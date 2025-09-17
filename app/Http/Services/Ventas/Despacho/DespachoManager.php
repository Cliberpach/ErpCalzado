<?php

namespace App\Http\Services\Ventas\Despacho;

class DespachoManager
{
    private DespachoService $s_despacho;

    public function __construct() {
        $this->s_despacho      =   new DespachoService();
    }

    public function generarDespachoDefecto(int $venta_id,string $modo = 'VENTA') {
        $this->s_despacho->generarDespachoDefecto($venta_id,$modo);
    }

    public function store(array $datos){
        $this->s_despacho->store($datos);
    }

    public function update(array $datos){
        $this->s_despacho->update($datos);
    }

}
