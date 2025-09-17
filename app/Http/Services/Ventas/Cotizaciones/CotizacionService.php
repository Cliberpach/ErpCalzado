<?php

namespace App\Http\Services\Ventas\Cotizaciones;

use App\Almacenes\Almacen;
use App\Http\Services\Pedidos\Pedidos\PedidoService;
use App\User;
use App\Ventas\Cliente;
use App\Ventas\Cotizacion;
use App\Ventas\CotizacionDetalle;
use App\Ventas\Pedido;

class CotizacionService
{
    private CalculosService $sc_calculos;
    private CotizacionRepository $s_repository;
    private CotizacionValidacion $s_validaciones;
    private PedidoService $s_pedido;
    private CotizacionDTOS $s_dto;

    public function __construct()
    {
        $this->sc_calculos      =   new CalculosService();
        $this->s_repository     =   new CotizacionRepository();
        $this->s_validaciones   =   new CotizacionValidacion();
        $this->s_pedido         =   new PedidoService();
        $this->s_dto            =   new CotizacionDTOS();
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
        $cotizacion =   $this->s_repository->actualizarCotizacion($id, $datos);

        //======== ELIMINAR DETALLE ANTERIOR ======
        $this->s_repository->eliminarDetalleCotizacion($id);
        $this->s_repository->registrarDetalleCotizacion($lstCotizacion, $cotizacion);

        return $cotizacion;
    }

    public function generarPedido(array $datos):Pedido
    {
        $datos      =   $this->s_validaciones->validacionGenerarPedido($datos);

        $cotizacion =   $datos['cotizacion'];
        $cliente    =   $datos['cliente'];


        //======== CREAR PEDIDO =========
        $dto    =   $this->s_dto->prepararDatosToPedido($datos);
        $pedido =   $this->s_pedido->storeFromCotizacion($dto);

        $cotizacion->pedido_id  =   $pedido->id;
        $cotizacion->update();

        return $pedido;
    }
}
