<?php

use Illuminate\Support\Facades\Route;

Route::prefix('cajas')->middleware('auth')->group(function () {

    //Caja
    Route::prefix('cajas')->group(function () {

        Route::get('/index', 'Pos\CajaController@index')->name('Caja.index');
        Route::get('/getCajas', 'Pos\CajaController@getCajas')->name('Caja.getCajas');
        Route::post('/store', 'Pos\CajaController@store')->name('Caja.store');
        Route::post('/update/{id}', 'Pos\CajaController@update')->name('Caja.update');
        Route::get('/destroy/{id}', 'Pos\CajaController@destroy')->name('Caja.destroy');
        //-------------------------Movimientos de Caja -------------------------
    });

    Route::prefix('movimientos')->group(function () {

        Route::get('index/movimiento', 'Pos\CajaController@indexMovimiento')->name('Caja.Movimiento.index');
        Route::get('getMovimientosCajas', 'Pos\CajaController@getMovimientosCajas')->name('Caja.get_movimientos_cajas');
        Route::get('getDatosAperturaCaja', 'Pos\CajaController@getDatosAperturaCaja')->name('Caja.getDatosAperturaCaja');

        Route::post('aperturaCaja', 'Pos\CajaController@aperturaCaja')->name('Caja.apertura');

        Route::post('cerrarCaja', 'Pos\CajaController@cerrarCaja')->name('Caja.cerrar');
        Route::get('estadoCaja', 'Pos\CajaController@estadoCaja')->name('Caja.estado');
        Route::get('cajaDatosCierre', 'Pos\CajaController@cajaDatosCierre')->name('Caja.datos.cierre');
        Route::get('verificarEstadoUser', 'Pos\CajaController@verificarEstadoUser')->name('Caja.movimiento.verificarestado');
        Route::get('repoteMovimiento/{id}', 'Pos\CajaController@reporteMovimiento')->name('Caja.reporte.movimiento');
        Route::get('caja/verificar-ventas/{movimiento_id}', 'Pos\CajaController@verificarVentasNoPagadas')->name('caja.movimiento.verificarVentasNoPagadas');
    });


    Route::prefix('egresos')->group(function () {
        Route::get('index', 'Egreso\EgresoController@index')->name('Egreso.index');
        Route::get('getEgresos', 'Egreso\EgresoController@getEgresos')->name('Egreso.getEgresos');
        Route::get('getEgreso/{id}', 'Egreso\EgresoController@getEgreso')->name('Egreso.getEgreso');
        Route::post('store', 'Egreso\EgresoController@store')->name('Egreso.store');
        Route::post('update/{id}', 'Egreso\EgresoController@update')->name('Egreso.update');
        Route::post('destroy/{id}', 'Egreso\EgresoController@destroy')->name('Egreso.destroy');
        Route::get('recibo/{size}', 'Egreso\EgresoController@recibo')->name('Egreso.recibo');
    });

    //========== RECIBOS CAJA ===========
    Route::prefix('recibos_caja')->group(function () {
        Route::get('index', 'ReciboCaja\ReciboCajaController@index')->name('recibos_caja.index');
        Route::get('getRecibosCaja', 'ReciboCaja\ReciboCajaController@getRecibosCaja')->name('recibos_caja.getRecibosCaja');
        Route::get('create/{pedido_id?}', 'ReciboCaja\ReciboCajaController@create')->name('recibos_caja.create')->middleware('recibos_caja.create');
        Route::get('edit/{recibo_caja_id}', 'ReciboCaja\ReciboCajaController@edit')->name('recibos_caja.edit')->middleware('recibos_caja.create');
        Route::post('store', 'ReciboCaja\ReciboCajaController@store')->name('recibos_caja.store');
        Route::get('buscarCajaApertUsuario', 'ReciboCaja\ReciboCajaController@buscarCajaApertUsuario')->name('recibos_caja.buscarCajaApertUsuario');
        Route::get('pdf/{size}/{recibo_caja_id}', 'ReciboCaja\ReciboCajaController@pdf')->name('recibos_caja.pdf');
        Route::put('update/{recibo_caja_id}', 'ReciboCaja\ReciboCajaController@update')->name('recibos_caja.update');
        Route::put('destroy/{recibo_caja_id}', 'ReciboCaja\ReciboCajaController@destroy')->name('recibos_caja.destroy');
        Route::get('detalles/{recibo_caja_id}', 'ReciboCaja\ReciboCajaController@detalles')->name('recibos_caja.detalles');


        // Route::get('getEgreso','Egreso\EgresoController@getEgreso')->name('Egreso.getEgreso');
        // Route::post('store','Egreso\EgresoController@store')->name('Egreso.store');
        // Route::post('update/{id}','Egreso\EgresoController@update')->name('Egreso.update');
        // Route::get('destroy/{id}','Egreso\EgresoController@destroy')->name('Egreso.destroy');
        // Route::get('recibo/{size}','Egreso\EgresoController@recibo')->name('Egreso.recibo');
    });
});
