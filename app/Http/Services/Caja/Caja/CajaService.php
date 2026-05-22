<?php

namespace App\Http\Services\Caja\Caja;

use Exception;

class CajaService
{
    private CajaRepository $repository;

    public function __construct()
    {
        $this->repository = new CajaRepository();
    }

    public function store(string $nombre, int $sedeId): void
    {
        $this->repository->store($nombre, $sedeId);
    }

    public function update(int $id, string $nombre): void
    {
        $this->repository->update($id, $nombre);
    }

    public function destroy(int $id): void
    {
        if ($this->repository->estaAbierta($id)) {
            throw new Exception('No se puede eliminar una caja que está abierta. Ciérrela primero.');
        }
        $this->repository->destroy($id);
    }
}
