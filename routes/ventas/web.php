<?php

use Illuminate\Support\Facades\Route;


Route::prefix('ventas')->group(function () {

    // Clientes
    Route::prefix('clientes')->group(function () {

        Route::get('/', 'Ventas\ClienteController@index')->name('ventas.cliente.index');
        Route::get('/getTable', 'Ventas\ClienteController@getTable')->name('ventas.cliente.getTable');
        Route::get('/registrar', 'Ventas\ClienteController@create')->name('ventas.cliente.create');
        Route::post('/registrar', 'Ventas\ClienteController@store')->name('ventas.cliente.store');
        Route::post('/registrarFast', 'Ventas\ClienteController@storeFast')->name('ventas.cliente.storeFast');
        Route::get('/actualizar/{id}', 'Ventas\ClienteController@edit')->name('ventas.cliente.edit');
        Route::put('/actualizar/{id}', 'Ventas\ClienteController@update')->name('ventas.cliente.update');
        Route::get('/datos/{id}', 'Ventas\ClienteController@show')->name('ventas.cliente.show');
        Route::get('/destroy/{id}', 'Ventas\ClienteController@destroy')->name('ventas.cliente.destroy');
        Route::post('/getDocumento', 'Ventas\ClienteController@getDocumento')->name('ventas.cliente.getDocumento');
        Route::post('/getCustomer', 'Ventas\ClienteController@getCustomer')->name('ventas.cliente.getcustomer');
        Route::get('/getCliente/{tipo_documento}/{nro_documento}', 'Ventas\ClienteController@getCliente')->name('ventas.cliente.getCliente');


        //Tiendas
        Route::get('tiendas/index/{id}', 'Ventas\TiendaController@index')->name('clientes.tienda.index');
        Route::get('tiendas/getShop/{id}', 'Ventas\TiendaController@getShop')->name('clientes.tienda.shop');
        Route::get('tiendas/create/{id}', 'Ventas\TiendaController@create')->name('clientes.tienda.create');
        Route::post('tiendas/store/', 'Ventas\TiendaController@store')->name('clientes.tienda.store');
        Route::put('tiendas/update/{id}', 'Ventas\TiendaController@update')->name('clientes.tienda.update');
        Route::get('tiendas/destroy/{id}', 'Ventas\TiendaController@destroy')->name('clientes.tienda.destroy');
        Route::get('tiendas/show/{id}', 'Ventas\TiendaController@show')->name('clientes.tienda.show');
        Route::get('tiendas/actualizar/{id}', 'Ventas\TiendaController@edit')->name('clientes.tienda.edit');
    });

    // Cotizaciones
    Route::prefix('cotizaciones')->group(function () {
        Route::get('/', 'Ventas\CotizacionController@index')->name('ventas.cotizacion.index');
        Route::get('/getCotizaciones', 'Ventas\CotizacionController@getCotizaciones')->name('ventas.cotizacion.getCotizaciones');
        Route::get('/registrar', 'Ventas\CotizacionController@create')->name('ventas.cotizacion.create');
        Route::post('/registrar', 'Ventas\CotizacionController@store')->name('ventas.cotizacion.store');
        Route::get('/actualizar/{id}', 'Ventas\CotizacionController@edit')->name('ventas.cotizacion.edit');
        Route::put('/update/{id}', 'Ventas\CotizacionController@update')->name('ventas.cotizacion.update');
        Route::get('/datos/{id}', 'Ventas\CotizacionController@show')->name('ventas.cotizacion.show');
        Route::get('/destroy/{id}', 'Ventas\CotizacionController@destroy')->name('ventas.cotizacion.destroy');
        Route::get('reporte/{id}', 'Ventas\CotizacionController@report')->name('ventas.cotizacion.reporte');
        Route::get('email/{id}', 'Ventas\CotizacionController@email')->name('ventas.cotizacion.email');
        Route::get('documento/{id}', 'Ventas\CotizacionController@document')->name('ventas.cotizacion.documento');
        Route::get('/getProductoBarCode/{barcode}', 'Ventas\CotizacionController@getProductoBarCode')->name('ventas.cotizacion.getProductoBarCode');

        Route::get('generar-pedido-create/{id}', 'Ventas\CotizacionController@generarPedidoCreate')->name('ventas.cotizacion.generarPedidoCreate');
        Route::post('pedido/', 'Ventas\CotizacionController@generarPedido')->name('ventas.cotizacion.pedido');
        Route::get('/getProductos', 'Ventas\CotizacionController@getProductos')->name('ventas.cotizacion.getProductos');
        Route::get('/getColoresTallas/{almacen_id}/{producto_id}', 'Ventas\CotizacionController@getColoresTallas')->name('ventas.cotizacion.getColoresTallas');

        Route::get('nuevodocumento/{id}', 'Ventas\CotizacionController@newdocument')->name('ventas.cotizacion.nuevodocumento');

        Route::post('convertirADocVenta', 'Ventas\CotizacionController@convertirADocVenta')->name('ventas.cotizacion.convertirADocVenta');
        Route::post('devolverCantidades', 'Ventas\CotizacionController@devolverCantidades')->name('ventas.cotizacion.devolverCantidades');
    });

    // Documentos - cotizaciones
    Route::prefix('documentos')->group(function () {

        Route::get('index', 'Ventas\DocumentoController@index')->name('ventas.documento.index');
        Route::get('index-antiguo', 'Ventas\DocumentoController@indexAntiguo')->name('ventas.documento.indexAntiguo');
        Route::get('getVentas', 'Ventas\DocumentoController@getVentas')->name('ventas.getVentas');
        Route::get('getDocument', 'Ventas\DocumentoController@getDocument')->name('ventas.getDocument');
        Route::get('create', 'Ventas\DocumentoController@create')->name('ventas.documento.create');
        Route::post('create', 'Ventas\DocumentoController@getCreate')->name('ventas.documento.getCreate');
        Route::post('store', 'Ventas\DocumentoController@store')->name('ventas.documento.store');
        Route::get('edit/{id}', 'Ventas\DocumentoController@edit')->name('ventas.documento.edit');
        Route::put('update/{id}', 'Ventas\DocumentoController@update')->name('ventas.documento.update');
        Route::get('destroy/{id}', 'Ventas\DocumentoController@destroy')->name('ventas.documento.destroy');
        Route::get('show/{id}', 'Ventas\DocumentoController@show')->name('ventas.documento.show');
        Route::get('reporte/{id}', 'Ventas\DocumentoController@report')->name('ventas.documento.reporte');
        Route::get('tipoPago/{id}', 'Ventas\DocumentoController@TypePay')->name('ventas.documento.tipo_pago.existente');
        Route::get('getProductos', 'Ventas\DocumentoController@getProductos')->name('ventas.documento.getProductos');
        Route::get('/getColoresTallas/{almacen_id}/{producto_id}', 'Ventas\DocumentoController@getColoresTallas')->name('ventas.documento.getColoresTallas');
        Route::get('/getProductoBarCode/{barcode}', 'Ventas\DocumentoController@getProductoBarCode')->name('ventas.documento.getProductoBarCode');
        Route::post('/validarStockVentas', 'Ventas\DocumentoController@validarStockVentas')->name('ventas.documento.validarStockVentas');
        Route::post('/actualizarStockAdd', 'Ventas\DocumentoController@actualizarStockAdd')->name('ventas.documento.actualizarStockAdd');
        Route::post('/actualizarStockDelete', 'Ventas\DocumentoController@actualizarStockDelete')->name('ventas.documento.actualizarStockDelete');
        Route::post('/actualizarStockEdit', 'Ventas\DocumentoController@actualizarStockEdit')->name('ventas.documento.actualizarStockEdit');

        Route::get('convertir/create/{id}', 'Ventas\DocumentoController@convertirCreate')->name('ventas.documento.convertirCreate');
        Route::post('convertir/store', 'Ventas\DocumentoController@convertirStore')->name('ventas.documento.convertirStore');

        Route::get('guiaCreate/{id}', 'Ventas\DocumentoController@guiaCreate')->name('ventas.documento.guiaCreate');
        Route::post('guiaStore', 'Ventas\DocumentoController@guiaStore')->name('ventas.documento.guiaStore');

        Route::get('/getProductosVenta/', 'Ventas\DocumentoController@getProductosVenta')->name('ventas.documento.getProductosVenta');


        Route::post('getDocumentClient', 'Ventas\DocumentoController@getDocumentClient')->name('ventas.getDocumentClient');
        Route::post('/storePago', 'Ventas\DocumentoController@storePago')->name('ventas.documento.storePago');
        Route::post('/updatePago', 'Ventas\DocumentoController@updatePago')->name('ventas.documento.updatePago');
        Route::post('/getCuentas', 'Ventas\DocumentoController@getCuentas')->name('ventas.documento.getCuentas');
        Route::get('/getRecibosCaja/{cliente_id}', 'Ventas\DocumentoController@getRecibosCaja')->name('ventas.documento.getRecibosCaja');

        Route::post('quantity', 'Ventas\DocumentoController@quantity')->name('ventas.documento.cantidad');
        Route::post('devolverCantidades', 'Ventas\DocumentoController@devolverCantidades')->name('ventas.documento.devolverCantidades');
        Route::post('obtener/lote', 'Ventas\DocumentoController@returnLote')->name('ventas.documento.obtener.lote');
        Route::post('update/lote', 'Ventas\DocumentoController@updateLote')->name('ventas.documento.update.lote');


        Route::post('customers', 'Ventas\DocumentoController@customers')->name('ventas.customers');
        Route::post('customers-all', 'Ventas\DocumentoController@customers_all')->name('ventas.customers_all');
        Route::post('vouchersAvaible', 'Ventas\DocumentoController@vouchersAvaible')->name('ventas.vouchersAvaible');

        Route::post('regularizar-venta', 'Ventas\DocumentoController@regularizarVenta')->name('ventas.regularizarVenta');
        Route::get('cambiarTallas/create/{id}', 'Ventas\DocumentoController@cambiarTallasCreate')->name('venta.cambiarTallas.create');
        Route::get('getTallas/{almacen_id}/{producto_id}/{color_id}', 'Ventas\DocumentoController@getTallas')->name('venta.cambiarTallas.getTallas');
        Route::get('getStock/{almacen_id}/{producto_id}/{color_id}/{talla_id}', 'Ventas\DocumentoController@getStock')->name('venta.cambiarTallas.getStock');
        Route::post('validarStock', 'Ventas\DocumentoController@validarStock')->name('venta.cambiarTallas.validarStock');
        Route::get('validarCantCambiar/{documento_id}/{detalle_id}/{cantidad}', 'Ventas\DocumentoController@validarCantCambiar')->name('venta.cambiarTallas.validarCantCambiar');

        Route::post('devolverStockLogico', 'Ventas\DocumentoController@devolverStockLogico')->name('venta.cambiarTallas.devolverStockLogico');
        Route::post('cambiarTallas/store', 'Ventas\DocumentoController@cambiarTallasStore')->name('venta.cambiarTallas.cambiarTallasStore');
        Route::get('getHistorialCambiosTallas/{detalle_id}/{documento_id}', 'Ventas\DocumentoController@getHistorialCambiosTallas')->name('venta.cambiarTallas.getHistorialCambiosTallas');
    });

    //VENTAS-CAJA
    Route::prefix('caja')->group(function () {

        Route::get('index', 'Ventas\CajaController@index')->name('ventas.caja.index');
        Route::get('getDocument', 'Ventas\CajaController@getDocument')->name('ventas.caja.getDocument');
        Route::post('getDocumentClient', 'Ventas\CajaController@getDocumentClient')->name('ventas.caja.getDocumentClient');

        Route::post('/storePago', 'Ventas\CajaController@storePago')->name('ventas.caja.storePago');
        Route::post('/updatePago', 'Ventas\CajaController@updatePago')->name('ventas.caja.updatePago');
    });


     //Despachos
    Route::prefix('despachos')->group(function() {

        Route::get('/', 'Ventas\DespachoController@index')->name('ventas.despachos.index');
        Route::get('/getTable', 'Ventas\DespachoController@getTable')->name('ventas.despachos.getTable');
        Route::get('/showDetalles/{documento_id}', 'Ventas\DespachoController@showDetalles')->name('ventas.despachos.showDetalles');
        Route::get('/pdfBultos/{documento_id}/{despacho_id}/{nro_bultos}', 'Ventas\DespachoController@pdfBultos')->name('ventas.despachos.pdfBultos');
        Route::post('/setEmbalaje', 'Ventas\DespachoController@setEmbalaje')->name('ventas.despachos.setEmbalaje');
        Route::post('/setDespacho', 'Ventas\DespachoController@setDespacho')->name('ventas.despachos.setDespacho');
        Route::get('get-despacho/{documento_id}','Ventas\DespachoController@getDespacho')->name('ventas.despachos.getDespacho');
        Route::post('/updateDespacho', 'Ventas\DespachoController@updateDespacho')->name('ventas.despachos.updateDespacho');
        Route::post('/eliminarDespacho', 'Ventas\DespachoController@eliminarDespacho')->name('ventas.despachos.eliminarDespacho');

    });

    //Reservas
    Route::prefix('reservas')->group(function () {

        Route::get('/', 'Ventas\ReservaController@index')->name('ventas.reservas.index');
        Route::get('/getTable', 'Ventas\ReservaController@getTable')->name('ventas.reservas.getTable');
        Route::get('/showDetalles/{documento_id}', 'Ventas\ReservaController@showDetalles')->name('ventas.reservas.showDetalles');
        // Route::get('/pdfBultos/{documento_id}/{despacho_id}/{nro_bultos}', 'Ventas\DespachoController@pdfBultos')->name('ventas.despachos.pdfBultos');
        // Route::post('/setEmbalaje', 'Ventas\DespachoController@setEmbalaje')->name('ventas.despachos.setEmbalaje');
        // Route::post('/setDespacho', 'Ventas\DespachoController@setDespacho')->name('ventas.despachos.setDespacho');
        // Route::get('get-despacho/{documento_id}','Ventas\DespachoController@getDespacho')->name('ventas.despachos.getDespacho');
        // Route::post('/updateDespacho', 'Ventas\DespachoController@updateDespacho')->name('ventas.despachos.updateDespacho');
        // Route::post('/eliminarDespacho', 'Ventas\DespachoController@eliminarDespacho')->name('ventas.despachos.eliminarDespacho');

    });

    //GUIAS DE REMISION
    Route::prefix('guiasremision')->group(function () {

        Route::get('index', 'Ventas\GuiaController@index')->name('ventas.guiasremision.index');
        Route::get('getGuia', 'Ventas\GuiaController@getGuias')->name('ventas.getGuia');
        // Route::get('create/{id}', 'Ventas\GuiaController@create')->name('ventas.guiasremision.create');
        Route::get('create', 'Ventas\GuiaController@create')->name('ventas.guiasremision.create');
        Route::post('store', 'Ventas\GuiaController@store')->name('ventas.guiasremision.store');
        Route::put('update/{id}', 'Ventas\GuiaController@update')->name('ventas.guiasremision.update');
        Route::post('destroy', 'Ventas\GuiaController@destroy')->name('ventas.guiasremision.delete');
        Route::get('show/{id}', 'Ventas\GuiaController@show')->name('ventas.guiasremision.show');
        Route::get('reporte/{id}', 'Ventas\GuiaController@report')->name('ventas.guiasremision.reporte');
        Route::get('tiendaDireccion/{id}', 'Ventas\GuiaController@tiendaDireccion')->name('ventas.guiasremision.tienda_direccion');
        Route::post('sunat/guia', 'Ventas\GuiaController@sunat')->name('ventas.guiasremision.sunat');
        Route::post('consulta_ticket/guia/', 'Ventas\GuiaController@consulta_ticket')->name('ventas.guiasremision.consultar');
        Route::get('getXml/{guia_id}', 'Ventas\GuiaController@getXml')->name('ventas.guiasremision.getXml');
        Route::get('getCdr/{guia_id}', 'Ventas\GuiaController@getCdr')->name('ventas.guiasremision.getCdr');
        Route::get('/getProductos/', 'Ventas\GuiaController@getProductos')->name('ventas.guiasremision.getProductos');
        Route::get('/getColoresTallas/{almacen_id}/{producto_id}', 'Ventas\GuiaController@getColoresTallas')->name('ventas.guiasremision.getColoresTallas');
    });

    //VENTAS-RESÚMENES
    Route::prefix('resumenes')->group(function () {

        Route::get('index', 'Ventas\ResumenController@index')->name('ventas.resumenes.index');
        Route::get('getComprobantes/{fechaComprobantes}/{sede_id}', 'Ventas\ResumenController@getComprobantes')->name('ventas.resumenes.getComprobantes');
        Route::get('getStatus/{sede_id}', 'Ventas\ResumenController@isActive')->name('ventas.resumenes.getStatus');
        Route::get('getXml/{resumen_id}', 'Ventas\ResumenController@getXml')->name('ventas.resumenes.getXml');
        Route::get('getCdr/{resumen_id}', 'Ventas\ResumenController@getCdr')->name('ventas.resumenes.getCdr');
        Route::post('enviarSunat', 'Ventas\ResumenController@sendSunat')->name('ventas.resumenes.enviarSunat');

        Route::post('/store', 'Ventas\ResumenController@store')->name('ventas.resumenes.store');
        Route::post('/consultar', 'Ventas\ResumenController@consultarTicket')->name('ventas.resumenes.consultar');
        Route::post('/reenviar', 'Ventas\ResumenController@reenviarSunat')->name('ventas.resumenes.reenviar');
        Route::get('getResumenes', 'Ventas\ResumenController@getResumenes')->name('ventas.resumenes.getResumenes');
        Route::get('getDetalles/{resumen_id}', 'Ventas\ResumenController@getDetallesResumen')->name('ventas.resumenes.getDetalles');
    });
});
