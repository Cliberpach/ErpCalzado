<?php

namespace App\Http\Services\Seguridad\User;

class UserDto
{
    public function getDtoStore(array $data): array
    {
        return [
            'usuario'        => mb_strtoupper($data['usuario'] ?? '', 'UTF-8'),
            'email'          => mb_strtoupper($data['email'] ?? '', 'UTF-8'),
            'password'       => bcrypt(strtoupper($data['password'])),
            'sede_id'        => $data['sede_id'],
            'colaborador_id' => $data['colaborador_id'],
        ];
    }

    /**
     * Al editar, la contraseña es opcional: si llega vacía no se toca la que
     * el usuario ya tenía.
     */
    public function getDtoUpdate(array $data): array
    {
        $dto = [
            'usuario'        => mb_strtoupper($data['usuario'] ?? '', 'UTF-8'),
            'email'          => mb_strtoupper($data['email'] ?? '', 'UTF-8'),
            'sede_id'        => $data['sede_id'],
            'colaborador_id' => $data['colaborador_id'],
        ];

        $password = isset($data['password']) ? trim((string) $data['password']) : '';

        if ($password !== '') {
            $dto['password'] = bcrypt(strtoupper($password));
        }

        return $dto;
    }
}
