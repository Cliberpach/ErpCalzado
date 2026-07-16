<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fase 1.3 del plan de pendientes
 * (docs/PLANIFICATIONS/2026-07-15-plan-pendientes.md, ecommerceMerris) —
 * bloqueante para poder emitir cualquier comprobante fiscal (Fase 2,
 * plan-despacho-web-auto.md) desde una reserva confirmada.
 */
class AddDatosFiscalesToReservasWebTable extends Migration
{
    public function up()
    {
        Schema::table('reservas_web', function (Blueprint $table) {
            $table->string('doc_tipo', 10)->nullable()->after('cliente_direccion');
            $table->string('doc_numero', 20)->nullable()->after('doc_tipo');
            $table->boolean('desea_factura')->default(false)->after('doc_numero');
            $table->string('razon_social')->nullable()->after('desea_factura');
            $table->string('ruc', 11)->nullable()->after('razon_social');
        });
    }

    public function down()
    {
        Schema::table('reservas_web', function (Blueprint $table) {
            $table->dropColumn(['doc_tipo', 'doc_numero', 'desea_factura', 'razon_social', 'ruc']);
        });
    }
}
