<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Recojo en tienda (docs/PLANIFICATIONS/2026-07-15-plan-recojo-tienda.md
 * §2.2): sede_recojo_id es el destino final elegido por el cliente en el
 * checkout. NO es el almacén de descuento de la reserva (ese sigue siendo
 * `almacen_id`, que ahora también apunta al almacén PRINCIPAL de esta
 * misma sede cuando el método es recojo) — se guarda aparte porque
 * conceptualmente es "dónde entrega el negocio", útil para mostrarlo en
 * pantallas de seguimiento aunque hoy coincida con almacen_id.
 */
class AddSedeRecojoIdToReservasWebTable extends Migration
{
    public function up()
    {
        Schema::table('reservas_web', function (Blueprint $table) {
            $table->unsignedBigInteger('sede_recojo_id')->nullable()->after('almacen_id');
            $table->foreign('sede_recojo_id')->references('id')->on('empresa_sedes');
        });
    }

    public function down()
    {
        Schema::table('reservas_web', function (Blueprint $table) {
            $table->dropForeign(['sede_recojo_id']);
            $table->dropColumn('sede_recojo_id');
        });
    }
}
