<?php

namespace App\Http\Services\Mantenimiento\CopiaSeguridad;

use App\Mantenimiento\CopiaSeguridad\CopiaSeguridad;
use App\Mantenimiento\Empresa\Empresa;
use Illuminate\Support\Facades\Log;
use Throwable;

class CopiasSeguridadService
{
    private const MAX_BACKUPS = 3;

    private CopiasSeguridadRepository $repository;

    public function __construct()
    {
        $this->repository = new CopiasSeguridadRepository();
    }

    public function crearRegistro(?int $userId): CopiaSeguridad
    {
        return $this->repository->crearRegistro($userId);
    }

    /** Horas tras las que un GENERANDO se da por perdido. */
    private const HORAS_GENERANDO_COLGADO = 2;

    public function procesarBackup(int $registroId): void
    {
        $registro = CopiaSeguridad::findOrFail($registroId);

        // Antes de nada, cerrar los GENERANDO que quedaron colgados de
        // ejecuciones anteriores (worker caído a media faena).
        try {
            $this->repository->caducarGenerandoColgados(self::HORAS_GENERANDO_COLGADO);
        } catch (Throwable $th) {
            Log::warning('Backup #' . $registro->id . ': no se pudieron caducar los GENERANDO colgados: ' . $th->getMessage());
        }

        try {
            $sqlPath  = $this->repository->generarDump();
            $sqlName  = basename($sqlPath);

            if (!$this->repository->dumpEstaCompleto($sqlPath)) {
                throw new \RuntimeException('El volcado quedó incompleto: falta la marca final de mysqldump.');
            }

            $zipName = $this->repository->comprimirEnZip($sqlPath);
            $zipPath = $this->repository->rutaArchivo($zipName);

            if (!$this->repository->zipEsValido($zipPath, $sqlName)) {
                throw new \RuntimeException('El archivo comprimido no es válido o no contiene el volcado.');
            }

            $this->repository->marcarCompletado($registro, $zipName, $this->repository->tamanoArchivo($zipPath));
        } catch (Throwable $th) {
            $this->repository->marcarFallido($registro, $th->getMessage());
            throw $th;
        }

        // La retención va fuera del try y sólo con el backup ya COMPLETADO: si
        // fallara, no debe volver a marcar FALLIDO un respaldo que sí es bueno
        // (eso es justo lo que dejaba los 85 registros en FALLIDO).
        try {
            $this->repository->limpiarAntiguos(self::MAX_BACKUPS);
        } catch (Throwable $th) {
            Log::warning('Backup #' . $registro->id . ': la limpieza de antiguos falló: ' . $th->getMessage());
        }
    }

    public function eliminarBackup(int $id): void
    {
        $registro = CopiaSeguridad::findOrFail($id);

        if ($registro->nombre) {
            $this->repository->eliminarArchivo(basename($registro->nombre));
        }

        $this->repository->eliminarRegistro($id);
    }

    public function rutaBackup(int $id): string
    {
        $registro = CopiaSeguridad::findOrFail($id);

        if ($registro->estado !== 'COMPLETADO' || !$registro->nombre) {
            throw new \RuntimeException('El backup no está disponible para descarga.');
        }

        $path = $this->repository->rutaArchivoExistente(basename($registro->nombre));

        if ($path === null) {
            throw new \RuntimeException('Archivo no encontrado en disco.');
        }

        return $path;
    }

    /** ¿El zip de este registro sigue en disco? Decide si se ofrece descargarlo. */
    public function archivoDisponible(CopiaSeguridad $registro): bool
    {
        if ($registro->estado !== 'COMPLETADO' || !$registro->nombre) {
            return false;
        }

        return $this->repository->rutaArchivoExistente(basename($registro->nombre)) !== null;
    }

    /**
     * Nombre con el que se descarga: <RUC>_Backup_<AAAA-MM-DD_HHMM>.zip.
     * La fecha es la del registro, no la de hoy.
     */
    public function nombreDescarga(CopiaSeguridad $registro): string
    {
        $ruc = preg_replace('/[^A-Za-z0-9]/', '', (string) optional(Empresa::first())->ruc);

        if ($ruc === '') {
            $ruc = 'SIN-RUC';
        }

        $fecha = $registro->created_at ? $registro->created_at->format('Y-m-d_Hi') : now()->format('Y-m-d_Hi');

        return $ruc . '_Backup_' . $fecha . '.zip';
    }
}
