<?php

namespace App\Jobs\Mantenimiento\CopiaSeguridad;

use App\Http\Services\Mantenimiento\CopiaSeguridad\CopiasSeguridadService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerarBackupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;
    public $tries = 1;

    protected $registroId;

    public function __construct($registroId)
    {
        $this->registroId = $registroId;
    }

    public function handle(CopiasSeguridadService $service)
    {
        $service->procesarBackup($this->registroId);
    }
}
