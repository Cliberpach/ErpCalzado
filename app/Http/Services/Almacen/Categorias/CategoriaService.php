<?php

namespace App\Http\Services\Almacen\Categorias;


use App\Models\Almacenes\Categoria\Categoria;
use Illuminate\Support\Facades\Storage;

class CategoriaService
{
    private CategoriaRepository $s_repository;
    private CategoriaDto $s_dto;

    public function __construct()
    {
        $this->s_repository =   new CategoriaRepository();
        $this->s_dto        =   new CategoriaDto();
    }

    public function store(array $data): Categoria
    {
        $dto        =   $this->s_dto->getDtoStore($data);
        $instance   =   $this->s_repository->store($dto);

        if (!empty($data['imagen'])) {
            $this->saveImg($data['imagen'], $instance);
        }
        return $instance;
    }

    public function update(array $data, int $id): Categoria
    {
        $dto        =   $this->s_dto->getDtoStore($data);
        $instance   =   $this->s_repository->update($dto, $id);

        if (!empty($data['imagen'])) {
            $this->deleteimg($instance);
            $this->saveImg($data['imagen'], $instance);
        } else {
            $this->deleteImg($instance);
        }

        return $instance;
    }

    public function deleteImg($instance)
    {
        if (!empty($instance->img_ruta) && Storage::disk('public')->exists($instance->img_ruta)) {
            Storage::disk('public')->delete($instance->img_ruta);
        }
    }
    public function saveImg($img, $instance)
    {
        if (!empty($img)) {
            $file = $img;

            $imgName = $instance->id . '_categoria.' . $file->getClientOriginalExtension();

            $path = $file->storeAs(
                'categorias',
                $imgName,
                'public'
            );

            $instance->update([
                'img_ruta' => $path,
                'img_nombre' => $imgName
            ]);
        }
    }
}
