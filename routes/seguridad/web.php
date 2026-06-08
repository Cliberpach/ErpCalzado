<?php

use Illuminate\Support\Facades\Route;

Route::prefix('seguridad')->group(function () {

    //Users
    Route::prefix('users')->group(function () {
        Route::get('/', 'Seguridad\UserController@index')->name('seguridad.user.index');
        Route::get('getUsuarios', 'Seguridad\UserController@getUsuarios')->name('seguridad.user.getUsuarios');
        Route::delete('destroy/{id}', 'Seguridad\UserController@destroy')->name('seguridad.user.destroy');
        Route::get('create', 'Seguridad\UserController@create')->name('seguridad.user.create');
        Route::post('store', 'Seguridad\UserController@store')->name('seguridad.user.store');
        Route::get('edit/{id}', 'Seguridad\UserController@edit')->name('seguridad.user.edit');
        Route::put('update/{id}', 'Seguridad\UserController@update')->name('seguridad.user.update');
        Route::get('show/{id}', 'Seguridad\UserController@show')->name('seguridad.user.show');
    });

    //Roles
    Route::prefix('roles')->group(function () {
        Route::get('/', 'Seguridad\RoleController@index')->name('seguridad.role.index');
        Route::get('destroy/{id}', 'Seguridad\RoleController@destroy')->name('seguridad.role.destroy');
        Route::get('create', 'Seguridad\RoleController@create')->name('seguridad.role.create');
        Route::post('store', 'Seguridad\RoleController@store')->name('seguridad.role.store');
        Route::get('edit/{id}', 'Seguridad\RoleController@edit')->name('seguridad.role.edit');
        Route::put('update/{id}', 'Seguridad\RoleController@update')->name('seguridad.role.update');
        Route::get('show/{id}', 'Seguridad\RoleController@show')->name('seguridad.role.show');
        Route::get('getTable', 'Seguridad\RoleController@getTable')->name('seguridad.role.getTable');
    });
});
