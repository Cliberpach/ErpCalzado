<?php

namespace App\Http\Services\Ventas\Cotizaciones;

use App\Almacenes\Almacen;
use App\User;
use App\Ventas\Cliente;
use App\Ventas\Cotizacion;

class CotizacionService
{
    private CalculosService $sc_calculos;
    private CotizacionRepository $s_repository;

    public function __construct()
    {
        $this->sc_calculos  =   new CalculosService();
        $this->s_repository =   new CotizacionRepository();
    }

    public function store(array $datos): Cotizacion
    {
        $lstCotizacion      =   json_decode($datos['lstCotizacion']);
        $montos_cotizacion  =   json_decode($datos['montos_cotizacion']);

        $almacen                =   Almacen::find($datos['almacen']);
        $registrador            =   User::find($datos['registrador_id']);
        $cliente                =   Cliente::findOrFail($datos['cliente']);
        $datos['cliente']       =   $cliente;
        $datos['lstCotizacion'] =   $lstCotizacion;
        $datos['almacen']       =   $almacen;
        $datos['registrador']   =   $registrador;

        //======= CALCULANDO MONTOS ========
        $montos =   $this->sc_calculos->calcularMontos($datos);
        $datos['montos']    =   $montos;

        //======== REGISTRANDO MAESTRO COTIZACIÓN ======
        $cotizacion =   $this->s_repository->registrarCotizacion($datos);

        //======= REGISTRO DETALLE DE LA COTIZACIÓN =====
        $this->s_repository->registrarDetalleCotizacion($lstCotizacion, $cotizacion);

        return $cotizacion;
    }

    public function update(array $datos, int $id): Cotizacion
    {
        $lstCotizacion      =   json_decode($datos['lstCotizacion']);
        $montos_cotizacion  =   json_decode($datos['montos_cotizacion']);

        $almacen                =   Almacen::find($datos['almacen']);
        $registrador            =   User::find($datos['registrador_id']);
        $cliente                =   Cliente::findOrFail($datos['cliente']);
        $datos['cliente']       =   $cliente;
        $datos['lstCotizacion'] =   $lstCotizacion;
        $datos['almacen']       =   $almacen;
        $datos['registrador']   =   $registrador;

        //======= CALCULANDO MONTOS ========
        $montos =   $this->sc_calculos->calcularMontos($datos);
        $datos['montos']    =   $montos;

        //====== ACTUALIZAR ======
        $cotizacion =   $this->s_repository->actualizarCotizacion($id,$datos);

        //======== ELIMINAR DETALLE ANTERIOR ======
        $this->s_repository->eliminarDetalleCotizacion($id);
        $this->s_repository->registrarDetalleCotizacion($lstCotizacion,$cotizacion);

        return $cotizacion;
    }
}
