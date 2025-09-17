<?php

use App\Http\Controllers\Kardex\KCuenta\KCuentaController;
use App\Http\Controllers\Kardex\KStock\KStockController;
use Illuminate\Support\Facades\Route;

Route::prefix('kardex')->group(function () {

    // Consultas - Kardex - Producto
    Route::prefix('producto')->group(function () {
        Route::get('index', 'Consultas\Kardex\ProductoController@index')->name('consultas.kardex.producto.index');
        Route::post('getTable', 'Consultas\Kardex\ProductoController@getTable')->name('consultas.kardex.producto.getTable');
    });

    // Consultas - Kardex - Proveedor
    Route::prefix('proveedor')->group(function () {
        Route::get('index', 'Consultas\Kardex\ProveedorController@index')->name('consultas.kardex.proveedor.index');
        Route::post('getTable', 'Consultas\Kardex\ProveedorController@getTable')->name('consultas.kardex.proveedor.getTable');
    });

    // Consultas - Kardex - Cliente
    Route::prefix('cliente')->group(function () {
        Route::get('index', 'Consultas\Kardex\ClienteController@index')->name('consultas.kardex.cliente.index');
        Route::post('getTable', 'Consultas\Kardex\ClienteController@getTable')->name('consultas.kardex.cliente.getTable');
    });

    // Consultas - Kardex - Cliente
    Route::prefix('venta')->group(function () {
        Route::get('index', 'Consultas\Kardex\VentaController@index')->name('consultas.kardex.venta.index');
        Route::get('getTable', 'Consultas\Kardex\VentaController@getTable')->name('consultas.kardex.venta.getTable');
    });

    Route::prefix('stock')->group(function () {
        Route::get('index', [KStockController::class, 'index'])->name('consultas.kardex.stock.index');
        Route::get('getKStock', [KStockController::class, 'getKStock'])->name('consultas.kardex.stock.getKStock');
        Route::get('excelKardexStock', [KStockController::class, 'excelKardexStock'])->name('consultas.kardex.stock.excelKardexStock');
    });

    Route::prefix('cuenta')->group(function () {
        Route::get('index', [KCuentaController::class, 'index'])->name('consultas.kardex.cuenta.index');
        Route::get('getKCuenta', [KCuentaController::class, 'getKCuenta'])->name('consultas.kardex.cuenta.getKCuenta');
        Route::get('excel', [KCuentaController::class, 'excel'])->name('consultas.kardex.cuenta.excel');
        Route::get('pdf', [KCuentaController::class, 'pdf'])->name('consultas.kardex.cuenta.pdf');
    });
});
