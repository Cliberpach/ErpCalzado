<?php

namespace App\Http\Services\Mantenimiento\Colaborador;

use Illuminate\Support\Str;

class ColaboradorDto
{
    public function getDtoStore(array $data): array
    {
        return [
            'tipo_documento_id'     => $data['tipo_documento'],
            'tipo_documento_nombre' => $data['tipo_documento_nombre'],
            'nro_documento'         => $data['nro_documento'],
            'nombre'                => Str::upper($data['nombre'] ?? ''),
            'cargo_id'              => $data['cargo'],
            'direccion'             => Str::upper($data['direccion'] ?? ''),
            'telefono'              => $data['telefono'] ?? null,
            'dias_trabajo'          => $data['dias_trabajo'],
            'dias_descanso'         => $data['dias_descanso'],
            'pago_mensual'          => $data['pago_mensual'] ?? null,
            'pago_dia'              => $data['pago_mensual'] ? $data['pago_mensual'] / 30 : null,
            'sede_id'               => $data['sede'],
        ];
    }

    public function getDtoUpdate(array $data): array
    {
        return [
            'tipo_documento_id' => $data['tipo_documento'],
            'nro_documento'     => $data['nro_documento'],
            'nombre'            => Str::upper($data['nombre'] ?? ''),
            'cargo_id'          => $data['cargo'],
            'direccion'         => Str::upper($data['direccion'] ?? ''),
            'telefono'          => $data['telefono'] ?? null,
            'dias_trabajo'      => $data['dias_trabajo'],
            'dias_descanso'     => $data['dias_descanso'],
            'pago_mensual'      => $data['pago_mensual'] ?? null,
            'pago_dia'          => $data['pago_mensual'] ? $data['pago_mensual'] / 30 : null,
            'sede_id'           => $data['sede'],
        ];
    }
}
