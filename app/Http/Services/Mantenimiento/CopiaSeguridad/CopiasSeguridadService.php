<?php

namespace App\Http\Services\Mantenimiento\CopiaSeguridad;

use App\Mantenimiento\CopiaSeguridad\CopiaSeguridad;
use Illuminate\Support\Facades\File;
use Throwable;

class CopiasSeguridadService
{
    private CopiasSeguridadRepository $repository;

    public function __construct()
    {
        $this->repository = new CopiasSeguridadRepository();
    }

    public function crearRegistro(?int $userId): CopiaSeguridad
    {
        return $this->repository->crearRegistro($userId);
    }

    public function procesarBackup(int $registroId): void
    {
        $registro = CopiaSeguridad::findOrFail($registroId);

        try {
            $sqlPath = $this->repository->generarDump();
            $zipName = $this->repository->comprimirEnZip($sqlPath);
            $tamano  = $this->repository->tamanoArchivo($this->repository->rutaArchivo($zipName));

            $this->repository->marcarCompletado($registro, $zipName, $tamano);
        } catch (Throwable $th) {
            $this->repository->marcarFallido($registro, $th->getMessage());
            throw $th;
        }
    }

    public function eliminarBackup(int $id): void
    {
        $registro = CopiaSeguridad::findOrFail($id);

        if ($registro->ruta) {
            File::delete(public_path($registro->ruta));
        }

        $this->repository->eliminarRegistro($id);
    }

    public function rutaBackup(int $id): string
    {
        $registro = CopiaSeguridad::findOrFail($id);

        if ($registro->estado !== 'COMPLETADO' || !$registro->ruta) {
            throw new \RuntimeException('El backup no está disponible para descarga.');
        }

        $filename = basename($registro->ruta);
        $path     = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'backups' . DIRECTORY_SEPARATOR . $filename);

        if (!File::exists($path)) {
            throw new \RuntimeException('Archivo no encontrado en disco.');
        }

        return $path;
    }
}
