<?php

namespace App\Listeners;

use App\Events\ReservaWebCreadaEvent;
use App\Mail\PedidoRecibidoMail;
use App\Models\Ventas\ReservaWeb\ReservaWeb;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

/**
 * Fase 4.2 (docs/PLANIFICATIONS/2026-07-15-plan-pendientes.md): envía el
 * correo "pedido recibido" apenas se crea la reserva_web — no espera a que
 * el staff Confirme el pago. ShouldQueue para no bloquear la respuesta de
 * store() (mismo patrón que NotificarResolucionReservaWebListener).
 */
class EnviarCorreoPedidoRecibidoListener implements ShouldQueue
{
    public $tries = 3;

    public $backoff = [10, 30, 60];

    public function handle(ReservaWebCreadaEvent $event): void
    {
        $reserva = ReservaWeb::with(['detalle.producto', 'detalle.color', 'detalle.talla', 'sedeRecojo'])
            ->find($event->reservaWebId);

        if (!$reserva) {
            return;
        }

        Mail::to($reserva->cliente_email)->send(new PedidoRecibidoMail($reserva));
    }
}
