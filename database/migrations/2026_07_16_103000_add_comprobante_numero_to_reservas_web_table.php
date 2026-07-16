<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Serie-correlativo del comprobante generado al confirmar, guardado acá
 * para mostrarlo en el listado sin JOIN/eager-load contra cotizacion_documento.
 * Seteado por ReservaWebController@confirmar en el mismo momento que
 * guarda `documento_id`.
 */
class AddComprobanteNumeroToReservasWebTable extends Migration
{
    public function up()
    {
        Schema::table('reservas_web', function (Blueprint $table) {
            $table->string('comprobante_numero')->nullable()->after('documento_id');
        });
    }

    public function down()
    {
        Schema::table('reservas_web', function (Blueprint $table) {
            $table->dropColumn('comprobante_numero');
        });
    }
}
