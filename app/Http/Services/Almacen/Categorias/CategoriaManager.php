<?php

namespace App\Http\Services\Almacen\Categorias;

use App\Almacenes\Producto;
use App\Models\Almacenes\Categoria\Categoria;
use Illuminate\Support\Collection;

class CategoriaManager
{

    private CategoriaService $s_service;

    public function __construct()
    {
        $this->s_service   =   new CategoriaService();
    }

    public function store(array $data): Categoria
    {
        return $this->s_service->store($data);
    }

    public function update(array $data, int $id): Categoria
    {
        return $this->s_service->update($data, $id);
    }
}
