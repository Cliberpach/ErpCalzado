<?php

namespace App\Http\Services\Mantenimiento\Empresa;


class EmpresaManager
{
    private EmpresaService $s_service;

    public function __construct()
    {
        $this->s_service = new EmpresaService();
    }

    public function update(array $data, int $id)
    {
        return $this->s_service->update($data, $id);
    }
}
