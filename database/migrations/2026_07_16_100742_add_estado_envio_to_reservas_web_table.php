<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Estado del despacho asociado (RESERVADO/DESPACHADO), reflejado acá para
 * mostrarlo en la vista de reserva_web sin JOIN contra envios_ventas.
 * No reemplaza `estado` (PENDIENTE/CONFIRMADO/ANULADO) — son máquinas de
 * estado independientes: una es el pedido, otra es el envío físico.
 * Actualizado por DespachoController@setEmbalaje/@setDespacho.
 */
class AddEstadoEnvioToReservasWebTable extends Migration
{
    public function up()
    {
        Schema::table('reservas_web', function (Blueprint $table) {
            $table->string('estado_envio')->nullable()->after('estado');
        });
    }

    public function down()
    {
        Schema::table('reservas_web', function (Blueprint $table) {
            $table->dropColumn('estado_envio');
        });
    }
}
