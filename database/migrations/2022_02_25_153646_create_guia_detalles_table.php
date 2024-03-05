<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuiaDetallesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guia_detalles', function (Blueprint $table) {
            $table->Increments('id');
            $table->unsignedInteger('guia_id');
            $table->foreign('guia_id')->references('id')->on('guias_remision')->onDelete('cascade');
            $table->unsignedBigInteger('producto_id');
            $table->unsignedBigInteger('color_id');
            $table->unsignedBigInteger('talla_id');
            $table->unsignedBigInteger('lote_id')->nullable();
            $table->text('codigo_producto');
            $table->text('nombre_producto');
            $table->string('nombre_modelo');
            $table->string('nombre_color');
            $table->string('nombre_talla');
            $table->unsignedDecimal('cantidad', 15, 2);
            $table->string('unidad')->default('NIU');
            $table->enum('estado',['ACTIVO','ANULADO'])->default('ACTIVO');
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
        Schema::dropIfExists('guia_detalles');
    }
}
