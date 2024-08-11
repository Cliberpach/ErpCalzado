<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdenesPedidoDetallesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordenes_pedido_detalles', function (Blueprint $table) {

            $table->unsignedBigInteger('orden_pedido_id');
            $table->foreign('orden_pedido_id')->references('id')->on('ordenes_pedido')->onDelete('cascade');


            $table->unsignedInteger('modelo_id');
            $table->foreign('modelo_id')->references('id')->on('modelos');

            $table->unsignedInteger('producto_id');
            $table->foreign('producto_id')->references('id')->on('productos');

            $table->unsignedInteger('color_id');
            $table->foreign('color_id')->references('id')->on('colores');

            $table->unsignedInteger('talla_id');
            $table->foreign('talla_id')->references('id')->on('tallas');

            $table->string('modelo_nombre'); 
            $table->string('producto_nombre'); 
            $table->string('color_nombre'); 
            $table->string('talla_nombre'); 

            $table->unsignedInteger('cantidad');

            $table->primary(['orden_pedido_id', 'modelo_id', 'producto_id', 'color_id', 'talla_id']);

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
        Schema::dropIfExists('ordenes_pedido_detalles');
    }
}
