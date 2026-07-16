<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // Fase 4.2/4.4: webhook de producto hacia ecommerceMerris. Mismo
    // secreto se reutiliza para el webhook de reserva-web (Fase F del
    // carrito, docs/PLANIFICATIONS/2026-07-11-carrito-plan.md §7) — es un
    // canal de confianza único entre los dos sistemas, distintos endpoints
    // por tipo de evento, no distintos secretos.
    'ecommerce_merris' => [
        'webhook_url'              => env('ECOMMERCE_MERRIS_WEBHOOK_URL'),
        'webhook_url_reserva_web'  => env('ECOMMERCE_MERRIS_RESERVA_WEB_WEBHOOK_URL'),
        'webhook_url_reserva_envio' => env('ECOMMERCE_MERRIS_RESERVA_ENVIO_WEBHOOK_URL'),
        'webhook_secret'           => env('ECOMMERCE_MERRIS_WEBHOOK_SECRET'),
        // Dirección inversa: ecommerceMerris llamando a ErpCalzado para
        // crear una reserva_web al confirmar checkout.
        'reservas_web_api_token'   => env('RESERVAS_WEB_API_TOKEN'),
    ],

];
