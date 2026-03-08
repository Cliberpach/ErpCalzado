<?php

namespace App\Http\Services\Almacen\Tallas;

class TallaService
{
    private TallaRepository $s_repository;

    public function __construct()
    {
        $this->s_repository =   new TallaRepository();
    }

    public function getTallas()
    {
        return $this->s_repository->getTallas();
    }
}
