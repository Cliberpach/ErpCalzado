<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleNotaIngresoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_nota_ingreso', function (Blueprint $table) {
            $table->Increments('id');
            $table->unsignedInteger('nota_ingreso_id')->unsigned();
            $table->foreign('nota_ingreso_id')
                  ->references('id')->on('nota_ingreso')
                  ->onDelete('cascade');

            $table->unsignedInteger('producto_id')->unsigned();
            $table->foreign('producto_id')->references('id')->on('productos')->onDelete('cascade');
            $table->unsignedInteger('color_id')->unsigned();
            $table->foreign('color_id')->references('id')->on('colores')->onDelete('cascade');
            $table->unsignedInteger('talla_id')->unsigned();
            $table->foreign('talla_id')->references('id')->on('tallas')->onDelete('cascade');
            // $table->string('lote');
            // $table->unsignedInteger('lote_id')->unsigned()->nullable();
            // $table->foreign('lote_id')->references('id')->on('lote_productos')->onDelete('cascade');
            $table->unsignedDecimal('cantidad', 15,2);
            //$table->date("fecha_vencimiento");
            // $table->unsignedDecimal('costo', 15,4)->nullable();
            // $table->unsignedDecimal('costo_soles', 15,4)->nullable();
            // $table->unsignedDecimal('costo_dolares', 15,4)->nullable();
            // $table->unsignedDecimal('costo_mas_igv_soles', 15,4)->nullable();
            // $table->unsignedDecimal('costo_mas_igv_dolares', 15,4)->nullable();
            // $table->unsignedDecimal('valor_ingreso', 15,4)->nullable();
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
        Schema::dropIfExists('detalle_nota_ingreso');
    }
}
