<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductoColorTallasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('producto_color_tallas', function (Blueprint $table) {
            $table->unsignedInteger('producto_id'); 
            $table->unsignedInteger('color_id'); 
            $table->unsignedInteger('talla_id'); 

            $table->foreign('producto_id')->references('id')->on('productos');
            $table->foreign('color_id')->references('id')->on('colores');
            $table->foreign('talla_id')->references('id')->on('tallas');

            $table->bigInteger('stock');

            $table->timestamps();
            $table->primary(['producto_id', 'color_id','talla_id']); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('producto_color_tallas');
    }
}
