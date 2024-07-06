<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleMovimientoVentaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_movimiento_venta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mcaja_id')->references('id')->on('movimiento_caja')->onDelete('cascade');
            $table->unsignedInteger('cdocumento_id');
            $table->string('cobrar',10)->default('SI');
            $table->foreign('cdocumento_id')->references('id')->on('cotizacion_documento')->onDelete('cascade');
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
        Schema::dropIfExists('detalle_movimiento_venta');
    }
}
