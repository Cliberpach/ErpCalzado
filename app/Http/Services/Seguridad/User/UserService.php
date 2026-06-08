<?php

namespace App\Http\Services\Seguridad\User;

use App\Mantenimiento\Colaborador\Colaborador;
use App\User;
use Exception;
use Illuminate\Support\Facades\DB;

class UserService
{
    private UserDto $s_dto;
    private UserRepository $s_repository;

    public function __construct()
    {
        $this->s_dto        = new UserDto();
        $this->s_repository = new UserRepository();
    }

    public function store(array $data): User
    {
        $colaborador     = Colaborador::findOrFail($data['colaborador_id']);
        $data['sede_id'] = $colaborador->sede_id;

        $dto  = $this->s_dto->getDtoStore($data);
        $user = $this->s_repository->store($dto);

        $user->roles()->sync($data['role'] ?? []);

        return $user;
    }

    public function destroy(int $id): User
    {
        $user = User::findOrFail($id);

        $movimiento = DB::select(
            'SELECT dmc.movimiento_id FROM detalles_movimiento_caja AS dmc WHERE dmc.colaborador_id = ? AND dmc.fecha_salida IS NULL',
            [$user->colaborador_id]
        );

        if (count($movimiento) !== 0) {
            throw new Exception('ESTE USUARIO NO PUEDE ELIMINARSE, PORQUE SU COLABORADOR TIENE UNA CAJA ABIERTA');
        }

        return $this->s_repository->destroy($user);
    }

    public function update(int $id, array $data): User
    {
        $user = User::findOrFail($id);

        $movimiento = DB::select(
            'SELECT dmc.movimiento_id FROM detalles_movimiento_caja AS dmc WHERE dmc.colaborador_id = ? AND dmc.fecha_salida IS NULL',
            [$user->colaborador_id]
        );

        if (count($movimiento) !== 0) {
            throw new Exception('ESTE USUARIO NO PUEDE MODIFICARSE, PORQUE SU COLABORADOR TIENE UNA CAJA ABIERTA');
        }

        $colaborador     = Colaborador::findOrFail($data['colaborador_id']);
        $data['sede_id'] = $colaborador->sede_id;

        $dto  = $this->s_dto->getDtoUpdate($data);
        $user = $this->s_repository->update($user, $dto);

        $user->roles()->sync($data['role'] ?? []);

        return $user;
    }
}
