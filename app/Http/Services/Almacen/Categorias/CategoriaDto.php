<?php

namespace App\Http\Services\Almacen\Categorias;

class CategoriaDto
{
    public function getDtoStore(array $data): array
    {
        $dto    =   [];

        $dto['descripcion'] = mb_strtoupper(trim($data['descripcion']), 'UTF-8');
        $dto['mostrar_en_web'] = $data['mostrar_en_web'] ?? 0;

        return $dto;
    }
}
