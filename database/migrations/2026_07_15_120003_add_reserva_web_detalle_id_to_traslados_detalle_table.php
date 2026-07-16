<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Recojo en tienda (docs/PLANIFICATIONS/2026-07-15-plan-recojo-tienda.md
 * §2.3): liga un traslado creado desde "Cubrir stock" (mantenedor Reservas
 * Web) a la línea de reserva que está cubriendo. Al confirmarse recibido
 * (SolicitudTrasladoController::confirmarStore), esta columna es lo que
 * permite bajar `cantidad_pendiente` en la reserva. Nula en cualquier
 * traslado normal, no relacionado a una reserva web.
 */
class AddReservaWebDetalleIdToTrasladosDetalleTable extends Migration
{
    public function up()
    {
        Schema::table('traslados_detalle', function (Blueprint $table) {
            $table->unsignedBigInteger('reserva_web_detalle_id')->nullable()->after('cantidad');
            $table->foreign('reserva_web_detalle_id')->references('id')->on('reservas_web_detalle');
        });
    }

    public function down()
    {
        Schema::table('traslados_detalle', function (Blueprint $table) {
            $table->dropForeign(['reserva_web_detalle_id']);
            $table->dropColumn('reserva_web_detalle_id');
        });
    }
}
