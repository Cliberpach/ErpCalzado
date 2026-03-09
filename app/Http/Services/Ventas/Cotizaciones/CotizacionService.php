<?php

namespace App\Http\Services\Ventas\Cotizaciones;

use App\Http\Services\Almacen\Productos\ProductoService;
use App\Http\Services\Almacen\Tallas\TallaService;
use App\Http\Services\Pedidos\Pedidos\PedidoService;
use App\Models\Ventas\Cotizacion\Cotizacion;
use App\Ventas\Pedido;

class CotizacionService
{
    private CalculosService $sc_calculos;
    private CotizacionRepository $s_repository;
    private CotizacionValidacion $s_validaciones;
    private PedidoService $s_pedido;
    private CotizacionDTOS $s_dto;
    private CotizacionMapper $s_mapper;

    public function __construct()
    {
        $this->sc_calculos      =   new CalculosService();
        $this->s_repository     =   new CotizacionRepository();
        $this->s_validaciones   =   new CotizacionValidacion();
        $this->s_pedido         =   new PedidoService();
        $this->s_dto            =   new CotizacionDTOS();
        $this->s_mapper         =   new CotizacionMapper();
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
        $dto_detalle    =   $this->s_dto->prepararDtoDetalle($lstCotizacion, $cotizacion);
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
        $dto_detalle    =   $this->s_dto->prepararDtoDetalle($lstCotizacion, $cotizacion);
        $this->s_repository->registrarDetalleCotizacion($dto_detalle);

        return $cotizacion;
    }

    public function generarPedido(array $datos): Pedido
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

    public function getColoresTallas(int $almacen_id, int $producto_id)
    {
        $s_producto     =   new ProductoService();
        $s_talla        =   new TallaService();

        $precios_venta  =   $s_producto->getPreciosVenta($producto_id);
        $colores        =   $s_producto->getProductoColores($almacen_id, $producto_id);
        $stocks         =   $s_producto->getProductoStocks($almacen_id, $producto_id);
        $tallas         =   $s_talla->getTallas();

        $producto_color_tallas  =   null;
        if (count($colores) > 0) {
            $producto_color_tallas  =   $this->s_mapper->formatearColoresTallas($colores, $stocks, $tallas);
        }

        return (object)[
            'producto_color_tallas' =>  $producto_color_tallas,
            '_precios_venta'        =>  $precios_venta
        ];
    }
}
