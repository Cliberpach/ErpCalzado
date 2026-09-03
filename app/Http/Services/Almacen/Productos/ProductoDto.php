<?php

namespace App\Http\Services\Almacen\Productos;

use Illuminate\Support\Collection;

class ProductoDto
{
    /**
     * @param bool $esCreacion true sólo desde ProductoService::store().
     *        El valor por defecto (false = edición) es el comportamiento seguro:
     *        ante la duda, el costo existente no se toca.
     */
    public function dtoStore(array $data, bool $esCreacion = false): array
    {
        $dto = [

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



            'descripcion' => $data['descripcion'] ?? null,

            'mostrar_en_web' => $data['mostrar_en_web'] ?? 0,

            'is_featured' => $data['is_featured'] ?? 0,

            'is_sale' => $data['is_sale'] ?? 0,

            'is_outlet' => $data['is_outlet'] ?? 0,

        ];

        /**
         * COSTO — se resuelve aparte, nunca con "?? 0".
         *
         * A los usuarios sin el permiso 'almacen.producto.ver_costo' no se les
         * renderiza el campo, así que la clave NO llega en el POST. Antes esto
         * lo ponía a 0 y destruía el dato al guardar cualquier otro cambio.
         *
         * Reglas:
         *   - update() sin permiso: se omite la clave, Producto::update() no toca
         *     la columna y el costo conserva su valor.
         *   - store() sin permiso: se escribe 1 explícitamente (requisito del
         *     cliente), no el DEFAULT 0.00 de la columna.
         *
         * Se exige además el permiso: aunque alguien inyecte 'costo' en el POST,
         * sin permiso no se escribe.
         */
        $costoRecibido = array_key_exists('costo', $data) && $data['costo'] !== null && $data['costo'] !== '';

        if ($costoRecibido && puedeVerCosto()) {
            // Con permiso y con dato: se escribe lo que envió el formulario.
            $dto['costo'] = $data['costo'];
        } elseif ($esCreacion && !puedeVerCosto()) {
            // ALTA sin permiso: el cliente pidió que quede en 1, no en el
            // DEFAULT 0.00 de la columna. Se escribe explícitamente.
            $dto['costo'] = 1;
        }
        // EDICIÓN sin permiso: la clave se omite a propósito y Producto::update()
        // no toca la columna, así que el costo conserva su valor.

        return $dto;
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
