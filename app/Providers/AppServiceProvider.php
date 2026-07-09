<?php

namespace App\Providers;

use App\Almacenes\Producto;
use App\Observers\ProductoObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        // Fase 4.2 (ecommerceMerris): App\Almacenes\Producto es el modelo
        // REAL usado por el CRUD (no el duplicado en App\Models\Almacenes\Producto).
        Producto::observe(ProductoObserver::class);
    }
}
