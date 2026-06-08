<?php

namespace App\Http\Services\Seguridad\User;

class UserDto
{
    public function getDtoStore(array $data): array
    {
        $password = strtoupper($data['password']);

        return [
            'usuario'        => mb_strtoupper($data['usuario'] ?? '', 'UTF-8'),
            'email'          => mb_strtoupper($data['email'] ?? '', 'UTF-8'),
            'password'       => bcrypt($password),
            'contra'         => $password,
            'sede_id'        => $data['sede_id'],
            'colaborador_id' => $data['colaborador_id'],
        ];
    }

    public function getDtoUpdate(array $data): array
    {
        $password = strtoupper($data['password']);

        return [
            'usuario'        => mb_strtoupper($data['usuario'] ?? '', 'UTF-8'),
            'email'          => mb_strtoupper($data['email'] ?? '', 'UTF-8'),
            'password'       => bcrypt($password),
            'contra'         => $password,
            'sede_id'        => $data['sede_id'],
            'colaborador_id' => $data['colaborador_id'],
        ];
    }
}
