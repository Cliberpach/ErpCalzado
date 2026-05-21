<?php

namespace App\Http\Services\Mantenimiento\TipoPago;

use App\Mantenimiento\TipoPago\TipoPago;

class TipoPagoService
{
    private TipoPagoDto        $s_dto;
    private TipoPagoRepository $s_repository;

    public function __construct()
    {
        $this->s_dto        = new TipoPagoDto();
        $this->s_repository = new TipoPagoRepository();
    }

    public function store(array $data): TipoPago
    {
        $dto = $this->s_dto->dtoStore($data);
        return $this->s_repository->store($dto);
    }

    public function update(array $data, int $id): TipoPago
    {
        $dto = $this->s_dto->dtoUpdate($data);
        return $this->s_repository->update($dto, $id);
    }

    public function destroy(int $id): void
    {
        $this->s_repository->destroy($id);
    }

    public function asignarCuentasStore(int $tipo_pago_id, array $cuenta_ids): void
    {
        $this->s_repository->asignarCuentasStore($tipo_pago_id, $cuenta_ids);
    }
}
