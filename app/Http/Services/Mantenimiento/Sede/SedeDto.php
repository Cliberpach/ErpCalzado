<?php

namespace App\Http\Services\Mantenimiento\Sede;

use App\Mantenimiento\Empresa\Empresa;
use App\Mantenimiento\Ubigeo\Departamento;
use App\Mantenimiento\Ubigeo\Distrito;
use App\Mantenimiento\Ubigeo\Provincia;

class SedeDto
{
    public function getDtoUpdate(array $data): array
    {
        $empresa = Empresa::findOrFail(1);

        $departamentoId = str_pad($data['departamento'], 2, '0', STR_PAD_LEFT);
        $provinciaId    = str_pad($data['provincia'],    4, '0', STR_PAD_LEFT);
        $distritoId     = str_pad($data['distrito'],     6, '0', STR_PAD_LEFT);

        $departamento = Departamento::findOrFail($departamentoId);
        $provincia    = Provincia::findOrFail($provinciaId);
        $distrito     = Distrito::findOrFail($distritoId);

        return [
            'nombre'              => mb_strtoupper($data['nombre'] ?? '', 'UTF-8'),
            'empresa_id'          => $empresa->id,
            'ruc'                 => $empresa->ruc,
            'razon_social'        => $empresa->razon_social,
            'direccion'           => mb_strtoupper($data['direccion'] ?? '', 'UTF-8'),
            'telefono'            => $data['telefono'] ?? null,
            'correo'              => $data['correo'] ?? null,
            'departamento_id'     => $departamentoId,
            'provincia_id'        => $provinciaId,
            'distrito_id'         => $distritoId,
            'departamento_nombre' => $departamento->nombre,
            'provincia_nombre'    => $provincia->nombre,
            'distrito_nombre'     => $distrito->nombre,
            'codigo_local'        => $data['codigo_local'] ?? null,
            'urbanizacion'        => $data['urbanizacion'] ?? null,
        ];
    }

    public function getDtoStoreNumeracion(array $data, object $tipoComprobante): array
    {
        $serie = strtoupper(str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ñ'],
            ['a', 'e', 'i', 'o', 'u', 'n'],
            $tipoComprobante->parametro . ($data['serie'] ?? '')
        ));

        return [
            'empresa_id'       => 1,
            'sede_id'          => $data['sede_id'],
            'serie'            => $serie,
            'tipo_comprobante' => $tipoComprobante->id,
            'numero_iniciar'   => $data['nro_inicio'],
            'emision_iniciada' => '0',
            'numero_fin'       => null,
        ];
    }

    public function getDtoStore(array $data): array
    {
        $empresa = Empresa::findOrFail(1);

        $departamentoId = str_pad($data['departamento'], 2, '0', STR_PAD_LEFT);
        $provinciaId    = str_pad($data['provincia'],    4, '0', STR_PAD_LEFT);
        $distritoId     = str_pad($data['distrito'],     6, '0', STR_PAD_LEFT);

        $departamento = Departamento::findOrFail($departamentoId);
        $provincia    = Provincia::findOrFail($provinciaId);
        $distrito     = Distrito::findOrFail($distritoId);

        return [
            'nombre'              => mb_strtoupper($data['nombre'] ?? '', 'UTF-8'),
            'empresa_id'          => $empresa->id,
            'ruc'                 => $empresa->ruc,
            'razon_social'        => $empresa->razon_social,
            'direccion'           => mb_strtoupper($data['direccion'] ?? '', 'UTF-8'),
            'telefono'            => $data['telefono'] ?? null,
            'correo'              => $data['correo'] ?? null,
            'departamento_id'     => $departamentoId,
            'provincia_id'        => $provinciaId,
            'distrito_id'         => $distritoId,
            'departamento_nombre' => $departamento->nombre,
            'provincia_nombre'    => $provincia->nombre,
            'distrito_nombre'     => $distrito->nombre,
            'codigo_local'        => $data['codigo_local'] ?? null,
            'urbanizacion'        => $data['urbanizacion'] ?? null,
            'serie'               => $data['serie'] ?? null,
            'tipo_sede'           => 'SECUNDARIA',
        ];
    }
}
