<?php

namespace App\Http\Services\Mantenimiento\Sede;

use App\Mantenimiento\Empresa\Numeracion;
use App\Mantenimiento\Sedes\Sede;
use Illuminate\Support\Facades\DB;

class SedeRepository
{
    public function store(array $dto): Sede
    {
        $sede = new Sede();
        $sede->fill($dto);
        $sede->save();
        return $sede;
    }

    public function update(array $dto, int $id): Sede
    {
        $sede = Sede::findOrFail($id);
        $sede->update($dto);
        return $sede;
    }

    public function findSedeActiva(int $sede_id): ?Sede
    {
        return Sede::where('id', $sede_id)->where('estado', 'ACTIVO')->first();
    }

    public function existsNumeracion(int $comprobante_id, int $sede_id): bool
    {
        return DB::table('empresa_numeracion_facturaciones')
            ->where('tipo_comprobante', $comprobante_id)
            ->where('sede_id', $sede_id)
            ->where('estado', 'ACTIVO')
            ->exists();
    }

    public function findTipoComprobante(int $comprobante_id, string $parametro): ?object
    {
        return DB::table('tabladetalles')
            ->where('id', $comprobante_id)
            ->where('parametro', $parametro)
            ->where('estado', 'ACTIVO')
            ->first();
    }

    public function storeNumeracion(array $dto): Numeracion
    {
        $numeracion                   = new Numeracion();
        $numeracion->empresa_id       = $dto['empresa_id'];
        $numeracion->sede_id          = $dto['sede_id'];
        $numeracion->serie            = $dto['serie'];
        $numeracion->tipo_comprobante = $dto['tipo_comprobante'];
        $numeracion->numero_iniciar   = $dto['numero_iniciar'];
        $numeracion->emision_iniciada = $dto['emision_iniciada'];
        $numeracion->numero_fin       = $dto['numero_fin'];
        $numeracion->save();
        return $numeracion;
    }
}
