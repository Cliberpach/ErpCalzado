<?php

namespace App\Http\Services\Mantenimiento\TipoPago;

class TipoPagoDto
{
    public function dtoStore(array $data): array
    {
        $descripcion = mb_strtoupper($data['descripcion'], 'UTF-8');

        return [
            'descripcion' => $descripcion,
            'simbolo'     => $descripcion,
            'editable'    => 1,
        ];
    }

    public function dtoUpdate(array $data): array
    {
        return [
            'descripcion' => mb_strtoupper($data['descripcion'], 'UTF-8'),
        ];
    }
}
