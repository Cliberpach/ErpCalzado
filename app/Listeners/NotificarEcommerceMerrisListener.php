<?php

namespace App\Listeners;

use App\Events\ProductoActualizadoEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Fase 4.2/4.4: notifica a ecommerceMerris que un producto cambió, vía POST
 * firmado (HMAC). ShouldQueue para no bloquear el guardado del producto
 * (requiere `php artisan queue:work` corriendo — QUEUE_CONNECTION en este
 * proyecto ya es 'database', confirmado en .env real).
 *
 * El polling de sync:catalog en ecommerceMerris (cada 15 min) sigue activo
 * como red de seguridad: si este webhook falla o el worker está caído, el
 * catálogo igual se termina reconciliando solo.
 */
class NotificarEcommerceMerrisListener implements ShouldQueue
{
    /** @var int */
    public $tries = 3;

    /** @var array */
    public $backoff = [10, 30, 60];

    public function handle(ProductoActualizadoEvent $event)
    {
        $url = config('services.ecommerce_merris.webhook_url');
        $secret = config('services.ecommerce_merris.webhook_secret');

        if (!$url || !$secret) {
            Log::warning('NotificarEcommerceMerrisListener: falta ECOMMERCE_MERRIS_WEBHOOK_URL o ECOMMERCE_MERRIS_WEBHOOK_SECRET en .env, no se notifica.');
            return;
        }

        $payload = [
            'erp_id'    => $event->productoId,
            'accion'    => $event->accion,
            'timestamp' => now()->timestamp,
        ];

        // Firmar el body EXACTO que se envía (no reserializar en el receptor)
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $signature = hash_hmac('sha256', $body, $secret);

        $response = Http::timeout(10)
            ->withHeaders(['X-Signature' => $signature])
            ->withBody($body, 'application/json')
            ->post($url);

        if (!$response->successful()) {
            // Lanzar para que el mecanismo de reintentos de la cola actúe
            // (tries/backoff arriba); si se agotan, cae a failed_jobs.
            throw new RuntimeException(
                "Webhook a ecommerceMerris falló (producto erp_id={$event->productoId}, accion={$event->accion}): HTTP {$response->status()}"
            );
        }
    }
}
