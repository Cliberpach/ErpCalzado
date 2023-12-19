<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleMovimientoEgresosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_movimiento_egresos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mcaja_id')->references('id')->on('movimiento_caja')->onDelete('cascade');
            $table->foreignId('egreso_id')->references('id')->on('egreso')->onDelete('cascade');
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
        Schema::dropIfExists('detalle_movimiento_egresos');
    }
}
