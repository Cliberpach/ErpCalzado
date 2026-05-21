<?php

namespace App\Http\Services\Mantenimiento\Cuentas;

use App\Mantenimiento\Cuenta\Cuenta;

class CuentaService
{
    private CuentaDto        $s_dto;
    private CuentaRepository $s_repository;

    public function __construct()
    {
        $this->s_dto        = new CuentaDto();
        $this->s_repository = new CuentaRepository();
    }

    public function store(array $data): Cuenta
    {
        $dto = $this->s_dto->dtoStore($data);
        return $this->s_repository->store($dto);
    }

    public function update(array $data, int $id): Cuenta
    {
        $dto = $this->s_dto->dtoUpdate($data);
        return $this->s_repository->update($dto, $id);
    }

    public function destroy(int $id): void
    {
        $this->s_repository->destroy($id);
    }
}
