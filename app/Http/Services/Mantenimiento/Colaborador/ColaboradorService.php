<?php

namespace App\Http\Services\Mantenimiento\Colaborador;

use App\Mantenimiento\Colaborador\Colaborador;
use Exception;
use Illuminate\Support\Facades\DB;

class ColaboradorService
{
    private ColaboradorDto $s_dto;
    private ColaboradorRepository $s_repository;

    public function __construct()
    {
        $this->s_dto        = new ColaboradorDto();
        $this->s_repository = new ColaboradorRepository();
    }

    public function store(array $data): Colaborador
    {
        $tipo_documento = DB::select(
            'SELECT td.* FROM tabladetalles AS td WHERE td.id = ?',
            [$data['tipo_documento']]
        )[0];

        $data['tipo_documento_nombre'] = $tipo_documento->simbolo;

        $dto = $this->s_dto->getDtoStore($data);

        return $this->s_repository->store($dto);
    }

    public function update(int $id, array $data): Colaborador
    {
        $colaborador = Colaborador::findOrFail($id);

        $movimiento = DB::select(
            'SELECT dmc.movimiento_id FROM detalles_movimiento_caja AS dmc WHERE dmc.colaborador_id = ? AND dmc.fecha_salida IS NULL',
            [$id]
        );

        if ($colaborador->sede_id != $data['sede'] && count($movimiento) !== 0) {
            throw new Exception('PERTENECES A UNA CAJA ABIERTA EN LA SEDE ACTUAL, NO PUEDES CAMBIARTE DE SEDE HASTA QUE LA CIERRES');
        }

        $dto         = $this->s_dto->getDtoUpdate($data);
        $colaborador = $this->s_repository->update($colaborador, $dto);

        DB::table('users')
            ->where('colaborador_id', $colaborador->id)
            ->update(['sede_id' => $data['sede'], 'updated_at' => now()]);

        return $colaborador;
    }

    public function destroy(int $id): Colaborador
    {
        $movimiento = DB::select(
            'SELECT dmc.movimiento_id FROM detalles_movimiento_caja AS dmc WHERE dmc.colaborador_id = ? AND dmc.fecha_salida IS NULL',
            [$id]
        );

        if (count($movimiento) !== 0) {
            throw new Exception('ESTE COLABORADOR NO PUEDE ELIMINARSE, PORQUE TIENE UNA CAJA ABIERTA');
        }

        $colaborador = Colaborador::findOrFail($id);

        return $this->s_repository->destroy($colaborador);
    }
}
