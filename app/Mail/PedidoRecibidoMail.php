<?php

namespace App\Mail;

use App\Models\Ventas\ReservaWeb\ReservaWeb;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Fase 4.2: confirma al cliente que su pedido llegó, apenas se crea la
 * reserva_web (antes de que el staff Confirme el pago). Se dispara desde
 * EnviarCorreoPedidoRecibidoListener.
 */
class PedidoRecibidoMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public ReservaWeb $reserva;

    public function __construct(ReservaWeb $reserva)
    {
        $this->reserva = $reserva;
    }

    public function build(): self
    {
        return $this
            ->subject("Recibimos tu pedido {$this->reserva->codigo_pedido_ecommerce}")
            ->view('emails.pedido_recibido');
    }
}
