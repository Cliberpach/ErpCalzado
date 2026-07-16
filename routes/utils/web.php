<?php

use App\Http\Controllers\Almacenes\AlmacenController;
use App\Http\Controllers\Almacenes\ProductoController;
use App\Http\Controllers\Seguridad\UserController;
use App\Http\Controllers\UtilidadesController;
use Illuminate\Support\Facades\Route;

Route::prefix('utilidades')->middleware('auth')->group(function () {
    // Route::get('getProductos', 'UtilidadesController@getProductos')->name('utilidades.getProductos');
    // Route::get('getColores', 'UtilidadesController@getColores')->name('utilidades.getColores');

    Route::get('consultarDocumento', 'UtilidadesController@consultarDocumento')->name('utilidades.consultarDocumento');
    Route::get('getClientes', 'Ventas\ClienteController@getClientes')->name('utilidades.getClientes');
    Route::get('getProductosTodos', 'Almacenes\ProductoController@getProductosTodos')->name('utilidades.getProductosTodos');
    Route::get('getProductosConStock', 'Almacenes\ProductoController@getProductosConStock')->name('utilidades.getProductosConStock');
    Route::get('getColoresTalla/{almacen_id}/{producto_id}', 'Almacenes\ProductoController@getColoresTalla')->name('utilidades.getColoresTalla');
    Route::get('validarCantidad', 'UtilidadesController@validarCantidad')->name('utilidades.validarCantidad');
    Route::get('getCajaMovimiento', 'UtilidadesController@getCajaMovimiento')->name('utilidades.getCajaMovimiento');
    Route::get('get-cuentas-metodo/{metodo_pago}', 'UtilidadesController@getCuentasPorMetodoPago')->name('utilidades.getCuentasPorMetodoPago');
    Route::get('getProductoBarCode', 'UtilidadesController@getProductoBarCode')->name('utilidades.getProductoBarCode');
    Route::get('getUsers', [UserController::class, 'getUsers'])->name('utilidades.getUsers');
    Route::get('get-almacenes', [AlmacenController::class, 'getAlmacenes'])->name('utilidades.getAlmacenes');
    Route::get('datatable-productos', [ProductoController::class, 'dataTableProducts'])->name('utilidades.dataTableProducts');
    Route::get('getProductosSimple', [UtilidadesController::class, 'getProductosSimple'])->name('utilidades.getProductosSimple');
    Route::get('getTallas', [UtilidadesController::class, 'getTallasEndpoint'])->name('utilidades.getTallas');
    Route::get('getColores', [UtilidadesController::class, 'getColores'])->name('utilidades.getColores');
    Route::get('getMarcas', [UtilidadesController::class, 'getMarcas'])->name('utilidades.getMarcas');
    Route::get('getCategorias', [UtilidadesController::class, 'getCategorias'])->name('utilidades.getCategorias');
    Route::get('getAlmacenes', 'UtilidadesController@getAlmacenes')->name('utilidades.getAlmacenes');
});
