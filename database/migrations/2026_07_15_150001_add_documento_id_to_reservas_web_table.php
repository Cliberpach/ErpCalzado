<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fase 2 (docs/PLANIFICATIONS/2026-07-15-plan-despacho-web-auto.md §3.5):
 * trazabilidad — qué Documento (comprobante) se generó al confirmar esta
 * reserva, para mostrarlo en la UI en vez de nada.
 */
class AddDocumentoIdToReservasWebTable extends Migration
{
    public function up()
    {
        Schema::table('reservas_web', function (Blueprint $table) {
            $table->unsignedInteger('documento_id')->nullable()->after('total');
        });
    }

    public function down()
    {
        Schema::table('reservas_web', function (Blueprint $table) {
            $table->dropColumn('documento_id');
        });
    }
}
