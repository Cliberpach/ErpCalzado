<?php

namespace App\Providers;

use App\Listeners\BroadcastUserLogin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        \App\Events\VentaRegistrada::class => [
            \App\Listeners\CargarLotesVenta::class,
        ],
        \App\Events\FacturacionEmpresa::class => [
            \App\Listeners\RegistrarFacturacionEmpresa::class,
        ],
        \App\Events\EmpresaModificada::class => [
            \App\Listeners\ModificarFacturacionEmpresa::class,
        ],
        \App\Events\DocumentoNumeracion::class => [
            \App\Listeners\ConsultarTipoNumeracion::class,
        ],
        \App\Events\DocumentoNumeracionContingencia::class => [
            \App\Listeners\ConsultarTipoNumeracionContingencia::class,
        ],
        \App\Events\ComprobanteRegistrado::class => [
            \App\Listeners\GenerarComprobante::class,
        ],
        \App\Events\NumeracionGuiaRemision::class => [
            \App\Listeners\GenerarNumeracionGuia::class,
        ],
        \App\Events\GuiaRegistrado::class => [
            \App\Listeners\GenerarGuiaRemisionElectronica::class,
        ],
        \App\Events\NotaRegistrada::class => [
            \App\Listeners\GeneraNota::class,
        ],
        Login::class => [
            BroadcastUserLogin::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
