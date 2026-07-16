<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Valida `Authorization: Bearer <token>` para el endpoint que
 * ecommerceMerris usa para crear una reserva_web al confirmar checkout
 * (carrito Fase F, docs/PLANIFICATIONS/2026-07-11-carrito-plan.md §7).
 * Es un endpoint que ESCRIBE (descuenta stock) — a diferencia del resto
 * de la API pública de ErpCalzado, que hoy es de solo lectura sin
 * autenticación (ver docs/ADR/001-fuente-de-verdad-catalogo.md), este no
 * puede quedar abierto.
 */
class VerifyReservasWebToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = config('services.ecommerce_merris.reservas_web_api_token');

        if (!$expected) {
            return response()->json(['success' => false, 'message' => 'Endpoint no configurado.'], 500);
        }

        $token = $request->bearerToken();

        if (!$token || !hash_equals($expected, $token)) {
            return response()->json(['success' => false, 'message' => 'Token inválido.'], 401);
        }

        return $next($request);
    }
}
