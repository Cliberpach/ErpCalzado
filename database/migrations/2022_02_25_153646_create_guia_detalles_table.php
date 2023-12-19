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
            $table->unsignedBigInteger('lote_id')->nullable();
            $table->text('codigo_producto');
            $table->text('nombre_producto');
            $table->unsignedDecimal('cantidad', 15, 2);
            $table->text('unidad');
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
