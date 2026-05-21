<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPagoImgColumnsToCotizacionDocumentoTable extends Migration
{
    public function up()
    {
        Schema::table('cotizacion_documento', function (Blueprint $table) {
            $table->string('pago_1_img_nombre', 200)->nullable()->after('pago_1_moneda');
            $table->string('pago_1_img_ruta', 500)->nullable()->after('pago_1_img_nombre');
            $table->string('pago_2_img_nombre', 200)->nullable()->after('pago_2_moneda');
            $table->string('pago_2_img_ruta', 500)->nullable()->after('pago_2_img_nombre');
        });
    }

    public function down()
    {
        Schema::table('cotizacion_documento', function (Blueprint $table) {
            $table->dropColumn([
                'pago_1_img_nombre', 'pago_1_img_ruta',
                'pago_2_img_nombre', 'pago_2_img_ruta',
            ]);
        });
    }
}
