<?php

namespace App\Http\Services\Mantenimiento\CopiaSeguridad;

use App\Mantenimiento\CopiaSeguridad\CopiaSeguridad;
use Illuminate\Support\Facades\File;
use PhpZip\ZipFile;

class CopiasSeguridadRepository
{
    private string $backupPath;

    public function __construct()
    {
        // Fuera de app/public: esta carpeta NO se sirve por HTTP. Los volcados
        // llevan la base entera y antes eran descargables sin sesión desde
        // /storage/backups/<archivo>.
        $this->backupPath = storage_path('app' . DIRECTORY_SEPARATOR . 'backups');
        $this->crearDirectorioSiNoExiste();
    }

    private function crearDirectorioSiNoExiste(): void
    {
        if (File::exists($this->backupPath)) {
            return;
        }

        // 0775: el cron y el worker pueden correr con un usuario distinto al de
        // php-fpm, pero dentro del mismo grupo (igual que el resto de storage/).
        File::makeDirectory($this->backupPath, 0775, true);
    }

    public function crearRegistro(?int $userId): CopiaSeguridad
    {
        return CopiaSeguridad::create([
            'estado'  => 'GENERANDO',
            'user_id' => $userId,
        ]);
    }

    public function marcarCompletado(CopiaSeguridad $registro, string $nombre, int $tamano): void
    {
        $registro->update([
            'nombre'       => $nombre,
            // Ruta relativa a storage/, no una URL: la carpeta ya no es pública
            // y la descarga pasa siempre por el controlador.
            'ruta'         => 'app/backups/' . $nombre,
            'tamano_bytes' => $tamano,
            'estado'       => 'COMPLETADO',
        ]);
    }

    public function marcarFallido(CopiaSeguridad $registro, string $error): void
    {
        $registro->update([
            'estado' => 'FALLIDO',
            'error'  => $error,
        ]);
    }

    public function eliminarRegistro(int $id): void
    {
        CopiaSeguridad::findOrFail($id)->delete();
    }

    /**
     * Deja sólo los $keep backups COMPLETADO más recientes y borra el resto:
     * los COMPLETADO antiguos y todos los FALLIDO, archivo y registro.
     *
     * Los que están GENERANDO no se tocan: puede haber otro backup en curso.
     *
     * Antes esto usaba skip() sin take(), que Eloquent traduce a un OFFSET sin
     * LIMIT y MySQL rechaza. Ahora se resuelve en dos pasos: primero qué ids se
     * conservan y después se borra todo lo demás.
     */
    public function limpiarAntiguos(int $keep): void
    {
        $conservar = CopiaSeguridad::where('estado', 'COMPLETADO')
            ->orderByDesc('id')
            ->take($keep)
            ->pluck('id')
            ->all();

        $sobrantes = CopiaSeguridad::whereIn('estado', ['COMPLETADO', 'FALLIDO'])
            ->when(count($conservar) > 0, function ($query) use ($conservar) {
                return $query->whereNotIn('id', $conservar);
            })
            ->get();

        foreach ($sobrantes as $registro) {
            // Que un archivo ya no esté en disco no puede impedir que se borre
            // el registro, ni tumbar el job.
            if ($registro->nombre) {
                try {
                    $this->eliminarArchivo($registro->nombre);
                } catch (\Throwable $th) {
                    // Se ignora: el registro se borra igual.
                }
            }

            $registro->delete();
        }
    }

    public function generarDump(): string
    {
        $config  = config('database.connections.mysql');
        $host    = $config['host'];
        $port    = $config['port'] ?? '3306';
        $user    = $config['username'];
        $pass    = $config['password'];
        $dbname  = $config['database'];

        $filename = 'backup_' . now()->format('Y_m_d_His') . '.sql';
        $sqlPath  = $this->backupPath . DIRECTORY_SEPARATOR . $filename;

        // Forward slashes work on Windows and Linux for --result-file
        $sqlPathNorm = str_replace('\\', '/', $sqlPath);

        // Do NOT escapeshellarg the binary — quoted executable breaks CMD on Windows
        $mysqldump = env('MYSQLDUMP_PATH', 'mysqldump');
        $passArg   = !empty($pass) ? '--password=' . escapeshellarg($pass) : '';

        $cmd = sprintf(
            '%s --user=%s %s --host=%s --port=%s --result-file=%s %s',
            $mysqldump,
            escapeshellarg($user),
            $passArg,
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($sqlPathNorm),
            escapeshellarg($dbname)
        );

        $output   = [];
        $exitCode = 0;
        exec($cmd . ' 2>&1', $output, $exitCode);

        if ($exitCode !== 0) {
            throw new \RuntimeException('mysqldump falló (código ' . $exitCode . '): ' . implode(' | ', $output));
        }

        if (!file_exists($sqlPath) || filesize($sqlPath) === 0) {
            throw new \RuntimeException('El archivo de respaldo no fue generado o está vacío.');
        }

        return $sqlPath;
    }

    /**
     * mysqldump cierra el volcado con una línea "-- Dump completed". Si no
     * está, el archivo quedó a medias (disco lleno, conexión cortada) y no
     * sirve como respaldo.
     */
    public function dumpEstaCompleto(string $sqlPath): bool
    {
        if (!file_exists($sqlPath)) {
            return false;
        }

        $tamano = filesize($sqlPath);
        if ($tamano === 0) {
            return false;
        }

        $manejador = fopen($sqlPath, 'rb');
        if ($manejador === false) {
            return false;
        }

        // El marcador va al final; basta con leer la cola del archivo.
        fseek($manejador, max(0, $tamano - 512));
        $cola = (string) fread($manejador, 512);
        fclose($manejador);

        return strpos($cola, '-- Dump completed') !== false;
    }

    public function comprimirEnZip(string $sqlPath): string
    {
        $zipName = pathinfo($sqlPath, PATHINFO_FILENAME) . '.zip';
        $zipPath = $this->backupPath . DIRECTORY_SEPARATOR . $zipName;

        $zip = new ZipFile();
        $zip->addFile($sqlPath, basename($sqlPath));
        $zip->saveAsFile($zipPath);
        $zip->close();

        File::delete($sqlPath);

        return $zipName;
    }

    /**
     * Comprueba que el .zip se puede abrir y que dentro está el .sql con
     * contenido. Sin esto se podría marcar COMPLETADO un zip corrupto.
     */
    public function zipEsValido(string $zipPath, string $sqlNombre): bool
    {
        if (!file_exists($zipPath) || filesize($zipPath) === 0) {
            return false;
        }

        $zip = new ZipFile();

        try {
            $zip->openFile($zipPath);

            if (!$zip->hasEntry($sqlNombre)) {
                return false;
            }

            return strlen($zip->getEntryContents($sqlNombre)) > 0;
        } catch (\Throwable $th) {
            return false;
        } finally {
            try {
                $zip->close();
            } catch (\Throwable $th) {
                // Nada que hacer si ni siquiera llegó a abrirse.
            }
        }
    }

    /**
     * Dónde puede estar un zip: la carpeta privada actual y, para registros
     * anteriores al cambio, la carpeta pública antigua. Así los backups viejos
     * se siguen pudiendo descargar y, sobre todo, borrar.
     *
     * @return array<int, string>
     */
    private function ubicacionesPosibles(string $filename): array
    {
        return [
            $this->backupPath . DIRECTORY_SEPARATOR . $filename,
            storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'backups' . DIRECTORY_SEPARATOR . $filename),
        ];
    }

    public function eliminarArchivo(string $filename): void
    {
        foreach ($this->ubicacionesPosibles($filename) as $path) {
            if (File::exists($path)) {
                File::delete($path);
            }
        }
    }

    /** Ruta real del zip, o null si no está en ninguna de las ubicaciones. */
    public function rutaArchivoExistente(string $filename): ?string
    {
        foreach ($this->ubicacionesPosibles($filename) as $path) {
            if (File::exists($path)) {
                return $path;
            }
        }

        return null;
    }

    public function rutaArchivo(string $filename): string
    {
        return $this->backupPath . DIRECTORY_SEPARATOR . $filename;
    }

    /**
     * Un GENERANDO que lleva demasiado tiempo quedó huérfano: el worker se cayó
     * o lo mataron antes de que el job terminase. Se marca FALLIDO para que no
     * se quede así indefinidamente y para que la retención pueda recogerlo.
     */
    public function caducarGenerandoColgados(int $horas): int
    {
        return CopiaSeguridad::where('estado', 'GENERANDO')
            ->where('created_at', '<', now()->subHours($horas))
            ->update([
                'estado'     => 'FALLIDO',
                'error'      => 'Sin respuesta tras ' . $horas . ' h: el proceso no terminó (worker caído o interrumpido).',
                'updated_at' => now(),
            ]);
    }

    public function tamanoArchivo(string $zipPath): int
    {
        return file_exists($zipPath) ? filesize($zipPath) : 0;
    }
}
