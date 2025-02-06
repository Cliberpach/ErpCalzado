<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleNotaSalidadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_nota_salidad', function (Blueprint $table) {
            $table->Increments('id');

            $table->unsignedInteger('nota_salidad_id')->unsigned();
            $table->foreign('nota_salidad_id')->references('id')->on('nota_salidad');

            $table->unsignedInteger('almacen_id')->unsigned();
            $table->foreign('almacen_id')->references('id')->on('almacenes');

            $table->unsignedInteger('producto_id')->unsigned();
            $table->foreign('producto_id')->references('id')->on('productos')->onDelete('cascade');
            
            $table->unsignedInteger('color_id')->unsigned();
            $table->foreign('color_id')->references('id')->on('colores')->onDelete('cascade');
            
            $table->unsignedInteger('talla_id')->unsigned();
            $table->foreign('talla_id')->references('id')->on('tallas')->onDelete('cascade');
           
            
            $table->string('almacen_nombre',160);
            $table->string('producto_nombre',160);
            $table->string('color_nombre',160);
            $table->string('talla_nombre',160);
            
            $table->unsignedDecimal('cantidad', 15,2);

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
        Schema::dropIfExists('detalle_nota_salidad');
    }
}
