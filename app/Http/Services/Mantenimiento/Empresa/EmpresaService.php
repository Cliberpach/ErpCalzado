<?php

namespace App\Http\Services\Mantenimiento\Empresa;

use App\Facturacion\Helpers\Certificate\GenerateCertificate;
use App\Http\Controllers\UtilidadesController;
use App\Mantenimiento\Greenter\GreenterConfig;
use Exception;
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

    public function facturacionStore(array $data, ?UploadedFile $certificado): void
    {
        $config = GreenterConfig::where('empresa_id', 1)->where('modo', 'PRODUCCION')->first();

        if (!$config) {
            throw new Exception('No se encontró configuración Greenter. Ejecute el seeder primero.');
        }

        if ($certificado) {
            $extension  = strtolower($certificado->getClientOriginalExtension());
            $destDir    = storage_path('app/public/greenter/certificado');
            $destFile   = $destDir . DIRECTORY_SEPARATOR . 'certificate_merris.pem';

            if (!file_exists($destDir)) {
                mkdir($destDir, 0755, true);
            }

            if ($extension === 'pem') {
                $pemContent = file_get_contents($certificado->getRealPath());
            } else {
                // pfx / p12 → convertir a PEM vía openssl_pkcs12_read (cross-platform)
                $password   = $data['contra_certificado'] ?? '';
                $pfxBinary  = file_get_contents($certificado->getRealPath());
                $pemContent = GenerateCertificate::typePEM($pfxBinary, $password);

                if (!$pemContent) {
                    throw new Exception('No se pudo convertir el certificado a PEM. Verifique la contraseña.');
                }
            }

            file_put_contents($destFile, $pemContent);

            $config->ruta_certificado   = 'greenter/certificado/certificate_merris.pem';
            $config->nombre_certificado = 'certificate_merris.pem';
        }

        $config->id_api_guia_remision       = $data['id_api_guia_remision']   ?? $config->id_api_guia_remision;
        $config->clave_api_guia_remision    = $data['clave_api_guia_remision'] ?? $config->clave_api_guia_remision;
        $config->sol_user                   = $data['sol_user']                ?? $config->sol_user;
        $config->sol_pass                   = $data['sol_pass']                ?? $config->sol_pass;
        $config->cpe_client_id              = $data['cpe_client_id']           ?? $config->cpe_client_id;
        $config->cpe_client_secret          = $data['cpe_client_secret']       ?? $config->cpe_client_secret;

        $config->save();
    }
}
