<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Switch DEMO/PRODUCCION para confirmar reservas web (docs/PLANIFICATIONS
 * de hoy): en DEMO se emite NOTA DE VENTA (tipo_venta_id 129, nunca va a
 * SUNAT) en vez de boleta/factura real. `modo_confirmacion` guarda con
 * qué modo se confirmó cada reserva, sin tener que joinear/parsear el
 * tipo_venta_id del documento generado. Reusa la tabla `configuracion`
 * (mismo patrón que `AG` de ConfiguracionController::setGreenterModo).
 */
class AddModoConfirmacionAndConfigReservasWeb extends Migration
{
    public function up()
    {
        Schema::table('reservas_web', function (Blueprint $table) {
            $table->enum('modo_confirmacion', ['DEMO', 'PRODUCCION'])->nullable()->after('estado_envio');
        });

        if (!DB::table('configuracion')->where('slug', 'RWM')->exists()) {
            DB::table('configuracion')->insert([
                'slug'        => 'RWM',
                'descripcion' => 'Modo Reservas Web (DEMO emite Nota de Venta / PRODUCCION emite Boleta o Factura)',
                'propiedad'   => 'PRODUCCION',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }

    public function down()
    {
        Schema::table('reservas_web', function (Blueprint $table) {
            $table->dropColumn('modo_confirmacion');
        });

        DB::table('configuracion')->where('slug', 'RWM')->delete();
    }
}
