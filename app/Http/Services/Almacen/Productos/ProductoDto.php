<?php

namespace App\Http\Services\Almacen\Productos;

use Illuminate\Support\Collection;

class ProductoDto
{
    public function dtoStore(array $data): array
    {
        return [

            'nombre' => mb_strtoupper(
                trim($data['nombre']),
                'UTF-8'
            ),

            'marca_id' => $data['marca'],

            'categoria_id' => $data['categoria'],

            'modelo_id' => $data['modelo'],

            'medida' => 105,

            'precio_venta_1' => $data['precio1'],

            'precio_venta_2' => $data['precio2'],

            'precio_venta_3' => $data['precio3'],

            'precio_venta_4' => $data['precio4'],

            'costo' => $data['costo'] ?? 0,

            'descripcion' => $data['descripcion'] ?? null,

            'mostrar_en_web' => $data['mostrar_web'] ?? 0,

            'is_featured' => $data['is_featured'] ?? 0,

            'is_sale' => $data['is_sale'] ?? 0,

            'is_outlet' => $data['is_outlet'] ?? 0,

        ];
    }

    public function dtoProductFeatures(array $features, int $id): array
    {
        $data = [];

        foreach ($features as $feature) {

            $data[] = [

                'product_id' => $id,

                'title' => mb_strtoupper(
                    trim($feature->title),
                    'UTF-8'
                ),

                'icon' => $feature->icon ?? null,

                'description' => isset($feature->description)
                    ? trim($feature->description)
                    : null,

                'sort_order' => $feature->sort_order ?? 0,

                'status' => 1,
                'created_at'    =>  now(),
                'updated_at'    =>  now()
            ];
        }

        return $data;
    }
}
