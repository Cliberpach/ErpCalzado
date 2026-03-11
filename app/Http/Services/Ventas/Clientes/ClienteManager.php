<?php

namespace App\Http\Services\Ventas\Clientes;

use App\Models\Almacenes\Categoria\Categoria;
use App\Models\Ventas\Cliente\Cliente;

class ClienteManager
{
    private ClienteService $s_service;

    public function __construct()
    {
        $this->s_service   =   new ClienteService();
    }

    public function store(array $data):Cliente
    {
        return $this->s_service->store($data);
    }

    public function update(array $data, int $id): Cliente
    {
        return $this->s_service->update($data, $id);
    }
}
