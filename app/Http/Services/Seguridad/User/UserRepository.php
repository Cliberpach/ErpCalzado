<?php

namespace App\Http\Services\Seguridad\User;

use App\User;

class UserRepository
{
    public function store(array $dto): User
    {
        return User::create($dto);
    }

    public function update(User $user, array $dto): User
    {
        $user->update($dto);

        return $user;
    }

    public function destroy(User $user): User
    {
        $user->estado = 'ANULADO';
        $user->update();

        return $user;
    }
}
