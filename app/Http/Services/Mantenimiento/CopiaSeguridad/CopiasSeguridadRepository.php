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
        $this->backupPath = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'backups');
        $this->crearDirectorioSiNoExiste();
    }

    private function crearDirectorioSiNoExiste(): void
    {
        if (!File::exists($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }
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
            'ruta'         => 'storage/backups/' . $nombre,
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

    public function eliminarArchivo(string $filename): void
    {
        $path = $this->backupPath . DIRECTORY_SEPARATOR . $filename;
        if (File::exists($path)) {
            File::delete($path);
        }
    }

    public function rutaArchivo(string $filename): string
    {
        return $this->backupPath . DIRECTORY_SEPARATOR . $filename;
    }

    public function tamanoArchivo(string $zipPath): int
    {
        return file_exists($zipPath) ? filesize($zipPath) : 0;
    }
}
