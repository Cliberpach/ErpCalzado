<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoteDetalleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lote_detalle', function (Blueprint $table) {
            $table->Increments('id');
            $table->unsignedInteger('detalle_id');
            $table->foreign('detalle_id')->references('id')->on('cotizacion_documento_detalles')->onDelete('cascade');
            $table->unsignedInteger('lote_id');
            $table->foreign('lote_id')->references('id')->on('lote_productos')->onDelete('cascade');
            $table->unsignedDecimal('cantidad', 15,2);
            $table->enum('estado',['0','1'])->default('1');
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
        Schema::dropIfExists('lote_detalle');
    }
}
