<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPago2ColumnsToCotizacionDocumentoTable extends Migration
{
    public function up()
    {
        Schema::table('cotizacion_documento', function (Blueprint $table) {
            $table->unsignedInteger('pago_2_tipo_pago_id')->nullable()->after('pago_1_tipo_pago_id');
            $table->string('pago_2_tipo_pago_nombre', 160)->nullable();
            $table->unsignedDecimal('pago_2_monto', 15, 6)->nullable();
            $table->string('pago_2_nro_operacion', 30)->nullable();
            $table->date('pago_2_fecha_operacion')->nullable();
            $table->unsignedBigInteger('pago_2_cuenta_id')->nullable();
            $table->string('pago_2_banco_nombre', 160)->nullable();
            $table->string('pago_2_nro_cuenta', 100)->nullable();
            $table->string('pago_2_cci', 100)->nullable();
            $table->string('pago_2_celular', 20)->nullable();
            $table->string('pago_2_titular', 200)->nullable();
            $table->string('pago_2_moneda', 160)->nullable();
        });
    }

    public function down()
    {
        Schema::table('cotizacion_documento', function (Blueprint $table) {
            $table->dropColumn([
                'pago_2_tipo_pago_id', 'pago_2_tipo_pago_nombre', 'pago_2_monto',
                'pago_2_nro_operacion', 'pago_2_fecha_operacion', 'pago_2_cuenta_id',
                'pago_2_banco_nombre', 'pago_2_nro_cuenta', 'pago_2_cci',
                'pago_2_celular', 'pago_2_titular', 'pago_2_moneda',
            ]);
        });
    }
}
