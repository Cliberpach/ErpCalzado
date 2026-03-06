<?php

namespace App\Http\Services\Almacen\Categorias;

class CategoriaDto
{
    public function getDtoStore(array $data): array
    {
        $dto    =   [];

        $dto['descripcion'] = mb_strtoupper(trim($data['nombre']), 'UTF-8');

        return $dto;
    }
}
