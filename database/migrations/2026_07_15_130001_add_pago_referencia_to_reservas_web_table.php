<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sin pasarela de pago real todavía — solo referencia informativa de
 * lo que el cliente escribió en el checkout web (nunca el número
 * completo de tarjeta ni el CVV, ni fake). Sirve para que el staff
 * tenga con qué verificar el pago manualmente al Confirmar la reserva.
 */
class AddPagoReferenciaToReservasWebTable extends Migration
{
    public function up()
    {
        Schema::table('reservas_web', function (Blueprint $table) {
            $table->string('metodo_pago')->nullable()->after('total');
            $table->string('pago_titular')->nullable()->after('metodo_pago');
            $table->string('pago_tarjeta_last4', 4)->nullable()->after('pago_titular');
            $table->string('pago_banco')->nullable()->after('pago_tarjeta_last4');
            $table->string('pago_cuotas', 10)->nullable()->after('pago_banco');
            $table->string('pago_referencia')->nullable()->after('pago_cuotas');
        });
    }

    public function down()
    {
        Schema::table('reservas_web', function (Blueprint $table) {
            $table->dropColumn(['metodo_pago', 'pago_titular', 'pago_tarjeta_last4', 'pago_banco', 'pago_cuotas', 'pago_referencia']);
        });
    }
}
