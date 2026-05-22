<?php

namespace App\Http\Services\Almacen\Almacen;

use App\Models\Almacenes\Almacen\Almacen;

class AlmacenService
{
    private AlmacenDto $s_dto;
    private AlmacenRepository $s_repository;

    public function __construct()
    {
        $this->s_dto        = new AlmacenDto();
        $this->s_repository = new AlmacenRepository();
    }

    public function store(array $data): Almacen
    {
        $dto = $this->s_dto->getDtoStore($data);
        return $this->s_repository->store($dto);
    }
}
