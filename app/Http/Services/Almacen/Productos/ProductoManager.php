<?php

namespace App\Http\Services\Almacen\Productos;

use App\Almacenes\Producto;
use Illuminate\Support\Collection;

class ProductoManager
{

    private ProductoService $s_producto;

    public function __construct() {
        $this->s_producto   =   new ProductoService();
    }

    public function registrar(array $data):Producto{
        return $this->s_producto->registrar($data);
    }


    public function actualizar(array $data,int $id):Producto{
        return $this->s_producto->actualizar($data,$id);
    }

    public function generarAdhesivos(array $data):Collection{
        return $this->s_producto->generarAdhesivos($data);
    }
}
