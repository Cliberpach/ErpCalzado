<?php

namespace App\Http\Services\Ventas\Clientes;

use App\Models\Ventas\Cliente\Cliente;
use Illuminate\Support\Facades\Storage;

class ClienteService
{
    private ClienteRepository $s_repository;
    private ClienteDto $s_dto;

    public function __construct()
    {
        $this->s_repository =   new ClienteRepository();
        $this->s_dto        =   new ClienteDto();
    }

    public function store(array $data): Cliente
    {
        $dto        =   $this->s_dto->getDtoStore($data);
        $instance   =   $this->s_repository->store($dto);

        if (!empty($data['logo'])) {
            $this->saveImg($data['logo'], $instance);
        }
        return $instance;
    }

    public function update(array $data, int $id): Cliente
    {
        $dto        =   $this->s_dto->getDtoStore($data);
        $instance   =   $this->s_repository->update($dto, $id);

        if (!empty($data['logo'])) {
            $this->deleteimg($instance);
            $this->saveImg($data['logo'], $instance);
        } else {
            $this->deleteImg($instance);
        }

        return $instance;
    }

    public function deleteImg($instance)
    {
        if (!empty($instance->img_ruta) && Storage::disk('public')->exists($instance->img_ruta)) {
            Storage::disk('public')->delete($instance->img_ruta);
        }
    }
    public function saveImg($img, $instance)
    {
        if (!empty($img)) {
            $file = $img;

            $imgName = $instance->id . '_cliente.' . $file->getClientOriginalExtension();

            $path = $file->storeAs(
                'clientes',
                $imgName,
                'public'
            );

            $instance->update([
                'ruta_logo' => $path,
                'nombre_logo' => $imgName
            ]);
        }
    }
}
