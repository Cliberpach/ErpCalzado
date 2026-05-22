<?php

namespace App\Http\Services\Caja\Caja;

class CajaManager
{
    private CajaService $service;

    public function __construct()
    {
        $this->service = new CajaService();
    }

    public function store(string $nombre, int $sedeId): void
    {
        $this->service->store($nombre, $sedeId);
    }

    public function update(int $id, string $nombre): void
    {
        $this->service->update($id, $nombre);
    }

    public function destroy(int $id): void
    {
        $this->service->destroy($id);
    }
}
