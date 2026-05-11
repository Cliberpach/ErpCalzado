<?php

namespace App\Http\Services\Almacen\Productos;

class ProductValidation
{
    public function validationStore(array $data):array
    {
        $features   =   json_decode($data['features']);
        $data['features']   =   $features;
        return $data;
    }
}
