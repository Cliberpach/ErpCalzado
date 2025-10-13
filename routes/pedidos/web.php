<?php

use App\Http\Controllers\Kardex\KCuenta\KCuentaController;
use App\Http\Controllers\Kardex\KStock\KStockController;
use App\Http\Controllers\Pedidos\PedidoController;
use Illuminate\Support\Facades\Route;

Route::prefix('pedidos')->middleware('auth')->group(function () {


    //PEDIDOS-PEDIDOS
    Route::prefix('pedidos')->group(function () {
        Route::get('index', 'Pedidos\PedidoController@index')->name('pedidos.pedido.index');
        Route::get('getTable', 'Pedidos\PedidoController@getTable')->name('pedidos.pedido.getTable');
        Route::get('create', 'Pedidos\PedidoController@create')->name('pedidos.pedido.create');
        Route::post('atender', 'Pedidos\PedidoController@atender')->name('pedidos.pedido.atender');
        Route::post('validar-cantidad-atender', 'Pedidos\PedidoController@validarCantidadAtender')->name('pedidos.pedido.validarCantidadAtender');
        Route::post('store', 'Pedidos\PedidoController@store')->name('pedidos.pedido.store');
        Route::post('generar-doc-venta', 'Pedidos\PedidoController@generarDocumentoVenta')->name('pedidos.pedido.generarDocumentoVenta');
        Route::put('update/{id}', 'Pedidos\PedidoController@update')->name('pedidos.pedido.update');
        Route::post('comprobante-consumo', 'Pedidos\PedidoController@generarComprobanteConsumo')->name('pedidos.pedido.generarComprobanteConsumo');

        Route::get('facturar-create/{id}', [PedidoController::class, 'facturarCreate'])->name('pedidos.pedido.facturar-create');
        Route::post('facturar-store/', 'Pedidos\PedidoController@facturarStore')->name('pedidos.pedido.facturar-store');

        Route::get('/getProductos/', 'Pedidos\PedidoController@getProductos')->name('pedidos.pedido.getProductos');


        Route::get('edit/{id}', 'Pedidos\PedidoController@edit')->name('pedidos.pedido.edit');
        //===== PARÁMETROS PARA VALIDAR CANTIDAD ATENDIDA: PEDIDOID,LISTADO DE PRODUCTOS(PRODUCTOID,COLORID,TALLAID,CANTIDAD) =======
        Route::post('validarCantidadAtendida', 'Pedidos\PedidoController@validarCantidadAtendida')->name('pedidos.pedido.validarCantidadAtendida');

        Route::delete('pedidos/{id}', 'Pedidos\PedidoController@destroy')->name('pedidos.pedido.destroy');
        Route::get('getProductosByModelo/{modelo_id}', 'Pedidos\PedidoController@getProductosByModelo')->name('pedidos.pedido.getProductosByModelo');
        Route::get('/getColoresTallas/{almacen_id}/{producto_id}', 'Pedidos\PedidoController@getColoresTallas')->name('pedidos.pedido.getColoresTallas');
        Route::get('reporte/{id}', 'Pedidos\PedidoController@report')->name('pedidos.pedido.reporte');
        Route::get('validar-tipo-venta/{comprobante_id}', 'Pedidos\PedidoController@validarTipoVenta')->name('pedidos.pedido.validarTipoVenta');
        Route::get('get-atencion-detalles/{pedido_id}/{documento_id}', 'Pedidos\PedidoController@getAtencionDetalles')->name('pedidos.pedido.getAtencionDetalles');
        Route::get('get-atenciones-pedido/{pedido_id}', 'Pedidos\PedidoController@getAtenciones')->name('pedidos.pedido.getAtenciones');
        Route::get('get-pedido-detalles/{pedido_id}', 'Pedidos\PedidoController@getPedidoDetalles')->name('pedidos.pedido.getPedidoDetalles');
        Route::post('devolver-stock-logico', 'Pedidos\PedidoController@devolverStockLogico')->name('pedidos.pedido.devolverStockLogico');
        Route::get('getExcel/{fecha_inicio?}/{fecha_fin?}/{estado?}', 'Pedidos\PedidoController@getExcel')->name('pedidos.pedido.getExcel');
        Route::get('getCliente/{pedido_id}', 'Pedidos\PedidoController@getCliente')->name('pedidos.pedido.getCliente');
    });

    //======= PEDIDOS - DETALLES =======
    Route::prefix('detalles')->group(function () {
        Route::get('index', 'Pedidos\DetalleController@index')->name('pedidos.pedidos_detalles.index');
        Route::get('getTable', 'Pedidos\DetalleController@getTable')->name('pedidos.pedidos_detalles.getTable');
        Route::get('getDetallesAtenciones/{pedido_id}/{producto_id}/{color_id}/{talla_id}', 'Pedidos\DetalleController@getDetallesAtenciones')->name('pedidos.pedidos_detalles.getDetallesAtenciones');
        Route::get('getDetallesDespachos/{pedido_id}/{producto_id}/{color_id}/{talla_id}', 'Pedidos\DetalleController@getDetallesDespachos')->name('pedidos.pedidos_detalles.getDetallesDespachos');
        Route::get('getDetallesDevoluciones/{pedido_id}/{producto_id}/{color_id}/{talla_id}', 'Pedidos\DetalleController@getDetallesDevoluciones')->name('pedidos.pedidos_detalles.getDetallesDevoluciones');
        Route::get('getDetallesFabricaciones/{pedido_id}/{producto_id}/{color_id}/{talla_id}', 'Pedidos\DetalleController@getDetallesFabricaciones')->name('pedidos.pedidos_detalles.getDetallesFabricaciones');
        Route::post('llenarCantEnviada/', 'Pedidos\DetalleController@llenarCantEnviada')->name('pedidos.pedidos_detalles.llenarCantEnviada');
        Route::post('generarOrdenProduccion/', 'Pedidos\DetalleController@generarOrdenProduccion')->name('pedidos.pedidos_detalles.generarOrdenProduccion');
        Route::get('getExcel/{pedido_detalle_estado?}/{cliente_id?}/{modelo_id?}/{producto_id?}', 'Pedidos\DetalleController@getExcel')->name('pedidos.pedidos_detalles.getExcel');
        Route::get('getPdf/{pedido_detalle_estado?}/{cliente_id?}/{modelo_id?}/{producto_id?}', 'Pedidos\DetalleController@getPdf')->name('pedidos.pedidos_detalles.getPdf');
    });

    //======= ÓRDENES - PEDIDO =======
    Route::prefix('ordenes')->group(function () {
        Route::get('index', 'Pedidos\OrdenProduccionController@index')->name('pedidos.ordenes_produccion.index');
        Route::get('getTable', 'Pedidos\OrdenProduccionController@getTable')->name('pedidos.ordenes_produccion.getTable');
        Route::get('getDetalle/{orden_produccion_id}', 'Pedidos\OrdenProduccionController@getDetalle')->name('pedidos.ordenes_produccion.getDetalle');
        Route::get('pdf/{orden_produccion_id}', 'Pedidos\OrdenProduccionController@pdf')->name('pedidos.ordenes_produccion.pdf');
        Route::get('create', 'Pedidos\OrdenProduccionController@create')->name('pedidos.ordenes_produccion.create');
        Route::get('/getProductosByModelo/{modelo_id}', 'Pedidos\OrdenProduccionController@getProductosByModelo')->name('pedidos.ordenes_produccion.getProductosByModelo');
        Route::get('/getColoresTallas/{producto_id}', 'Pedidos\OrdenProduccionController@getColoresTallas')->name('pedidos.ordenes_produccion.getColoresTallas');
        Route::post('store', 'Pedidos\OrdenProduccionController@store')->name('pedidos.ordenes_produccion.store');
    });
});
