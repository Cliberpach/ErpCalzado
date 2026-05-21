<?php

namespace App\Http\Services\Mantenimiento\Cuentas;

use App\Mantenimiento\Tabla\Detalle;
use Illuminate\Support\Facades\Auth;

class CuentaDto
{
    public function dtoStore(array $data): array
    {
        return [
            'nombre'              => mb_strtoupper($data['nombre'], 'UTF-8'),
            'titular'             => mb_strtoupper($data['titular'], 'UTF-8'),
            'moneda'              => mb_strtoupper($data['moneda'], 'UTF-8'),
            'banco_id'            => $data['banco_id'],
            'banco_nombre'        => Detalle::findOrFail($data['banco_id'])->descripcion,
            'nro_cuenta'          => $data['nro_cuenta'],
            'cci'                 => $data['cci']     ?? null,
            'celular'             => $data['celular'] ?? null,
            'editable'            => 1,
            'registrador_id'      => Auth::user()->id,
            'registrador_nombre'  => Auth::user()->usuario,
        ];
    }

    public function dtoUpdate(array $data): array
    {
        return [
            'nombre'       => mb_strtoupper($data['nombre'], 'UTF-8'),
            'titular'      => mb_strtoupper($data['titular'], 'UTF-8'),
            'moneda'       => mb_strtoupper($data['moneda'], 'UTF-8'),
            'banco_id'     => $data['banco_id'],
            'banco_nombre' => Detalle::findOrFail($data['banco_id'])->descripcion,
            'nro_cuenta'   => $data['nro_cuenta'],
            'cci'          => $data['cci']     ?? null,
            'celular'      => $data['celular'] ?? null,
        ];
    }
}
