<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInformacionDetallesCaja extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalles_movimiento_caja', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('movimiento_id');
            $table->integer('usuario_id')->unsigned();
            //
           
            $table->foreign('usuario_id')->references('id')
            ->on('users');
            $table->foreign('movimiento_id')->references('id')
            ->on('movimiento_caja');
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
        Schema::dropIfExists('informacion_detalles_caja');
    }
}
