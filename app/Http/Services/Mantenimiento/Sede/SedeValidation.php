<?php

namespace App\Http\Services\Mantenimiento\Sede;

use App\Mantenimiento\Sedes\Sede;
use Exception;

class SedeValidation
{
    private SedeRepository $s_repository;

    public function __construct(SedeRepository $repository)
    {
        $this->s_repository = $repository;
    }

    public function validateSedeActiva(int $sede_id): Sede
    {
        $sede = $this->s_repository->findSedeActiva($sede_id);

        if (!$sede) {
            throw new Exception("LA SEDE NO EXISTE EN LA BD!!!");
        }

        return $sede;
    }

    public function validateNumeracionUnica(int $comprobante_id, int $sede_id): void
    {
        if ($this->s_repository->existsNumeracion($comprobante_id, $sede_id)) {
            throw new Exception("EL TIPO DE COMPROBANTE YA FUE AGREGADO!!!");
        }
    }

    public function validateTipoComprobante(int $comprobante_id, string $parametro): object
    {
        $tipo = $this->s_repository->findTipoComprobante($comprobante_id, $parametro);

        if (!$tipo) {
            throw new Exception("EL TIPO DE COMPROBANTE NO EXISTE EN LA BD!!!");
        }

        return $tipo;
    }
}
