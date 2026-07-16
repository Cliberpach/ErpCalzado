<?php

namespace App\Jobs\Ventas;

use App\Http\Controllers\Ventas\Electronico\ComprobanteController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * ComprobanteController::email() usa Mail::send() síncrono (SMTP real,
 * 1-3+ seg de handshake) — llamarlo inline en confirmar() bloqueaba la
 * respuesta al staff (docs 2026-07-16). Se encola acá para que confirmar
 * responda al toque; el envío real lo hace queue:work en segundo plano.
 */
class EnviarComprobanteEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = 30;

    protected int $documentoId;
    protected string $correo;
    protected string $codigoPedido;

    public function __construct(int $documentoId, string $correo, string $codigoPedido)
    {
        $this->documentoId = $documentoId;
        $this->correo = $correo;
        $this->codigoPedido = $codigoPedido;
    }

    public function handle(): void
    {
        $res = (new ComprobanteController())->email(new Request([
            'id'     => $this->documentoId,
            'correo' => $this->correo,
        ]));

        $data = $res->getData();
        if (!$data->success) {
            Log::warning("No se pudo enviar el comprobante automático de la reserva web {$this->codigoPedido}: {$data->message}");
        }
    }
}
