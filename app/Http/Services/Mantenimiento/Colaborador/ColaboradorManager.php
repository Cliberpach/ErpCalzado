<?php

namespace App\Http\Services\Mantenimiento\Colaborador;

use App\Mantenimiento\Colaborador\Colaborador;

class ColaboradorManager
{
    private ColaboradorService $s_service;

    public function __construct()
    {
        $this->s_service = new ColaboradorService();
    }

    public function store(array $data): Colaborador
    {
        return $this->s_service->store($data);
    }

    public function update(int $id, array $data): Colaborador
    {
        return $this->s_service->update($id, $data);
    }

    public function destroy(int $id): Colaborador
    {
        return $this->s_service->destroy($id);
    }
}
