<?php

use App\Models\Almacenes\Producto\Producto;
use Illuminate\Database\Seeder;


class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $producto                   =   new Producto();
        $producto->categoria_id     =   1;
        $producto->marca_id         =   1;
        $producto->modelo_id        =   1;
        $producto->nombre           =   'EMBALAJE';
        $producto->codigo           =   'EMBALAJE';
        $producto->descripcion      =   'EMBALAJE';
        $producto->medida           =   'NIU';
        $producto->stock_minimo     =   1;
        $producto->precio_compra    =   1;
        $producto->precio_venta_1   =   1;
        $producto->precio_venta_2   =   1;
        $producto->precio_venta_3   =   1;
        $producto->igv              =   1;
        $producto->facturacion      =   'NO';
        $producto->estado           =   'ANULADO';
        $producto->costo            =   0;
        $producto->tipo             =   'FICTICIO';
        $producto->mostrar_en_web   =   false;
        $producto->save();

        $producto                   =   new Producto();
        $producto->categoria_id     =   1;
        $producto->marca_id         =   1;
        $producto->modelo_id        =   1;
        $producto->nombre           =   'ENVIO';
        $producto->codigo           =   'ENVIO';
        $producto->descripcion      =   'ENVIO';
        $producto->medida           =   'NIU';
        $producto->stock_minimo     =   1;
        $producto->precio_compra    =   1;
        $producto->precio_venta_1   =   1;
        $producto->precio_venta_2   =   1;
        $producto->precio_venta_3   =   1;
        $producto->igv              =   1;
        $producto->facturacion      =   'NO';
        $producto->estado           =   'ANULADO';
        $producto->costo            =   0;
        $producto->tipo             =   'FICTICIO';
        $producto->mostrar_en_web   =   false;
        $producto->save();
    }
}
