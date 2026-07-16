<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fase 3 (docs/PLANIFICATIONS/2026-07-15-plan-pendientes.md): se
 * dispara cuando el despacho (`envios_ventas`) de una reserva_web pasa a
 * DESPACHADO — separado de `ReservaWebResueltaEvent` (que es semántica
 * de pago, no de envío) a propósito, decidido 2026-07-14
 * (plan-despacho.md §5).
 */
class ReservaWebEnvioActualizadoEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var string */
    public $codigoPedidoEcommerce;

    /** @var string 'ENTREGADO' */
    public $estadoEnvio;

    public function __construct(string $codigoPedidoEcommerce, string $estadoEnvio)
    {
        $this->codigoPedidoEcommerce = $codigoPedidoEcommerce;
        $this->estadoEnvio = $estadoEnvio;
    }
}
