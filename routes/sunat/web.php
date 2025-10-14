<?php

use App\Events\NotifySunatEvent;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


Route::prefix('sunat')->middleware('auth')->group(function () {

    //COMPROBANTES ELECTRONICOS
    Route::prefix('invoice')->group(function () {
        Route::get('/', 'Ventas\Electronico\ComprobanteController@index')->name('ventas.comprobantes');
        Route::get('getVouchers', 'Ventas\Electronico\ComprobanteController@getVouchers')->name('ventas.getVouchers');
        Route::post('sunat', 'Ventas\Electronico\ComprobanteController@sunat')->name('ventas.documento.sunat');
        Route::get('sunat-contingencia/{id}', 'Ventas\Electronico\ComprobanteController@sunatContingencia')->name('ventas.documento.sunat.contingencia');
        Route::get('contingencia/{id}', 'Ventas\Electronico\ComprobanteController@convertirContingencia')->name('ventas.documento.contingencia');
        Route::get('cdr/{id}', 'Ventas\Electronico\ComprobanteController@cdr')->name('ventas.documento.cdr');
        Route::post('/envio', 'Ventas\Electronico\ComprobanteController@email')->name('ventas.documento.envio');
    });

});
