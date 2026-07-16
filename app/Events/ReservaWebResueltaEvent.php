<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Se dispara al Confirmar o Anular una reserva_web (carrito Fase F,
 * docs/PLANIFICATIONS/2026-07-11-carrito-plan.md §7). Lo captura
 * NotificarResolucionReservaWebListener y avisa a ecommerceMerris para
 * que actualice el `Pedido` local del cliente.
 */
class ReservaWebResueltaEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var string */
    public $codigoPedidoEcommerce;

    /** @var string 'CONFIRMADO' | 'ANULADO' */
    public $estado;

    /** @var string|null */
    public $motivoAnulacion;

    public function __construct(string $codigoPedidoEcommerce, string $estado, ?string $motivoAnulacion = null)
    {
        $this->codigoPedidoEcommerce = $codigoPedidoEcommerce;
        $this->estado = $estado;
        $this->motivoAnulacion = $motivoAnulacion;
    }
}
