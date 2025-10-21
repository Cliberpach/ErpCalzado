<?php

use App\Http\Controllers\Api\Almacenes\Categoria\CategoriaController;
use App\Http\Controllers\Api\Almacenes\Categoria\ColorController;
use App\Http\Controllers\Api\Almacenes\Producto\ProductoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('categorias')->group(function () {
    Route::get('get-all', [CategoriaController::class, 'getAll']);
});

Route::prefix('productos')->group(function () {
    Route::get('get-all', [ProductoController::class, 'getAll']);
});

Route::prefix('colores')->group(function () {
    Route::get('get-all', [ColorController::class, 'getAll']);
});
