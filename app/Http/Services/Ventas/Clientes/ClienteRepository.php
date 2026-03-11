<?php

namespace App\Http\Services\Ventas\Clientes;

use App\Models\Ventas\Cliente\Cliente;

class ClienteRepository
{

    public function store(array $dto): Cliente
    {
        return Cliente::create($dto);
    }

    public function update(array $dto, int $id): Cliente
    {
        $instance   =   Cliente::findOrFail($id);
        $instance->update($dto);
        return $instance;
    }
}
