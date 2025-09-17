<?php

use App\Http\Controllers\Mantenimiento\Cuentas\CuentaController;
use App\Http\Controllers\Mantenimiento\TipoPago\TipoPagoController;
use Illuminate\Support\Facades\Route;


Route::prefix('mantenimiento/')->group(function () {

    Route::prefix('tipo_pago/')->group(function () {
        Route::get('index', 'Mantenimiento\TipoPago\TipoPagoController@index')->name('mantenimiento.tipo_pago.index');
        Route::get('getTiposPago', 'Mantenimiento\TipoPago\TipoPagoController@getTiposPago')->name('mantenimiento.tipo_pago.getTiposPago');
        Route::post('store', 'Mantenimiento\TipoPago\TipoPagoController@store')->name('mantenimiento.tipo_pago.store');
        Route::put('/update/{id}', [TipoPagoController::class, 'update'])->name('mantenimiento.tipo_pago.update');
        Route::delete('/destroy/{id}', [TipoPagoController::class, 'destroy'])->name('mantenimiento.tipo_pago.destroy');
        Route::get('/asignar-cuentas/create/{id}', [TipoPagoController::class, 'asignarCuentasCreate'])->name('mantenimiento.tipo_pago.asignarCuentasCreate');
        Route::post('/asignar-cuentas/store', [TipoPagoController::class, 'asignarCuentasStore'])->name('mantenimiento.tipo_pago.asignarCuentasStore');
    });

    // Route::prefix('cuentas/')->group(function () {
    //     Route::get('index/{tipo_pago_id}', 'Mantenimiento\Cuentas\CuentaController@index')->name('mantenimiento.cuentas.index');
    //     Route::get('getCuentas/{tipo_pago_id}', 'Mantenimiento\Cuentas\CuentaController@getCuentas')->name('mantenimiento.cuentas.getCuentas');
    // });


    Route::prefix('cuentas/')->group(function () {
        Route::get('index/', 'Mantenimiento\Cuentas\CuentaController@index')->name('mantenimiento.cuentas.index');
        Route::get('getCuentas', 'Mantenimiento\Cuentas\CuentaController@getCuentas')->name('mantenimiento.cuentas.getCuentas');
        Route::post('store', 'Mantenimiento\Cuentas\CuentaController@store')->name('mantenimiento.cuentas.store');
        Route::put('update/{id}', 'Mantenimiento\Cuentas\CuentaController@update')->name('mantenimiento.cuentas.update');
        Route::delete('/destroy/{id}', [CuentaController::class, 'destroy'])->name('mantenimiento.cuentas.destroy');
    });

    Route::prefix('sedes/')->group(function () {

        Route::get('/', 'Mantenimiento\Sede\SedeController@index')->name('mantenimiento.sedes.index');
        Route::get('/create', 'Mantenimiento\Sede\SedeController@create')->name('mantenimiento.sedes.create');
        Route::get('/edit/{id}', 'Mantenimiento\Sede\SedeController@edit')->name('mantenimiento.sedes.edit');
        Route::post('/store', 'Mantenimiento\Sede\SedeController@store')->name('mantenimiento.sedes.store');
        Route::put('/update/{id}', 'Mantenimiento\Sede\SedeController@update')->name('mantenimiento.sedes.update');
        Route::get('/getSedes', 'Mantenimiento\Sede\SedeController@getSedes')->name('mantenimiento.sedes.getSedes');

        Route::get('/numeracion/create/{sede_id}', 'Mantenimiento\Sede\SedeController@numeracionCreate')->name('mantenimiento.sedes.numeracionCreate');
        Route::get('/numeracion/getNumeracion', 'Mantenimiento\Sede\SedeController@getNumeracion')->name('mantenimiento.sedes.getNumeracion');
        Route::post('/numeracion/store', 'Mantenimiento\Sede\SedeController@numeracionStore')->name('mantenimiento.sedes.numeracionStore');
    });

    Route::prefix('tablas/generales')->group(function () {
        Route::get('index', 'Mantenimiento\Tabla\GeneralController@index')->name('mantenimiento.tabla.general.index');
        Route::get('getTable', 'Mantenimiento\Tabla\GeneralController@getTable')->name('getTable');
        Route::put('update', 'Mantenimiento\Tabla\GeneralController@update')->name('mantenimiento.tabla.general.update');
    });

    Route::prefix('tablas/detalles')->group(function () {
        Route::get('index/{id}', 'Mantenimiento\Tabla\DetalleController@index')->name('mantenimiento.tabla.detalle.index');
        Route::get('getTable/{id}', 'Mantenimiento\Tabla\DetalleController@getTable')->name('getTableDetalle');
        Route::get('destroy/{id}', 'Mantenimiento\Tabla\DetalleController@destroy')->name('mantenimiento.tabla.detalle.destroy');
        Route::post('store', 'Mantenimiento\Tabla\DetalleController@store')->name('mantenimiento.tabla.detalle.store');
        Route::put('update', 'Mantenimiento\Tabla\DetalleController@update')->name('mantenimiento.tabla.detalle.update');
        Route::get('getDetail/{id}', 'Mantenimiento\Tabla\DetalleController@getDetail')->name('mantenimiento.tabla.detalle.getDetail');
        Route::post('/exist', 'Mantenimiento\Tabla\DetalleController@exist')->name('mantenimiento.tabla.detalle.exist');
    });
    //Empresas
    Route::prefix('empresas')->group(function () {
        Route::get('index', 'Mantenimiento\Empresa\EmpresaController@index')->name('mantenimiento.empresas.index');
        Route::get('getBusiness', 'Mantenimiento\Empresa\EmpresaController@getBusiness')->name('getBusiness');
        Route::get('create', 'Mantenimiento\Empresa\EmpresaController@create')->name('mantenimiento.empresas.create');
        Route::post('store', 'Mantenimiento\Empresa\EmpresaController@store')->name('mantenimiento.empresas.store');
        Route::get('destroy/{id}', 'Mantenimiento\Empresa\EmpresaController@destroy')->name('mantenimiento.empresas.destroy');
        Route::get('show/{id}', 'Mantenimiento\Empresa\EmpresaController@show')->name('mantenimiento.empresas.show');
        Route::get('edit/{id}', 'Mantenimiento\Empresa\EmpresaController@edit')->name('mantenimiento.empresas.edit');
        Route::put('update/{id}', 'Mantenimiento\Empresa\EmpresaController@update')->name('mantenimiento.empresas.update');
        Route::get('serie/{id}', 'Mantenimiento\Empresa\EmpresaController@serie')->name('serie.empresa.facturacion');
        Route::post('certificate', 'Mantenimiento\Empresa\EmpresaController@certificate')->name('mantenimiento.empresas.certificado');
        Route::get('obtenerNumeracion/{id}', 'Mantenimiento\Empresa\EmpresaController@obtenerNumeracion')->name('mantenimiento.empresas.obtenerNumeracion');
    });
    //Condiciones
    Route::prefix('condiciones')->group(function () {
        Route::get('index', 'Mantenimiento\CondicionController@index')->name('mantenimiento.condiciones.index');
        Route::get('getRepository', 'Mantenimiento\CondicionController@getRepository')->name('mantenimiento.condiciones.getRepository');
        Route::post('store', 'Mantenimiento\CondicionController@store')->name('mantenimiento.condiciones.store');
        Route::get('destroy/{id}', 'Mantenimiento\CondicionController@destroy')->name('mantenimiento.condiciones.destroy');
        Route::put('update', 'Mantenimiento\CondicionController@update')->name('mantenimiento.condiciones.update');
        Route::post('condicion/exist', 'Mantenimiento\CondicionController@exist')->name('mantenimiento.condiciones.exist');
    });
    // Ubigeo
    Route::prefix('ubigeo')->group(function () {
        Route::post('/provincias', 'Mantenimiento\Ubigeo\UbigeoController@provincias')->name('mantenimiento.ubigeo.provincias');
        Route::post('/distritos', 'Mantenimiento\Ubigeo\UbigeoController@distritos')->name('mantenimiento.ubigeo.distritos');
        Route::post('/api_ruc', 'Mantenimiento\Ubigeo\UbigeoController@api_ruc')->name('mantenimiento.ubigeo.api_ruc');
    });
    // Colaboradores
    Route::prefix('colaboradores')->group(function () {
        Route::get('/index', 'Mantenimiento\Colaborador\ColaboradorController@index')->name('mantenimiento.colaborador.index');
        Route::get('/getColaboradores', 'Mantenimiento\Colaborador\ColaboradorController@getColaboradores')->name('mantenimiento.colaborador.getColaboradores');
        Route::get('/registrar', 'Mantenimiento\Colaborador\ColaboradorController@create')->name('mantenimiento.colaborador.create');
        Route::post('/registrar', 'Mantenimiento\Colaborador\ColaboradorController@store')->name('mantenimiento.colaborador.store');
        Route::get('/edit/{id}', 'Mantenimiento\Colaborador\ColaboradorController@edit')->name('mantenimiento.colaborador.edit');
        Route::put('/update/{id}', 'Mantenimiento\Colaborador\ColaboradorController@update')->name('mantenimiento.colaborador.update');
        Route::get('/datos/{id}', 'Mantenimiento\Colaborador\ColaboradorController@show')->name('mantenimiento.colaborador.show');
        Route::delete('/destroy/{id}', 'Mantenimiento\Colaborador\ColaboradorController@destroy')->name('mantenimiento.colaborador.destroy');
        Route::post('/getDNI', 'Mantenimiento\Colaborador\ColaboradorController@getDNI')->name('mantenimiento.colaborador.getDni');
        Route::get('/consultarDni/{dni}', 'Mantenimiento\Colaborador\ColaboradorController@consultarDni')->name('mantenimiento.colaborador.consultarDni');
    });
    // Vendedores
    Route::prefix('vendedores')->group(function () {
        Route::get('/', 'Mantenimiento\Vendedor\VendedorController@index')->name('mantenimiento.vendedor.index');
        Route::get('/getTable', 'Mantenimiento\Vendedor\VendedorController@getTable')->name('mantenimiento.vendedor.getTable');
        Route::get('/registrar', 'Mantenimiento\Vendedor\VendedorController@create')->name('mantenimiento.vendedor.create');
        Route::post('/registrar', 'Mantenimiento\Vendedor\VendedorController@store')->name('mantenimiento.vendedor.store');
        Route::get('/actualizar/{id}', 'Mantenimiento\Vendedor\VendedorController@edit')->name('mantenimiento.vendedor.edit');
        Route::put('/actualizar/{id}', 'Mantenimiento\Vendedor\VendedorController@update')->name('mantenimiento.vendedor.update');
        Route::get('/datos/{id}', 'Mantenimiento\Vendedor\VendedorController@show')->name('mantenimiento.vendedor.show');
        Route::get('/destroy/{id}', 'Mantenimiento\Vendedor\VendedorController@destroy')->name('mantenimiento.vendedor.destroy');
        Route::post('/getDNI', 'Mantenimiento\Vendedor\VendedorController@getDNI')->name('mantenimiento.vendedor.getDni');
    });

    //===== METODOS ENTREGA =======
    Route::prefix('metodos_entrega')->group(function () {
        Route::get('/', 'Mantenimiento\MetodoEntrega\MetodoEntregaController@index')->name('mantenimiento.metodo_entrega.index');
        Route::get('/getTable', 'Mantenimiento\MetodoEntrega\MetodoEntregaController@getTable')->name('mantenimiento.metodo_entrega.getTable');
        Route::get('/registrar', 'Mantenimiento\MetodoEntrega\MetodoEntregaController@create')->name('mantenimiento.metodo_entrega.create');
        Route::post('/registrar', 'Mantenimiento\MetodoEntrega\MetodoEntregaController@store')->name('mantenimiento.metodo_entrega.store');
        Route::get('/get-metodo-entrega/{id}', 'Mantenimiento\MetodoEntrega\MetodoEntregaController@getMetodoEntrega')->name('mantenimiento.metodo_entrega.getMetodoEntrega');
        Route::post('/update', 'Mantenimiento\MetodoEntrega\MetodoEntregaController@update')->name('mantenimiento.metodo_entrega.update');
        Route::get('/destroy/{id}', 'Mantenimiento\MetodoEntrega\MetodoEntregaController@destroy')->name('mantenimiento.metodo_entrega.destroy');
        Route::get('/get-sedes/{agencia_id}', 'Mantenimiento\MetodoEntrega\MetodoEntregaController@getSedes')->name('mantenimiento.metodo_entrega.getSedes');
        Route::post('/create-sede', 'Mantenimiento\MetodoEntrega\MetodoEntregaController@createSede')->name('mantenimiento.metodo_entrega.createSede');
        Route::post('/update-sede', 'Mantenimiento\MetodoEntrega\MetodoEntregaController@updateSede')->name('mantenimiento.metodo_entrega.updateSede');
        Route::post('/delete-sede', 'Mantenimiento\MetodoEntrega\MetodoEntregaController@deleteSede')->name('mantenimiento.metodo_entrega.deleteSede');
    });
});
