<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Recojo en tienda (docs/PLANIFICATIONS/2026-07-15-plan-recojo-tienda.md
 * §2.2): stock/stock_logico son unsignedBigInteger, no admiten negativos.
 * Cuando la sede elegida por el cliente no alcanza, el faltante se guarda
 * acá en vez de forzar un negativo en producto_color_tallas.
 */
class AddCantidadPendienteToReservasWebDetalleTable extends Migration
{
    public function up()
    {
        Schema::table('reservas_web_detalle', function (Blueprint $table) {
            $table->unsignedInteger('cantidad_pendiente')->default(0)->after('cantidad');
        });
    }

    public function down()
    {
        Schema::table('reservas_web_detalle', function (Blueprint $table) {
            $table->dropColumn('cantidad_pendiente');
        });
    }
}
