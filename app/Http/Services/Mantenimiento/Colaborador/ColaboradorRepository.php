<?php

namespace App\Http\Services\Mantenimiento\Colaborador;

use App\Mantenimiento\Colaborador\Colaborador;

class ColaboradorRepository
{
    public function store(array $dto): Colaborador
    {
        return Colaborador::create($dto);
    }

    public function update(Colaborador $colaborador, array $dto): Colaborador
    {
        $colaborador->update($dto);

        return $colaborador;
    }

    public function destroy(Colaborador $colaborador): Colaborador
    {
        $colaborador->estado = 'ANULADO';
        $colaborador->update();

        return $colaborador;
    }
}
