<?php

namespace App\Listeners;

use App\Events\ReservaWebResueltaEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Avisa a ecommerceMerris que una reserva_web se confirmó o anuló, para
 * que actualice el `Pedido` local del cliente (carrito Fase F,
 * docs/PLANIFICATIONS/2026-07-11-carrito-plan.md §7). Mismo mecanismo de
 * firma HMAC + mismo secreto/URL que NotificarEcommerceMerrisListener
 * (productos) — un solo canal de webhook hacia ecommerceMerris, distintos
 * endpoints por tipo de evento.
 */
class NotificarResolucionReservaWebListener implements ShouldQueue
{
    /** @var int */
    public $tries = 3;

    /** @var array */
    public $backoff = [10, 30, 60];

    public function handle(ReservaWebResueltaEvent $event)
    {
        $url    = config('services.ecommerce_merris.webhook_url_reserva_web');
        $secret = config('services.ecommerce_merris.webhook_secret');

        if (!$url || !$secret) {
            Log::warning('NotificarResolucionReservaWebListener: falta ECOMMERCE_MERRIS_RESERVA_WEB_WEBHOOK_URL o ECOMMERCE_MERRIS_WEBHOOK_SECRET en .env, no se notifica.');
            return;
        }

        $payload = [
            'codigo_pedido_ecommerce' => $event->codigoPedidoEcommerce,
            'estado'                  => $event->estado,
            'motivo_anulacion'        => $event->motivoAnulacion,
            'timestamp'               => now()->timestamp,
        ];

        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $signature = hash_hmac('sha256', $body, $secret);

        $response = Http::timeout(10)
            ->withHeaders(['X-Signature' => $signature])
            ->withBody($body, 'application/json')
            ->post($url);

        if (!$response->successful()) {
            throw new RuntimeException(
                "Webhook reserva-web a ecommerceMerris falló (codigo={$event->codigoPedidoEcommerce}, estado={$event->estado}): HTTP {$response->status()}"
            );
        }
    }
}
