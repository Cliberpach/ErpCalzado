<?php

namespace App\Http\Services\Mantenimiento\Empresa;

use App\Http\Controllers\UtilidadesController;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class EmpresaService
{
    private EmpresaDto $s_dto;
    private EmpresaRepository $s_repository;

    public function __construct()
    {
        $this->s_dto    =   new EmpresaDto();
        $this->s_repository =   new EmpresaRepository();
    }

    public function update(array $data, int $id)
    {
        $dto        =   $this->s_dto->getDtoUpdate($data);
        $empresa    =   $this->s_repository->update($dto, $id);

        if (isset($data['logo']) && $data['logo'] instanceof UploadedFile) {
            $logo = $data['logo'];

            $extension  =   $logo->getClientOriginalExtension();
            $file_name  =   'logo_empresa.' . $extension;
            $folder     =   'logo_empresa';
            $ruta       =   $folder . '/' . $file_name;

            UtilidadesController::saveFile($logo, $file_name, $folder);
            $empresa->ruta_logo     =   $ruta;
            $empresa->nombre_logo   =   $file_name;
            $empresa->save();
        } else {
            UtilidadesController::deleteFile($empresa->ruta_logo);
            $empresa->ruta_logo     =   null;
            $empresa->nombre_logo   =   null;
            $empresa->save();
        }

        return $empresa;
    }
}
