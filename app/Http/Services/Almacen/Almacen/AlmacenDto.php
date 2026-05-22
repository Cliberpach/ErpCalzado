<?php

namespace App\Http\Services\Almacen\Almacen;

class AlmacenDto
{
    public function getDtoStore(array $data): array
    {
        return [
            'sede_id'      => $data['sede_id'],
            'descripcion'  => mb_strtoupper($data['descripcion'] ?? '', 'UTF-8'),
            'ubicacion'    => mb_strtoupper($data['ubicacion'] ?? '', 'UTF-8'),
            'tipo_almacen' => $data['tipo_almacen'] ?? 'SECUNDARIO',
            'tipo'         => $data['tipo'] ?? 'ALMACEN',
            'estado'       => 'ACTIVO',
        ];
    }
}
