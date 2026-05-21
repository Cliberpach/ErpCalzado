<?php

namespace App\Http\Services\Mantenimiento\Cuentas;

use App\Mantenimiento\Cuenta\Cuenta;

class CuentaManager
{
    private CuentaService $s_cuenta;

    public function __construct()
    {
        $this->s_cuenta = new CuentaService();
    }

    public function store(array $data): Cuenta
    {
        return $this->s_cuenta->store($data);
    }

    public function update(array $data, int $id): Cuenta
    {
        return $this->s_cuenta->update($data, $id);
    }

    public function destroy(int $id): void
    {
        $this->s_cuenta->destroy($id);
    }
}
