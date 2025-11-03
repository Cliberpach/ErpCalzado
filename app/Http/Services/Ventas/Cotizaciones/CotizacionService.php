<?php

namespace App\Http\Services\Ventas\Cotizaciones;

use App\Almacenes\Almacen;
use App\Http\Services\Pedidos\Pedidos\PedidoService;
use App\Models\Ventas\Cotizacion\Cotizacion;
use App\User;
use App\Ventas\Cliente;
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
        $lstCotizacion              =   json_decode($datos['lstCotizacion']);
        $montos_cotizacion          =   json_decode($datos['montos_cotizacion']);
        $datos['montos_cotizacion'] =   $montos_cotizacion;

        //======= CALCULANDO MONTOS ========
        $montos             =   $this->sc_calculos->calcularMontos($datos);
        $datos['montos']    =   $montos;

        //======== REGISTRANDO MAESTRO COTIZACIÓN ======
        $dto        =   $this->s_dto->prepararDtoStore($datos);
        $cotizacion =   $this->s_repository->registrarCotizacion($dto);

        //======= REGISTRO DETALLE DE LA COTIZACIÓN =====
        $dto_detalle    =   $this->s_dto->prepararDtoDetalle($lstCotizacion,$cotizacion);
        $this->s_repository->registrarDetalleCotizacion($dto_detalle);

        return $cotizacion;
    }

    public function update(array $datos, int $id): Cotizacion
    {
        $lstCotizacion              =   json_decode($datos['lstCotizacion']);
        $montos_cotizacion          =   json_decode($datos['montos_cotizacion']);
        $datos['montos_cotizacion'] =   $montos_cotizacion;

        //======= CALCULANDO MONTOS ========
        $montos =   $this->sc_calculos->calcularMontos($datos);
    
        $datos['montos']    =   $montos;

        //====== ACTUALIZAR ======
        $dto        =   $this->s_dto->prepararDtoStore($datos);
        $cotizacion =   $this->s_repository->actualizarCotizacion($id, $dto);

        //======== ELIMINAR DETALLE ANTERIOR ======
        $this->s_repository->eliminarDetalleCotizacion($id);
        $dto_detalle    =   $this->s_dto->prepararDtoDetalle($lstCotizacion,$cotizacion);
        $this->s_repository->registrarDetalleCotizacion($dto_detalle);

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
