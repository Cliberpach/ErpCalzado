<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fase 4.2 (docs/PLANIFICATIONS/2026-07-15-plan-pendientes.md): se dispara
 * al crear la reserva_web en Api\ReservasWeb\ReservaWebController::store()
 * — antes de Confirmar/Anular, apenas el cliente termina el checkout. Lo
 * captura EnviarCorreoPedidoRecibidoListener.
 */
class ReservaWebCreadaEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $reservaWebId;

    public function __construct(int $reservaWebId)
    {
        $this->reservaWebId = $reservaWebId;
    }
}
