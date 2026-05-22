<?php

namespace App\Http\Services\Mantenimiento\Empresa;

use App\Mantenimiento\Empresa\Empresa;
use App\Mantenimiento\Ubigeo\Departamento;
use App\Mantenimiento\Ubigeo\Distrito;
use App\Mantenimiento\Ubigeo\Provincia;

class EmpresaRepository
{
    public function update(array $dto, int $id): Empresa
    {
        $company    =   Empresa::findOrFail($id);
        $company->update($dto);
        return $company;
    }
}
