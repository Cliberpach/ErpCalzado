<?php

use Illuminate\Support\Facades\Route;

Route::prefix('cuentas')->group(function () {

    Route::prefix('cuentaProveedor')->group(function () {
        Route::get('index', 'Compras\CuentaProveedorController@index')->name('cuentaProveedor.index');
        Route::get('getTable', 'Compras\CuentaProveedorController@getTable')->name('cuentaProveedor.getTable');
        Route::get('getDatos', 'Compras\CuentaProveedorController@getDatos')->name('cuentaProveedor.getDatos');
        Route::post('detallePago', 'Compras\CuentaProveedorController@detallePago')->name('cuentaProveedor.detallePago');
        Route::get('consulta', 'Compras\CuentaProveedorController@consulta')->name('cuentaProveedor.consulta');
        Route::get('reporte/{id}', 'Compras\CuentaProveedorController@reporte')->name('cuentaProveedor.reporte');
        Route::get('imagen/{id}', 'Compras\CuentaProveedorController@imagen')->name('cuentaProveedor.imagen');
    });

    Route::prefix('cuentaCliente')->group(function () {
        Route::get('index', 'Ventas\CuentaClienteController@index')->name('cuentaCliente.index');
        Route::get('getTable', 'Ventas\CuentaClienteController@getTable')->name('cuentaCliente.getTable');
        Route::get('getDatos/{id}', 'Ventas\CuentaClienteController@getDatos')->name('cuentaCliente.getDatos');
        Route::post('detallePago', 'Ventas\CuentaClienteController@detallePago')->name('cuentaCliente.detallePago');
        Route::get('detalle', 'Ventas\CuentaClienteController@detalle')->name('cuentaCliente.detalle');
        Route::get('consulta', 'Ventas\CuentaClienteController@consulta')->name('cuentaCliente.consulta');
        Route::get('reporte/{id}', 'Ventas\CuentaClienteController@reporte')->name('cuentaCliente.reporte');
        Route::get('imagen/{id}', 'Ventas\CuentaClienteController@imagen')->name('cuentaCliente.imagen');
        Route::post('generar-comprobante', 'Ventas\CuentaClienteController@generarComprobantePago')->name('cuentaCliente.generarComprobantePago');
    });
});
