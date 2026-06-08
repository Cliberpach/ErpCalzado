<?php

namespace App\Console\Commands;

use App\Http\Services\Mantenimiento\CopiaSeguridad\CopiasSeguridadService;
use App\Jobs\Mantenimiento\CopiaSeguridad\GenerarBackupJob;
use Illuminate\Console\Command;

class CrearBackupAutomaticoCommand extends Command
{
    protected $signature   = 'backup:automatico';
    protected $description = 'Genera backup automático diario de la base de datos';

    public function handle(CopiasSeguridadService $service): int
    {
        $registro = $service->crearRegistro(null);
        GenerarBackupJob::dispatch($registro->id);
        $this->info("Backup en cola (registro #{$registro->id})");
        return 0;
    }
}
