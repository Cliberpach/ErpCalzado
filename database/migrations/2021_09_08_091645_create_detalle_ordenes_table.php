<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleOrdenesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_ordenes', function (Blueprint $table) {
            $table->Increments('id');
            $table->unsignedInteger('orden_id')->unsigned();

            $table->foreign('orden_id')
                  ->references('id')->on('ordenes')
                  ->onDelete('cascade');

            $table->unsignedInteger('producto_id')->unsigned();
            $table->foreign('producto_id')
                ->references('id')->on('productos')
                ->onDelete('cascade');

            $table->unsignedInteger('color_id')->unsigned();
            $table->foreign('color_id')
                ->references('id')->on('colores')
                ->onDelete('cascade');
            
            $table->unsignedInteger('talla_id')->unsigned();
            $table->foreign('talla_id')
                ->references('id')->on('tallas')
                ->onDelete('cascade');

            $table->BigInteger('cantidad');
            $table->unsignedDecimal('precio', 15,4);
            $table->unsignedDecimal('importe_producto_id', 15,2);

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
        Schema::dropIfExists('detalle_ordenes');
    }
}
