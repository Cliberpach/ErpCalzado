  <?php

    use App\Events\NotifySunatEvent;
use App\Http\Controllers\Almacenes\ConductorController;
use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\Auth;

    Route::prefix('almacenes')->group(function () {

    Route::prefix('almacen')->group(function () {
        Route::get('index', 'Almacenes\AlmacenController@index')->name('almacenes.almacen.index');
        Route::get('getRepository', 'Almacenes\AlmacenController@getRepository')->name('getRepository');
        Route::get('destroy/{id}', 'Almacenes\AlmacenController@destroy')->name('almacenes.almacen.destroy');
        Route::post('store', 'Almacenes\AlmacenController@store')->name('almacenes.almacen.store');
        Route::put('update', 'Almacenes\AlmacenController@update')->name('almacenes.almacen.update');
        Route::post('almacen/exist', 'Almacenes\AlmacenController@exist')->name('almacenes.almacen.exist');
    });

    //Categoria
    Route::prefix('categorias')->group(function () {
        Route::get('index', 'Almacenes\CategoriaController@index')->name('almacenes.categorias.index');
        Route::get('getCategory', 'Almacenes\CategoriaController@getCategory')->name('getCategory');
        Route::get('destroy/{id}', 'Almacenes\CategoriaController@destroy')->name('almacenes.categorias.destroy');
        Route::post('store', 'Almacenes\CategoriaController@store')->name('almacenes.categorias.store');
        Route::put('update', 'Almacenes\CategoriaController@update')->name('almacenes.categorias.update');
    });
    //Marcas
    Route::prefix('marcas')->group(function () {
        Route::get('index', 'Almacenes\MarcaController@index')->name('almacenes.marcas.index');
        Route::get('getmarca', 'Almacenes\MarcaController@getmarca')->name('getmarca');
        Route::get('destroy/{id}', 'Almacenes\MarcaController@destroy')->name('almacenes.marcas.destroy');
        Route::post('store', 'Almacenes\MarcaController@store')->name('almacenes.marcas.store');
        Route::put('update', 'Almacenes\MarcaController@update')->name('almacenes.marcas.update');
        Route::post('/exist', 'Almacenes\MarcaController@exist')->name('almacenes.marcas.exist');
    });

    //Modelos
    Route::prefix('modelos')->group(function () {
        Route::get('index', 'Almacenes\ModeloController@index')->name('almacenes.modelos.index');
        Route::get('getModelo', 'Almacenes\ModeloController@getModelo')->name('getModelo');
        Route::get('destroy/{id}', 'Almacenes\ModeloController@destroy')->name('almacenes.modelos.destroy');
        Route::post('store', 'Almacenes\ModeloController@store')->name('almacenes.modelos.store');
        Route::put('update', 'Almacenes\ModeloController@update')->name('almacenes.modelos.update');
    });

    //Colores
    Route::prefix('colores')->group(function () {
        Route::get('index', 'Almacenes\ColorController@index')->name('almacenes.colores.index');
        Route::get('getColores', 'Almacenes\ColorController@getColores')->name('almacenes.colores.getColores');
        Route::get('getColor/{id}', 'Almacenes\ColorController@getColor')->name('almacenes.colores.getColor');
        Route::delete('destroy/{id}', 'Almacenes\ColorController@destroy')->name('almacenes.colores.destroy');
        Route::post('store', 'Almacenes\ColorController@store')->name('almacenes.colores.store');
        Route::put('update/{id}', 'Almacenes\ColorController@update')->name('almacenes.colores.update');
    });

    //Tallas
    Route::prefix('tallas')->group(function () {
        Route::get('index', 'Almacenes\TallaController@index')->name('almacenes.tallas.index');
        Route::get('getTalla', 'Almacenes\TallaController@getTalla')->name('getTalla');
        Route::get('destroy/{id}', 'Almacenes\TallaController@destroy')->name('almacenes.tallas.destroy');
        Route::post('store', 'Almacenes\TallaController@store')->name('almacenes.tallas.store');
        Route::put('update', 'Almacenes\TallaController@update')->name('almacenes.tallas.update');
    });

    //Productos
    Route::prefix('productos')->group(function () {

        Route::get('/', 'Almacenes\ProductoController@index')->name('almacenes.producto.index');
        Route::get('/getTable', 'Almacenes\ProductoController@getTable')->name('almacenes.producto.getTable');
        Route::get('/registrar', 'Almacenes\ProductoController@create')->name('almacenes.producto.create');
        Route::post('/store', 'Almacenes\ProductoController@store')->name('almacenes.producto.store');
        Route::get('/actualizar/{id}', 'Almacenes\ProductoController@edit')->name('almacenes.producto.edit');
        Route::put('/update/{id}', 'Almacenes\ProductoController@update')->name('almacenes.producto.update');
        Route::get('/datos/{id}', 'Almacenes\ProductoController@show')->name('almacenes.producto.show');
        Route::delete('/destroy/{id}', 'Almacenes\ProductoController@destroy')->name('almacenes.producto.destroy');
        Route::get('/getColores/{almacen_id}/{producto_id}', 'Almacenes\ProductoController@getColores')->name('almacenes.producto.getColores');
        Route::get('/getTallas/{almacen_id}/{producto_id}/{color_id}', 'Almacenes\ProductoController@getTallas')->name('almacenes.producto.getTallas');

        Route::get('/getExcel', 'Almacenes\ProductoController@getExcel')->name('almacenes.producto.getExcel');

        Route::get('getProductos', 'Almacenes\ProductoController@getProductos')->name('getProductos');
        Route::get('getProducto/{id}', 'Almacenes\ProductoController@getProducto')->name('getProducto');
        Route::get('generarCode', 'Almacenes\ProductoController@generarCode')->name('generarCode');

        Route::get('/obtenerProducto/{id}', 'Almacenes\ProductoController@obtenerProducto')->name('almacenes.producto.obtenerProducto');
    });

    //NotaIngreso
    Route::prefix('nota_ingreso')->group(function () {
        Route::get('index', 'Almacenes\NotaIngresoController@index')->name('almacenes.nota_ingreso.index');
        Route::get('getdata', 'Almacenes\NotaIngresoController@gettable')->name('almacenes.nota_ingreso.data');
        Route::get('create', 'Almacenes\NotaIngresoController@create')->name('almacenes.nota_ingreso.create');
        Route::post('store', 'Almacenes\NotaIngresoController@store')->name('almacenes.nota_ingreso.store');
        Route::post('/storeFast', 'Almacenes\NotaIngresoController@storeFast')->name('almacenes.nota_ingreso.storeFast');
        Route::get('edit/{id}', 'Almacenes\NotaIngresoController@edit')->name('almacenes.nota_ingreso.edit');
        Route::get('show/{id}', 'Almacenes\NotaIngresoController@show')->name('almacenes.nota_ingreso.show');
        Route::put('update/{id}', 'Almacenes\NotaIngresoController@update')->name('almacenes.nota_ingreso.update');
        Route::get('destroy/{id}', 'Almacenes\NotaIngresoController@destroy')->name('almacenes.nota_ingreso.destroy');
        Route::post('productos', 'Almacenes\NotaIngresoController@getProductos')->name('almacenes.nota_ingreso.productos');
        Route::post('uploadnotaingreso', 'Almacenes\NotaIngresoController@uploadnotaingreso')->name('almacenes.nota_ingreso.uploadnotaingreso');
        Route::get('downloadexcel', 'Almacenes\NotaIngresoController@getDownload')->name('almacenes.nota_ingreso.downloadexcel');
        Route::get('downloadproductosexcel', 'Almacenes\NotaIngresoController@getProductosExcel')->name('almacenes.nota_ingreso.downloadproductosexcel');
        Route::get('downloaderrorexcel', 'Almacenes\NotaIngresoController@getErrorExcel')->name('almacenes.nota_ingreso.error_excel');
        Route::get('/getProductos/{modelo_id}/{almacen_id}', 'Almacenes\NotaIngresoController@getProductos')->name('almacenes.nota_ingreso.getProductos');
        Route::get('/generarEtiquetas/{nota_id}', 'Almacenes\NotaIngresoController@generarEtiquetas')->name('almacenes.nota_ingreso.generarEtiquetas');
    });

    //NotaSalida
    Route::prefix('nota_salidad')->group(function () {

        Route::get('index', 'Almacenes\NotaSalidadController@index')->name('almacenes.nota_salidad.index');
        Route::get('getdata', 'Almacenes\NotaSalidadController@gettable')->name('almacenes.nota_salidad.data');
        Route::get('create', 'Almacenes\NotaSalidadController@create')->name('almacenes.nota_salidad.create');
        Route::post('store', 'Almacenes\NotaSalidadController@store')->name('almacenes.nota_salidad.store');
        Route::get('edit/{id}', 'Almacenes\NotaSalidadController@edit')->name('almacenes.nota_salidad.edit');
        Route::get('show/{id}', 'Almacenes\NotaSalidadController@show')->name('almacenes.nota_salidad.show');
        Route::put('update/{id}', 'Almacenes\NotaSalidadController@update')->name('almacenes.nota_salidad.update');
        Route::get('destroy/{id}', 'Almacenes\NotaSalidadController@destroy')->name('almacenes.nota_salidad.destroy');
        Route::get('getPdf/{id}', 'Almacenes\NotaSalidadController@getPdf')->name('almacenes.nota_salidad.getPdf');
        Route::post('productos', 'Almacenes\NotaSalidadController@getProductos')->name('almacenes.nota_salidad.productos');
        Route::get('getLot', 'Almacenes\NotaSalidadController@getLot')->name('almacenes.nota_salidad.getLot');

        Route::get('getStock/{almacen_id}/{producto_id}/{color_id}/{talla_id}', 'Almacenes\NotaSalidadController@getStock')->name('almacenes.nota_salidad.getStock');
        Route::get('/getProductosAlmacen/{modelo_id}/{almacen_id}', 'Almacenes\NotaSalidadController@getProductosAlmacen')->name('almacenes.nota_salidad.getProductosAlmacen');

        Route::post('cantidad/', 'Almacenes\NotaSalidadController@quantity')->name('almacenes.nota_salidad.cantidad');
        Route::post('devolver/cantidad', 'Almacenes\NotaSalidadController@returnQuantity')->name('almacenes.nota_salidad.devolver.cantidades');
        Route::post('devolver/cantidadedit', 'Almacenes\NotaSalidadController@returnQuantityEdit')->name('almacenes.nota_salidad.devolver.cantidadesedit');
        Route::post('devolver/lotesinicio', 'Almacenes\NotaSalidadController@returnQuantityLoteInicio')->name('almacenes.nota_salidad.devolver.lotesinicio');
        Route::post('obtener/lote', 'Almacenes\NotaSalidadController@returnLote')->name('almacenes.nota_salidad.obtener.lote');
        Route::post('update/lote', 'Almacenes\NotaSalidadController@updateLote')->name('almacenes.nota_salidad.update.lote');
    });

    //========== SOLICITUDES TRASLADO =====
    Route::prefix('solicitudes_traslado')->group(function () {

        Route::get('index', 'Almacenes\SolicitudTrasladoController@index')->name('almacenes.solicitud_traslado.index');
        Route::get('getSolicitudesTraslado', 'Almacenes\SolicitudTrasladoController@getSolicitudesTraslado')->name('almacenes.solicitud_traslado.getSolicitudesTraslado');
        Route::get('confirmar/show/{id}', 'Almacenes\SolicitudTrasladoController@confirmarShow')->name('almacenes.solicitud_traslado.confirmarShow');
        Route::post('/confirmar/store', 'Almacenes\SolicitudTrasladoController@confirmarStore')->name('almacenes.solicitud_traslado.confirmarStore');
        Route::get('show/{id}', 'Almacenes\SolicitudTrasladoController@show')->name('almacenes.solicitud_traslado.show');
        Route::get('/generarEtiquetas/{id}', 'Almacenes\SolicitudTrasladoController@generarEtiquetas')->name('almacenes.solicitud_traslado.generarEtiquetas');
    });

    //========== TRASLADOS =====
    Route::prefix('traslados')->group(function () {

        Route::get('index', 'Almacenes\TrasladoController@index')->name('almacenes.traslados.index');
        Route::get('getTraslados', 'Almacenes\TrasladoController@getTraslados')->name('almacenes.traslados.getTraslados');
        Route::get('create', 'Almacenes\TrasladoController@create')->name('almacenes.traslados.create');
        Route::get('/getProductosAlmacen/{modelo_id}/{almacen_id}', 'Almacenes\TrasladoController@getProductosAlmacen')->name('almacenes.traslados.getProductosAlmacen');
        Route::get('getStock/{producto_id}/{color_id}/{talla_id}/{almacen_id}', 'Almacenes\TrasladoController@getStock')->name('almacenes.traslados.getStock');
        Route::post('store', 'Almacenes\TrasladoController@store')->name('almacenes.traslados.store');
        Route::get('generarGuiaCreate/{traslado_id}', 'Almacenes\TrasladoController@generarGuiaCreate')->name('almacenes.traslados.generarGuiaCreate');
        Route::get('show/{id}', 'Almacenes\TrasladoController@show')->name('almacenes.traslados.show');
        Route::post('generarGuiaStore', 'Almacenes\TrasladoController@generarGuiaStore')->name('almacenes.traslados.generarGuiaStore');
        Route::delete('anular-pendiente/{id}', 'Almacenes\TrasladoController@eliminarTrasladoPendiente')->name('almacenes.traslados.anularTrasladoPendiente');
    });

    //========== VEHÍCULOS =====
    Route::prefix('vehiculos')->group(function () {

        Route::get('index', 'Almacenes\VehiculoController@index')->name('almacenes.vehiculos.index');
        Route::get('getVehiculos', 'Almacenes\VehiculoController@getVehiculos')->name('almacenes.vehiculos.getVehiculos');
        Route::get('create', 'Almacenes\VehiculoController@create')->name('almacenes.vehiculos.create');
        Route::post('store', 'Almacenes\VehiculoController@store')->name('almacenes.vehiculos.store');
        Route::get('edit/{id}', 'Almacenes\VehiculoController@edit')->name('almacenes.vehiculos.edit');
        Route::put('update/{id}', 'Almacenes\VehiculoController@update')->name('almacenes.vehiculos.update');
        Route::delete('destroy/{id}', 'Almacenes\VehiculoController@destroy')->name('almacenes.vehiculos.destroy');
    });

    //========== CONDUCTORES =====
    Route::prefix('conductores')->group(function () {

        Route::get('index', 'Almacenes\ConductorController@index')->name('almacenes.conductores.index');
        Route::get('getConductores', 'Almacenes\ConductorController@getConductores')->name('almacenes.conductores.getConductores');
        Route::get('create', 'Almacenes\ConductorController@create')->name('almacenes.conductores.create');
        Route::post('store', 'Almacenes\ConductorController@store')->name('almacenes.conductores.store');
        Route::get('edit/{id}', 'Almacenes\ConductorController@edit')->name('almacenes.conductores.edit');
        Route::put('update/{id}', 'Almacenes\ConductorController@update')->name('almacenes.conductores.update');
        Route::delete('destroy/{id}', 'Almacenes\ConductorController@destroy')->name('almacenes.conductores.destroy');
        Route::get('/consultarDocumento', [ConductorController::class, 'consultarDocumento'])->name('almacenes.conductores.consultarDocumento');
    });

});
