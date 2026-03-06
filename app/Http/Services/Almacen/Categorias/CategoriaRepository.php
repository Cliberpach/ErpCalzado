<?php

namespace App\Http\Services\Almacen\Categorias;

use App\Models\Almacenes\Categoria\Categoria;

class CategoriaRepository
{

    public function store(array $dto): Categoria
    {
        return Categoria::create($dto);
    }

    public function update(array $dto, int $id): Categoria
    {
        $instance   =   Categoria::findOrFail($id);
        $instance->update($dto);
        return $instance;
    }
}
