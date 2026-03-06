<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleCuentaClienteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_cuenta_cliente', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('cuenta_cliente_id');
            $table->foreign('cuenta_cliente_id')->references('id')->on('cuenta_cliente')->onDelete('cascade');

            $table->unsignedBigInteger('mcaja_id');
            $table->foreign('mcaja_id')->references('id')->on('movimiento_caja')->onDelete('cascade');

            $table->date('fecha');
            $table->text('observacion')->nullable();
            $table->text('ruta_imagen')->nullable();
            $table->unsignedDecimal('monto', 15, 2);
            $table->unsignedInteger('tipo_pago_id');
            $table->foreign('tipo_pago_id')->references('id')->on('tipos_pago')->onDelete('cascade');
            $table->unsignedDecimal('efectivo', 15, 2)->nullable()->default(0.00);
            $table->unsignedDecimal('importe', 15, 2)->nullable()->default(0.00);
            $table->unsignedDecimal('saldo')->nullable();


            $table->unsignedBigInteger('cuenta_id')->nullable();
            $table->string('cuenta_nro_cuenta', 100)->nullable();
            $table->string('cuenta_cci', 100)->nullable();
            $table->string('cuenta_banco_nombre', 160)->nullable();
            $table->string('cuenta_moneda', 160)->nullable();

            $table->string('nro_operacion', 20)->nullable();

            $table->unsignedInteger('comprobante_id')->nullable();
            $table->string('comprobante_nro', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detalle_cuenta_cliente');
    }
}
