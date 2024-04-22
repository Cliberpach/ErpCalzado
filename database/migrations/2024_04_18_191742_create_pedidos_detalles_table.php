<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedidosDetallesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pedidos_detalles', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('pedido_id');
            $table->foreign('pedido_id')->references('id')->on('pedidos')->onDelete('cascade');;

            $table->unsignedInteger('producto_id');
            $table->foreign('producto_id')->references('id')->on('productos');

            $table->unsignedInteger('color_id');
            $table->foreign('color_id')->references('id')->on('colores');

            $table->unsignedInteger('talla_id');
            $table->foreign('talla_id')->references('id')->on('tallas');

            $table->string('producto_codigo')->nullable();
            $table->string('unidad')->default('NIU');
            $table->string('producto_nombre');
            $table->string('color_nombre');
            $table->string('talla_nombre');
            $table->string('modelo_nombre');
            $table->unsignedDecimal('cantidad', 15, 2);
            $table->unsignedDecimal('precio_unitario', 15, 2);
            $table->unsignedDecimal('importe', 15, 2);

            $table->unsignedDecimal('porcentaje_descuento', 15, 2)->nullable();
            $table->unsignedDecimal('precio_unitario_nuevo', 15, 2);
            $table->unsignedDecimal('importe_nuevo', 15, 2);
            $table->unsignedDecimal('monto_descuento', 15, 2)->nullable();

            $table->unsignedDecimal('cantidad_atendida', 15, 2);
            $table->unsignedDecimal('cantidad_pendiente', 15, 2);


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
        Schema::dropIfExists('pedidos_detalles');
    }
}
