<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Almacenes\Categoria\CategoriaController;
use App\Http\Controllers\Api\Almacenes\Producto\ProductoController;
use App\Http\Controllers\Api\Almacenes\Color\ColorController;
use App\Http\Controllers\Api\Almacenes\Talla\TallaController;
use App\Http\Controllers\Api\Company\CompanyController;
use App\Http\Controllers\Api\Promotions\PromotionController;

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
    Route::get('get-one/{id}', [ProductoController::class, 'getOne']);
    Route::get('{producto}/colores/{color}/tallas', [ProductoController::class, 'getTallasByColor']);
    Route::get('search-product', [ProductoController::class, 'searchProduct']);
    Route::get('get-by-tag', [ProductoController::class, 'getProductsByTag']);
    Route::get('get-home-products', [ProductoController::class, 'getHomeProducts']);
    Route::get('listing', [ProductoController::class, 'getListing']);
});

Route::prefix('colores')->group(function () {
    Route::get('get-all', [ColorController::class, 'getAll']);
});

Route::prefix('tallas')->group(function () {
    Route::get('/get-all', [TallaController::class, 'getAll']);
});

Route::prefix('company')->group(function () {
    Route::get('/get-company-locations', [CompanyController::class, 'getCompanyLocations']);
});

Route::prefix('promotions')->group(function () {
    Route::get('/get-promotions', [PromotionController::class, 'getPromotions']);
    Route::get('/get-home-promotions', [PromotionController::class, 'getHomePromotions']);
});
