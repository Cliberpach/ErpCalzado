<?php

namespace App\Http\Services\Mantenimiento\Sede;

use App\Http\Controllers\UtilidadesController;
use App\Http\Services\Almacen\Almacen\AlmacenService;
use App\Mantenimiento\Empresa\Numeracion;
use App\Mantenimiento\Sedes\Sede;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SedeService
{
    private SedeDto $s_dto;
    private SedeRepository $s_repository;
    private SedeValidation $s_validation;
    private AlmacenService $a_service;

    public function __construct()
    {
        $this->s_dto        = new SedeDto();
        $this->s_repository = new SedeRepository();
        $this->s_validation = new SedeValidation($this->s_repository);
        $this->a_service    = new AlmacenService();
    }

    public function store(array $data): Sede
    {
        $dto  = $this->s_dto->getDtoStore($data);
        $sede = $this->s_repository->store($dto);

        $carpeta = 'S' . $sede->id . '_' . $sede->ruc;

        $sede->carpeta_nombre = $carpeta;
        $sede->save();

        if (!Storage::exists("public/{$carpeta}")) {
            Storage::makeDirectory("public/{$carpeta}");
        }

        if (isset($data['img_empresa']) && $data['img_empresa'] instanceof UploadedFile) {
            $imagen    = $data['img_empresa'];
            $extension = $imagen->getClientOriginalExtension();
            $nombre    = 'LOGO' . $carpeta . '.' . $extension;
            $folder    = $carpeta . '/logo';

            UtilidadesController::saveFile($imagen, $nombre, $folder);

            $sede->logo_ruta   = $folder . '/' . $nombre;
            $sede->logo_nombre = $nombre;
            $sede->save();
        }

        $this->a_service->store([
            'sede_id'      => $sede->id,
            'descripcion'  => mb_substr('PRINCIPAL - ' . $sede->nombre, 0, 160, 'UTF-8'),
            'ubicacion'    => $sede->direccion,
            'tipo_almacen' => 'PRINCIPAL',
            'tipo'         => 'ALMACEN',
        ]);

        $this->a_service->store([
            'sede_id'      => $sede->id,
            'descripcion'  => mb_substr('MERMAS - ' . $sede->nombre, 0, 160, 'UTF-8'),
            'ubicacion'    => $sede->direccion,
            'tipo_almacen' => 'SECUNDARIO',
            'tipo'         => 'ALMACEN',
        ]);

        return $sede;
    }

    public function storeNumeracion(array $data): Numeracion
    {
        $sede_id = $data['sede_id'] ?? null;

        if (!$sede_id) {
            throw new \Exception("FALTA EL ID DE LA SEDE EN LA PETICIÓN!!!");
        }

        $this->s_validation->validateSedeActiva((int) $sede_id);
        $this->s_validation->validateNumeracionUnica((int) $data['comprobante_id'], (int) $sede_id);
        $tipoComprobante = $this->s_validation->validateTipoComprobante((int) $data['comprobante_id'], $data['parametro']);

        $dto = $this->s_dto->getDtoStoreNumeracion($data, $tipoComprobante);
        return $this->s_repository->storeNumeracion($dto);
    }

    public function update(array $data, int $id): Sede
    {
        $dto  = $this->s_dto->getDtoUpdate($data);
        $sede = $this->s_repository->update($dto, $id);

        if (isset($data['img_empresa']) && $data['img_empresa'] instanceof UploadedFile) {
            $carpeta   = $sede->carpeta_nombre;
            $imagen    = $data['img_empresa'];
            $extension = $imagen->getClientOriginalExtension();
            $nombre    = 'LOGO' . $carpeta . '.' . $extension;
            $folder    = $carpeta . '/logo';

            UtilidadesController::saveFile($imagen, $nombre, $folder);

            $sede->logo_ruta   = $folder . '/' . $nombre;
            $sede->logo_nombre = $nombre;
            $sede->save();
        }

        return $sede;
    }
}
