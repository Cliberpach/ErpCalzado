<?php

namespace App\Http\Services\Mantenimiento\Cuentas;

use App\Mantenimiento\Cuenta\Cuenta;

class CuentaRepository
{
    public function store(array $dto): Cuenta
    {
        return Cuenta::create($dto);
    }

    public function update(array $dto, int $id): Cuenta
    {
        $cuenta = Cuenta::findOrFail($id);
        $cuenta->update($dto);
        return $cuenta;
    }

    public function destroy(int $id): void
    {
        $cuenta         = Cuenta::findOrFail($id);
        $cuenta->estado = 'ANULADO';
        $cuenta->save();
    }
}
