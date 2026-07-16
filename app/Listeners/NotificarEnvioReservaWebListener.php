<?php

namespace App\Listeners;

use App\Events\ReservaWebEnvioActualizadoEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Avisa a ecommerceMerris que el despacho de una reserva_web cambió de
 * estado (Fase 3, docs/PLANIFICATIONS/2026-07-15-plan-pendientes.md).
 * Mismo mecanismo de firma HMAC que NotificarResolucionReservaWebListener
 * — un canal de webhook, distinto endpoint por tipo de evento.
 */
class NotificarEnvioReservaWebListener implements ShouldQueue
{
    /** @var int */
    public $tries = 3;

    /** @var array */
    public $backoff = [10, 30, 60];

    public function handle(ReservaWebEnvioActualizadoEvent $event)
    {
        $url    = config('services.ecommerce_merris.webhook_url_reserva_envio');
        $secret = config('services.ecommerce_merris.webhook_secret');

        if (!$url || !$secret) {
            Log::warning('NotificarEnvioReservaWebListener: falta ECOMMERCE_MERRIS_RESERVA_ENVIO_WEBHOOK_URL o ECOMMERCE_MERRIS_WEBHOOK_SECRET en .env, no se notifica.');
            return;
        }

        $payload = [
            'codigo_pedido_ecommerce' => $event->codigoPedidoEcommerce,
            'estado_envio'            => $event->estadoEnvio,
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
                "Webhook reserva-envio a ecommerceMerris falló (codigo={$event->codigoPedidoEcommerce}, estado={$event->estadoEnvio}): HTTP {$response->status()}"
            );
        }
    }
}
