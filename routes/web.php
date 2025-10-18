<?php

use App\Events\NotifySunatEvent;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (Auth()->user()) {
        return view('home');
    } else {
        return view('auth.login');
    }
});

Auth::routes();

Route::group(
    [
        'middleware' => 'auth',
        // 'middleware' => 'Cors'
    ],
    function () {
        Route::get('/home', 'HomeController@index')->name('home');
        Route::get('/home/dashboard', 'HomeController@dashboard')->name('home.dashboard');

        Route::get('logout', 'Auth\LoginController@logout')->name('logout');

        //Parametro
        Route::get('parametro/getApiruc/{ruc}', 'ParametroController@apiRuc')->name('getApiruc');
        Route::get('parametro/getApidni/{dni}', 'ParametroController@apiDni')->name('getApidni');
        Route::get('parametro/notifications', 'ParametroController@notifications')->name('getNotifications');

        //======== RESTAURAR STOCK ======
        Route::post('restaurar-stock/', 'ParametroController@restaurarStock')->name('restaurarStock');
        Route::get('descargar-bd/', 'ParametroController@descargarBD')->name('descargarBD');

        // Mantenimiento


        //Configuracion
        Route::prefix('configuracion')->group(function () {
            Route::get('index', 'Configuracion\ConfiguracionController@index')->name('configuracion.index');
            Route::put('update/{id}', 'Configuracion\ConfiguracionController@update')->name('configuracion.update');
            Route::put('/empresa/update', 'Configuracion\ConfiguracionController@codigo')->name('configuracion.empresa.update');
            Route::post('/changePassword', 'Configuracion\ConfiguracionController@changePasswordMaster')->name('changePasswordMaster');
            Route::post('/resumenes/envio', 'Configuracion\ConfiguracionController@resumenesEnvio')->name('configuracion.resumenes.envio');
            Route::post('/greenter/modo', 'Configuracion\ConfiguracionController@setGreenterModo')->name('configuracion.greenter.modo');
            Route::post('/cuentasBancarias/modo', 'Configuracion\ConfiguracionController@cuentasBancariasModo')->name('configuracion.cuentasBancarias.modo');
        });

        //Users
        Route::prefix('users')->group(function () {
            Route::get('/', 'Seguridad\UserController@index')->name('user.index');
            Route::get('destroy/{id}', 'Seguridad\UserController@destroy')->name('user.destroy');
            Route::get('create', 'Seguridad\UserController@create')->name('user.create');
            Route::post('store', 'Seguridad\UserController@store')->name('user.store');
            Route::get('edit/{id}', 'Seguridad\UserController@edit')->name('user.edit');
            Route::put('update/{id}', 'Seguridad\UserController@update')->name('user.update');
            Route::get('show/{id}', 'Seguridad\UserController@show')->name('user.show');
        });

        //Roles
        Route::prefix('roles')->group(function () {
            Route::get('/', 'Seguridad\RoleController@index')->name('role.index');
            Route::get('destroy/{id}', 'Seguridad\RoleController@destroy')->name('role.destroy');
            Route::get('create', 'Seguridad\RoleController@create')->name('role.create');
            Route::post('store', 'Seguridad\RoleController@store')->name('role.store');
            Route::get('edit/{id}', 'Seguridad\RoleController@edit')->name('role.edit');
            Route::put('update/{id}', 'Seguridad\RoleController@update')->name('role.update');
            Route::get('show/{id}', 'Seguridad\RoleController@show')->name('role.show');
            Route::get('getTable', 'Seguridad\RoleController@getTable')->name('role.getTable');
        });

        //Compras
        //Proveedores
        Route::prefix('compras/proveedores')->group(function () {
            Route::get('index', 'Compras\ProveedorController@index')->name('compras.proveedor.index');
            Route::get('getProvider', 'Compras\ProveedorController@getProvider')->name('getProvider');
            Route::get('create', 'Compras\ProveedorController@create')->name('compras.proveedor.create');
            Route::post('store', 'Compras\ProveedorController@store')->name('compras.proveedor.store');
            Route::get('edit/{id}', 'Compras\ProveedorController@edit')->name('compras.proveedor.edit');
            Route::get('show/{id}', 'Compras\ProveedorController@show')->name('compras.proveedor.show');
            Route::put('update/{id}', 'Compras\ProveedorController@update')->name('compras.proveedor.update');
            Route::get('destroy/{id}', 'Compras\ProveedorController@destroy')->name('compras.proveedor.destroy');
        });
        //Ordenes de Compra
        Route::prefix('compras/ordenes')->group(function () {
            Route::get('index', 'Compras\OrdenController@index')->name('compras.orden.index');
            Route::get('getOrder', 'Compras\OrdenController@getOrder')->name('getOrder');
            Route::get('create', 'Compras\OrdenController@create')->name('compras.orden.create');
            Route::post('store', 'Compras\OrdenController@store')->name('compras.orden.store');
            Route::get('edit/{id}', 'Compras\OrdenController@edit')->name('compras.orden.edit');
            Route::get('show/{id}', 'Compras\OrdenController@show')->name('compras.orden.show');
            Route::put('update/{id}', 'Compras\OrdenController@update')->name('compras.orden.update');
            Route::get('destroy/{id}', 'Compras\OrdenController@destroy')->name('compras.orden.destroy');
            Route::get('reporte/{id}', 'Compras\OrdenController@report')->name('compras.orden.reporte');
            Route::get('email/{id}', 'Compras\OrdenController@email')->name('compras.orden.email');
            Route::get('concretada/{id}', 'Compras\OrdenController@concretized')->name('compras.orden.concretada');
            Route::get('consultaEnvios/{id}', 'Compras\OrdenController@send')->name('compras.orden.envios');
            Route::get('documento/{id}', 'Compras\OrdenController@document')->name('compras.orden.documento');
            Route::get('nuevodocumento/{id}', 'Compras\OrdenController@newdocument')->name('compras.orden.nuevodocumento');
            Route::get('confirmarEliminar/{id}', 'Compras\OrdenController@confirmDestroy')->name('compras.orden.confirmDestroy');
            Route::get('dolar', 'Compras\OrdenController@dolar')->name('compras.orden.dolar');

            Route::get('getProductosByModelo/{modelo_id}', 'Compras\OrdenController@getProductosByModelo')->name('compras.orden.getProductosByModelo');
        });
        //Documentos
        Route::prefix('compras/documentos')->group(function () {
            Route::get('/index', 'Compras\DocumentoController@index')->name('compras.documento.index');
            Route::get('/getDocument', 'Compras\DocumentoController@getDocument')->name('getDocument');
            Route::get('/create', 'Compras\DocumentoController@create')->name('compras.documento.create');
            Route::post('/store', 'Compras\DocumentoController@store')->name('compras.documento.store');
            Route::post('/consulta-store', 'Compras\DocumentoController@comprobante_store')->name('compras.documento.consulta_store');
            Route::post('/consulta-update', 'Compras\DocumentoController@comprobante_update')->name('compras.documento.consulta_update');
            Route::get('/edit/{id}', 'Compras\DocumentoController@edit')->name('compras.documento.edit');
            Route::put('/update/{id}', 'Compras\DocumentoController@update')->name('compras.documento.update');
            Route::get('/destroy/{id}', 'Compras\DocumentoController@destroy')->name('compras.documento.destroy');
            Route::get('/show/{id}', 'Compras\DocumentoController@show')->name('compras.documento.show');
            Route::get('/getProduct', 'Compras\DocumentoController@getProduct')->name('compras.documento.getProduct');
            Route::get('/reporte/{id}', 'Compras\DocumentoController@report')->name('compras.documento.reporte');

            Route::get('/tipoPago/{id}', 'Compras\DocumentoController@TypePay')->name('compras.documento.tipo_pago.existente');
        });

        //NOTAS DE CREDITO COMPRAS
        Route::prefix('compras/notas')->group(function () {
            Route::get('index/{id}', 'Compras\NotaController@index')->name('compras.notas');
            Route::get('create', 'Compras\NotaController@create')->name('compras.notas.create');
            Route::post('store', 'Compras\NotaController@store')->name('compras.notas.store');
            Route::get('getNotes/{id}', 'Compras\NotaController@getNotes')->name('compras.getNotes');
            Route::get('getDetalles/{id}', 'Compras\NotaController@getDetalles')->name('compras.getDetalles');
            Route::get('show/{id}', 'Compras\NotaController@show')->name('compras.notas.show');
            Route::get('show_dev/{id}', 'Compras\NotaController@show_dev')->name('compras.notas_dev.show');
        });

        //NOTAS DE CREDITO / DEBITO
        Route::prefix('notas/electronicos')->group(function () {
            Route::get('index/{id}', 'Ventas\Electronico\NotaController@index')->name('ventas.notas');
            Route::get('index_dev/{id}', 'Ventas\Electronico\NotaController@index_dev')->name('ventas.notas_dev');
            Route::get('create', 'Ventas\Electronico\NotaController@create')->name('ventas.notas.create');
            Route::post('store', 'Ventas\Electronico\NotaController@store')->name('ventas.notas.store');
            Route::get('getNotes/{id}', 'Ventas\Electronico\NotaController@getNotes')->name('ventas.getNotes');
            Route::get('getDetalles/{id}', 'Ventas\Electronico\NotaController@getDetalles')->name('ventas.getDetalles');
            Route::get('show/{id}', 'Ventas\Electronico\NotaController@show')->name('ventas.notas.show');
            Route::get('show_dev/{id}', 'Ventas\Electronico\NotaController@show_dev')->name('ventas.notas_dev.show');
            Route::get('sunat/{id}/{type_response?}', 'Ventas\Electronico\NotaController@sunat')->name('ventas.notas.sunat');
        });

        Route::prefix('modeloExcel')->group(function () {
            Route::get('cliente', 'ModeloExcelController@cliente')->name('ModeloExcel.cliente');
            Route::get('categoria', 'ModeloExcelController@categoria')->name('ModeloExcel.categoria');
            Route::get('modelo', 'ModeloExcelController@modelo')->name('ModeloExcel.modelo');
            Route::get('marca', 'ModeloExcelController@marca')->name('ModeloExcel.marca');
            Route::get('producto', 'ModeloExcelController@producto')->name('ModeloExcel.producto');
            Route::get('proveedor', 'ModeloExcelController@proveedor')->name('ModeloExcel.proveedor');
        });

        Route::prefix('importExcel')->group(function () {
            Route::post('cliente', 'ImportExcelController@uploadcliente')->name('ImportExcel.uploadcliente');
            Route::post('categoria', 'ImportExcelController@uploadcategoria')->name('ImportExcel.uploadcategoria');
            Route::post('modelo', 'ImportExcelController@uploadmodelo')->name('ImportExcel.uploadmodelo');
            Route::post('marca', 'ImportExcelController@uploadmarca')->name('ImportExcel.uploadmarca');
            Route::post('producto', 'ImportExcelController@uploadproducto')->name('ImportExcel.uploadproducto');
            Route::post('proveedor', 'ImportExcelController@uploadproveedor')->name('ImportExcel.uploadproveedor');
        });

        // Consultas - Documentos
        Route::prefix('consultas/documentos')->group(function () {

            Route::get('index', 'Consultas\DocumentoController@index')->name('consultas.documento.index');
            Route::post('getTable', 'Consultas\DocumentoController@getTable')->name('consultas.documento.getTable');
            Route::get('getDownload', 'Consultas\DocumentoController@getDownload')->name('consultas.documento.getDownload');
            Route::get('convertir/{id}', 'Consultas\DocumentoController@convertir')->name('consultas.documento.convertir');
        });

        // Consultas - Ventas - Documentos
        Route::prefix('consultas/ventas/documentos')->group(function () {

            Route::get('index', 'Consultas\Ventas\DocumentoController@index')->name('consultas.ventas.documento.index');
            Route::post('getTable', 'Consultas\Ventas\DocumentoController@getTable')->name('consultas.ventas.documento.getTable');
        });

        // Consultas - Ventas - Documentos - NO
        Route::prefix('consultas/ventas/documentos-no')->group(function () {

            Route::get('index', 'Consultas\Ventas\NoEnviadosController@index')->name('consultas.ventas.documento.no.index');
            Route::post('getTable', 'Consultas\Ventas\NoEnviadosController@getTable')->name('consultas.ventas.documento.no.getTable');
            Route::get('edit/{id}', 'Consultas\Ventas\NoEnviadosController@edit')->name('consultas.ventas.documento.no.edit');
            Route::post('update/{id}', 'Consultas\Ventas\NoEnviadosController@update')->name('consultas.ventas.documento.no.update');

            Route::get('getLot/{id}', 'Consultas\Ventas\NoEnviadosController@getLot')->name('consultas.ventas.documento.no.getLot');
            Route::get('getLotRecientes/{id}', 'Consultas\Ventas\NoEnviadosController@getLotRecientes')->name('consultas.ventas.documento.no.getLotRecientes');
            Route::post('cantidad/', 'Consultas\Ventas\NoEnviadosController@quantity')->name('consultas.ventas.documento.no.cantidad');
            Route::post('devolver/cantidad', 'Consultas\Ventas\NoEnviadosController@returnQuantity')->name('consultas.ventas.documento.no.devolver.cantidades');
            // Route::post('devolver/cantidadedit', 'Consultas\Ventas\NoEnviadosController@returnQuantityEdit')->name('consultas.ventas.documento.no.devolver.cantidadesedit');
            // Route::post('updateQuantityEdit', 'Consultas\Ventas\NoEnviadosController@updateQuantityEdit')->name('consultas.ventas.documento.no.updateQuantityEdit');
            // Route::post('devolver/lotesinicio', 'Consultas\Ventas\NoEnviadosController@returnQuantityLoteInicio')->name('consultas.ventas.documento.no.devolver.lotesinicio');
            Route::post('obtener/lote', 'Consultas\Ventas\NoEnviadosController@returnLote')->name('consultas.ventas.documento.no.obtener.lote');
            Route::post('update/lote/edit', 'Consultas\Ventas\NoEnviadosController@updateLote')->name('consultas.ventas.documento.no.update.lote');
        });

        // Consultas - Ventas - Cotizaciones
        Route::prefix('consultas/ventas/cotizaciones')->group(function () {

            Route::get('index', 'Consultas\Ventas\CotizacionController@index')->name('consultas.ventas.cotizacion.index');
            Route::post('getTable', 'Consultas\Ventas\CotizacionController@getTable')->name('consultas.ventas.cotizacion.getTable');
        });

        // Consultas - Alertas
        Route::prefix('consultas/ventas/alertas')->group(function () {

            Route::get('envio', 'Consultas\Ventas\AlertaController@envio')->name('consultas.ventas.alerta.envio');
            Route::get('getTableEnvio', 'Consultas\Ventas\AlertaController@getTableEnvio')->name('consultas.ventas.alerta.getTableEnvio');
            Route::get('sunat/{id}', 'Consultas\Ventas\AlertaController@sunat')->name('consultas.ventas.alerta.sunat');
            Route::get('anular-venta/{id}', 'Consultas\Ventas\AlertaController@anularVenta')->name('consultas.ventas.alerta.anularVenta');

            Route::get('regularize', 'Consultas\Ventas\AlertaController@regularize')->name('consultas.ventas.alerta.regularize');
            Route::get('getTableRegularize', 'Consultas\Ventas\AlertaController@getTableRegularize')->name('consultas.ventas.alerta.getTableRegularize');
            Route::get('cdr/{id}', 'Consultas\Ventas\AlertaController@cdr')->name('consultas.ventas.alerta.cdr');

            Route::get('notas', 'Consultas\Ventas\AlertaController@notas')->name('consultas.ventas.alerta.notas');
            Route::get('getTableNotas', 'Consultas\Ventas\AlertaController@getTableNotas')->name('consultas.ventas.alerta.getTableNotas');
            Route::post('sunat_notas', 'Consultas\Ventas\AlertaController@sunat_notas')->name('consultas.ventas.alerta.sunat_notas');

            Route::get('guias', 'Consultas\Ventas\AlertaController@guias')->name('consultas.ventas.alerta.guias');
            Route::get('getTableguias', 'Consultas\Ventas\AlertaController@getTableGuias')->name('consultas.ventas.alerta.getTableGuias');
            Route::get('sunat_guias/{id}', 'Consultas\Ventas\AlertaController@sunat_guias')->name('consultas.ventas.alerta.sunat_guias');

            Route::get('retenciones', 'Consultas\Ventas\AlertaController@retenciones')->name('consultas.ventas.alerta.retenciones');
            Route::get('getTableretenciones', 'Consultas\Ventas\AlertaController@getTableRetenciones')->name('consultas.ventas.alerta.getTableRetenciones');
            Route::get('sunat_retenciones/{id}', 'Consultas\Ventas\AlertaController@sunat_retenciones')->name('consultas.ventas.alerta.sunat_retenciones');
        });

        // Consultas - Compras - Ordenes
        Route::prefix('consultas/compras/cotizaciones')->group(function () {

            Route::get('index', 'Consultas\Compras\OrdenController@index')->name('consultas.compras.orden.index');
            Route::post('getTable', 'Consultas\Compras\OrdenController@getTable')->name('consultas.compras.orden.getTable');
        });

        // Consultas - Compras - Documentos
        Route::prefix('consultas/compras/documentos')->group(function () {

            Route::get('index', 'Consultas\Compras\DocumentoController@index')->name('consultas.compras.documento.index');
            Route::post('getTable', 'Consultas\Compras\DocumentoController@getTable')->name('consultas.compras.documento.getTable');
        });

        // Consultas - Cuentas - Proveedores
        Route::prefix('consultas/cuentas/proveedores')->group(function () {

            Route::get('index', 'Consultas\Cuentas\ProveedorController@index')->name('consultas.cuentas.proveedor.index');
            Route::post('getTable', 'Consultas\Cuentas\ProveedorController@getTable')->name('consultas.cuentas.proveedor.getTable');
        });

        // Consultas - Cuentas - Clientes
        Route::prefix('consultas/cuentas/clientes')->group(function () {

            Route::get('index', 'Consultas\Cuentas\ClienteController@index')->name('consultas.cuentas.cliente.index');
            Route::post('getTable', 'Consultas\Cuentas\ClienteController@getTable')->name('consultas.cuentas.cliente.getTable');
        });

        // Consultas - Notas - Salida
        Route::prefix('consultas/notas/salidad')->group(function () {

            Route::get('index', 'Consultas\Notas\SalidadController@index')->name('consultas.notas.salidad.index');
            Route::post('getTable', 'Consultas\Notas\SalidadController@getTable')->name('consultas.notas.salidad.getTable');
        });

        // Consultas - Notas - Ingreso
        Route::prefix('consultas/notas/ingreso')->group(function () {

            Route::get('index', 'Consultas\Notas\IngresoController@index')->name('consultas.notas.ingreso.index');
            Route::post('getTable', 'Consultas\Notas\IngresoController@getTable')->name('consultas.notas.ingreso.getTable');
        });

        // Consultas - Kardex - Salida -Ventas
        Route::prefix('consultas/kardex/salidas')->group(function () {

            Route::get('index-V', 'Consultas\Kardex\SalidaController@ventas')->name('consultas.kardex.ventas.index');
            Route::post('getTableVentas', 'Consultas\Kardex\SalidaController@getTableVentas')->name('consultas.kardex.ventas.getTable');

            Route::get('index-N', 'Consultas\Kardex\SalidaController@notas')->name('consultas.kardex.notas.index');
            Route::post('getTableNotas', 'Consultas\Kardex\SalidaController@getTableNotas')->name('consultas.kardex.notas.getTable');
        });

        // Consultas - Caja - Utilidad
        Route::prefix('consultas/caja/utilidad')->group(function () {

            Route::get('index', 'Consultas\Caja\UtilidadController@index')->name('consultas.caja.utilidad.index');
            Route::post('getTable', 'Consultas\Caja\UtilidadController@getTable')->name('consultas.caja.utilidad.getTable');
        });

        // Cosultas - Caja - Egreso
        Route::prefix('consultas/pos/egreso')->group(function () {

            Route::get('index', 'Consultas\Pos\EgresoController@index')->name('consultas.pos.egreso.index');
            Route::post('getTable', 'Consultas\Pos\EgresoController@getTable')->name('consultas.pos.egreso.getTable');
        });

        // Consultas - Utilidad
        Route::prefix('consultas/utilidad')->group(function () {

            Route::get('index', 'Consultas\UtilidadController@index')->name('consultas.utilidad.index');
            Route::get('getDatos/{mes}/{anio}', 'Consultas\UtilidadController@getDatos')->name('consultas.utilidad.getDatos');
        });


        // Consultas - CONTABILIDAD
        Route::prefix('consultas/contabilidad')->group(function () {

            Route::get('index', 'Consultas\ContabilidadController@index')->name('consultas.contabilidad.index');
            Route::post('getTable', 'Consultas\ContabilidadController@getTable')->name('consultas.contabilidad.getTable');
            Route::get('getDownload', 'Consultas\ContabilidadController@getDownload')->name('consultas.contabilidad.getDownload');

            // Route::get('getDownload','Consultas\DocumentoController@getDownload')->name('consultas.documento.getDownload');
            // Route::get('convertir/{id}','Consultas\DocumentoController@convertir')->name('consultas.documento.convertir');

        });



        // Reportes - Producto - informe
        Route::prefix('reportes/producto')->group(function () {

            Route::get('informe', 'Reportes\ProductoController@informe')->name('reporte.producto.informe');
            Route::get('llenarCompras/{producto_id}/{color_id}/{talla_id}', 'Reportes\ProductoController@llenarCompras')->name('reporte.producto.llenarCompras');
            Route::get('llenarVentas/{almacen_id}/{producto_id}/{color_id}/{talla_id}', 'Reportes\ProductoController@llenarVentas')->name('reporte.producto.llenarVentas');
            Route::get('llenarNotasCredito/{almacen_id}/{producto_id}/{color_id}/{talla_id}', 'Reportes\ProductoController@llenarNotasCredito')->name('reporte.producto.llenarNotasCredito');
            Route::get('llenarSalidas/{almacen_id}/{producto_id}/{color_id}/{talla_id}', 'Reportes\ProductoController@llenarSalidas')->name('reporte.producto.llenarSalidas');
            Route::get('llenarIngresos/{almacen_id}/{producto_id}/{color_id}/{talla_id}', 'Reportes\ProductoController@llenarIngresos')->name('reporte.producto.llenarIngresos');
            Route::get('llenarTrasladoSalida/{almacen_id}/{producto_id}/{color_id}/{talla_id}', 'Reportes\ProductoController@llenarTrasladoSalida')->name('reporte.producto.llenarTrasladoSalida');
            Route::get('llenarTrasladoIngreso/{almacen_id}/{producto_id}/{color_id}/{talla_id}', 'Reportes\ProductoController@llenarTrasladoIngreso')->name('reporte.producto.llenarTrasladoIngreso');

            Route::post('updateIngreso', 'Reportes\ProductoController@updateIngreso')->name('reporte.producto.updateIngreso');
            // Route::get('getTable', 'Reportes\ProductoController@getTable')->name('reporte.producto.getTable');
            Route::get('getProductos', 'Reportes\ProductoController@getProductos')->name('reporte.producto.getProductos');
            Route::get('excelProductos', 'Reportes\ProductoController@excelProductos')->name('reporte.producto.excelProductos');
            Route::post('obtenerBarCode', 'Reportes\ProductoController@obtenerBarCode')->name('reporte.producto.obtenerBarCode');
            Route::get('getAdhesivos/{producto_id}/{color_id}/{talla_id}', 'Reportes\ProductoController@getAdhesivos')->name('reporte.producto.getAdhesivos');
        });

        // Reportes - Producto - stock valorizado
        Route::prefix('reportes/producto/stock-valorizado')->group(function () {

            Route::get('index', 'Reportes\StockValorizadoController@index')->name('reporte.producto.stockvalorizado.index');
            Route::get('getTable', 'Reportes\StockValorizadoController@getTable')->name('reporte.producto.stockvalorizado.getTable');
        });

        // Reportes - Pos - CajaDiaria
        Route::prefix('reportes/pos')->group(function () {

            Route::get('cajadiaria', 'Reportes\Pos\CajaController@index')->name('reporte.pos.cajadiaria');
            Route::post('cajadiaria/getTable', 'Reportes\Pos\CajaController@getTable')->name('reporte.pos.cajadiaria.getTable');
            Route::get('cajadiaria/getExcel', 'Reportes\Pos\CajaController@getExcel')->name('reporte.pos.cajadiaria.getExcel');

            Route::get('egreso', 'Reportes\Pos\EgresoController@index')->name('reporte.pos.egreso');
            Route::post('egreso/getTable', 'Reportes\Pos\EgresoController@getTable')->name('reporte.pos.egreso.getTable');
            Route::get('egreso/getExcel', 'Reportes\Pos\EgresoController@getExcel')->name('reporte.pos.egreso.getExcel');
        });

        // Reportes - Ventas - Documento
        Route::prefix('reportes/ventas')->group(function () {

            Route::get('documento', 'Reportes\Ventas\DocumentoController@index')->name('reporte.ventas.documento');
            Route::post('documento/getTable', 'Reportes\Ventas\DocumentoController@getTable')->name('reporte.ventas.documento.getTable');
            Route::get('documento/getExcel', 'Reportes\Ventas\DocumentoController@getExcel')->name('reporte.ventas.documento.getExcel');
        });

        // Reportes - Compras - Documento
        Route::prefix('reportes/compras')->group(function () {

            Route::get('documento', 'Reportes\Compras\DocumentoController@index')->name('reporte.compras.documento');
            Route::post('documento/getTable', 'Reportes\Compras\DocumentoController@getTable')->name('reporte.compras.documento.getTable');
            Route::get('documento/getExcel', 'Reportes\Compras\DocumentoController@getExcel')->name('reporte.compras.documento.getExcel');
        });

        // Reportes - Cuentas - Proveedor
        Route::prefix('reportes/cuentas')->group(function () {

            Route::get('proveedor', 'Reportes\Cuentas\ProveedorController@index')->name('reporte.cuentas.proveedor');
            Route::post('proveedor/getTable', 'Reportes\Cuentas\ProveedorController@getTable')->name('reporte.cuentas.proveedor.getTable');
            Route::get('proveedor/getExcel', 'Reportes\Cuentas\ProveedorController@getExcel')->name('reporte.cuentas.proveedor.getExcel');
        });

        // Reportes - Cuentas - Cliente
        Route::prefix('reportes/cuentas')->group(function () {

            Route::get('cliente', 'Reportes\Cuentas\ClienteController@index')->name('reporte.cuentas.cliente');
            Route::post('cliente/getTable', 'Reportes\Cuentas\ClienteController@getTable')->name('reporte.cuentas.cliente.getTable');
            Route::get('cliente/getExcel', 'Reportes\Cuentas\ClienteController@getExcel')->name('reporte.cuentas.cliente.getExcel');
        });

        // Reportes - Notas - Ingreso
        Route::prefix('reportes/notas')->group(function () {

            Route::get('ingreso', 'Reportes\Notas\IngresoController@index')->name('reporte.notas.ingreso');
            Route::post('ingreso/getTable', 'Reportes\Notas\IngresoController@getTable')->name('reporte.notas.ingreso.getTable');
            Route::get('ingreso/getExcel', 'Reportes\Notas\IngresoController@getExcel')->name('reporte.notas.ingreso.getExcel');
        });

        // Reportes - Notas - Salida
        Route::prefix('reportes/notas')->group(function () {

            Route::get('salida', 'Reportes\Notas\SalidaController@index')->name('reporte.notas.salida');
            Route::post('salida/getTable', 'Reportes\Notas\SalidaController@getTable')->name('reporte.notas.salida.getTable');
            Route::get('salida/getExcel', 'Reportes\Notas\SalidaController@getExcel')->name('reporte.notas.salida.getExcel');
        });
        Route::prefix("consultas/ajax")->group(function () {
            Route::get("tipo-documentos", "ConsultasAjaxController@getTipoDocumentos")->name("consulta.ajax.getTipoDocumentos");
            Route::get("tipo-clientes", "ConsultasAjaxController@tipoClientes")->name("consulta.ajax.tipoClientes");
            Route::get("departamentos", "ConsultasAjaxController@getDepartamentos")->name("consulta.ajax.getDepartamentos");
            Route::get("codigo-precio-menor", "ConsultasAjaxController@getCodigoPrecioMenor")->name("consulta.ajax.getCodigoPrecioMenor");
            Route::get("tipo-envios", "ConsultasAjaxController@getTipoEnvios")->name("consulta.ajax.getTipoEnvios");
            Route::get("get-empresas-envio/{tipo_envio}", "ConsultasAjaxController@getEmpresasEnvio")->name("consulta.ajax.getEmpresasEnvio");
            Route::get("get-sedes-envio/{empresa_envio_id}/{ubigeo}", "ConsultasAjaxController@getSedesEnvio")->name("consulta.ajax.getSedesEnvio");
            Route::get("get-origenes-ventas", "ConsultasAjaxController@getOrigenesVentas")->name("consulta.ajax.getOrigenesVentas");
            Route::get("get-tipos-pago-envio", "ConsultasAjaxController@getTiposPagoEnvio")->name("consulta.ajax.getTiposPagoEnvio");
        });
    }
);

require __DIR__ . '/almacenes/web.php';
require __DIR__ . '/ventas/web.php';
require __DIR__ . '/mantenimiento/web.php';
require __DIR__ . '/kardex/web.php';
require __DIR__ . '/cajas/web.php';
require __DIR__ . '/cuentas/web.php';
require __DIR__ . '/pedidos/web.php';
require __DIR__ . '/sunat/web.php';


Route::get('ventas/documentos/comprobante/{id}/{size}', 'Ventas\DocumentoController@voucher')->name('ventas.documento.comprobante');
Route::get('ventas/documentos/xml/{id}', 'Ventas\DocumentoController@xml')->name('ventas.documento.xml');
Route::get('/buscar', 'BuscarController@index');
Route::post('/getDocument', 'BuscarController@getDocumento')->name('buscar.getDocument');

Route::get('ruta', function () {
    $dato = "Message";
    broadcast(new NotifySunatEvent($dato));
    return 'ok';
    actualizarStockProductos();
    return "okkk";
    $comprobante = array(
        'ruc' => '11111111111',
        'tipo' => '',
        'serie' => '',
        'correlativo' => '',
        'fecha_emision' => '',
        'total' => ''
    );
    $comprobante = json_encode($comprobante, false);
    $comprobante = json_decode($comprobante, false);
    $data = consultaCrd($comprobante);
    $data = json_decode($data);
    return $data->message;
    return '<div style="width:100%; height: 100vh;text-align:center;"><h1 style="font-size: 350px;">SISCOM</h1></div';
});

Route::post('/liberar_colaborador', 'Pos\CajaController@retirarColaborades')->name('Caja.liberarColaborades');
Route::get('/get-colaborades/{id}', 'Pos\CajaController@getColaborades')->name('Caja.getColaborades');
Route::get('/get-producto-by-modelo/{modelo_id}', 'Almacenes\ProductoController@getProductosByModelo'); //VENTAS-NOTA SALIDA
Route::get('/get-stocklogico/{almacen_id}/{producto_id}/{color_id}/{talla_id}', 'Almacenes\ProductoController@getStockLogico');

Route::prefix('utilidades')->group(function () {
    Route::get('getProductos', 'UtilidadesController@getProductos')->name('utilidades.getProductos');
    Route::get('getClientes', 'Ventas\ClienteController@getClientes')->name('utilidades.getClientes');
    Route::get('getProductosTodos', 'Almacenes\ProductoController@getProductosTodos')->name('utilidades.getProductosTodos');
    Route::get('getProductosConStock', 'Almacenes\ProductoController@getProductosConStock')->name('utilidades.getProductosConStock');
    Route::get('getColoresTalla/{almacen_id}/{producto_id}', 'Almacenes\ProductoController@getColoresTalla')->name('utilidades.getColoresTalla');
    Route::get('validarCantidad', 'UtilidadesController@validarCantidad')->name('utilidades.validarCantidad');
    Route::get('get-cuentas-metodo/{metodo_pago}', 'UtilidadesController@getCuentasPorMetodoPago')->name('utilidades.getCuentasPorMetodoPago');
});
