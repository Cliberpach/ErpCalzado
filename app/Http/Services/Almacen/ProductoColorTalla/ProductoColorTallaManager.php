<?php

namespace App\Http\Services\Almacen\ProductoColorTalla;

use App\Http\Services\Almacen\ProductoColorTalla\ProductoColorTallaService;

class ProductoColorTallaManager
{
    private ProductoColorTallaService $s_pct;

    public function __construct() {
        $this->s_pct      =   new ProductoColorTallaService();
    }

    public function getProductoColorTalla(int $almacen_id,int $producto_id,int $color_id, int $talla_id):?\stdClass{
        return $this->s_pct->getProductoColorTalla($almacen_id,$producto_id,$color_id,$talla_id);
    }

    public function decrementarStocks(int $almacen_id,int $producto_id,int $color_id, int $talla_id,float $cantidad){
        $this->s_pct->decrementarStocks($almacen_id,$producto_id,$color_id,$talla_id,$cantidad);
    }

}
