<?php

namespace App\Http\Services\Seguridad\User;

use App\User;

class UserManager
{
    private UserService $s_service;

    public function __construct()
    {
        $this->s_service = new UserService();
    }

    public function store(array $data): User
    {
        return $this->s_service->store($data);
    }

    public function update(int $id, array $data): User
    {
        return $this->s_service->update($id, $data);
    }

    public function destroy(int $id): User
    {
        return $this->s_service->destroy($id);
    }
}
