<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecibosCajaDetalleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recibos_caja_detalle', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('recibo_id');
            $table->unsignedInteger('documento_id');
            $table->decimal('saldo_antes', 10, 2)->nullable();
            $table->decimal('monto_usado', 10, 2)->nullable();
            $table->decimal('saldo_despues', 10, 2)->nullable();
          
            $table->index('recibo_id');
            $table->index('documento_id');

            $table->foreign('recibo_id')->references('id')->on('recibos_caja')->onDelete('cascade');
            $table->foreign('documento_id')->references('id')->on('cotizacion_documento');
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
        Schema::table('recibos_caja_detalle', function (Blueprint $table) {
            $table->dropForeign(['recibo_id']);
            $table->dropForeign(['documento_id']);
        });
        Schema::dropIfExists('recibos_caja_detalle');
    }
}
