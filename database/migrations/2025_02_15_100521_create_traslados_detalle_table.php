<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrasladosDetalleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('traslados_detalle', function (Blueprint $table) {
        
            $table->unsignedBigInteger('traslado_id');
            $table->unsignedInteger('almacen_id');
            $table->unsignedInteger('producto_id');
            $table->unsignedInteger('color_id');
            $table->unsignedInteger('talla_id');

            $table->string('almacen_nombre',160);
            $table->string('producto_nombre',160);
            $table->string('color_nombre',160);
            $table->string('talla_nombre',160);
         
            $table->integer('cantidad');
            $table->timestamps();

            $table->foreign('traslado_id')->references('id')->on('traslados');
            $table->foreign('producto_id')->references('id')->on('productos');
            $table->foreign('color_id')->references('id')->on('colores');
            $table->foreign('talla_id')->references('id')->on('tallas');
            $table->foreign('almacen_id')->references('id')->on('almacenes');

            $table->primary(['traslado_id', 'almacen_id', 'producto_id', 'color_id', 'talla_id'], 'pk_traslado_det');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('traslados_detalle');
    }
}
