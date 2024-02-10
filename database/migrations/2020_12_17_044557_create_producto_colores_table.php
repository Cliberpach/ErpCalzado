<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductoColoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('producto_colores', function (Blueprint $table) {
            $table->unsignedInteger('producto_id'); 
            $table->unsignedInteger('color_id'); 
            $table->foreign('producto_id')->references('id')->on('productos');
            $table->foreign('color_id')->references('id')->on('colores');
            $table->enum('estado',['0','1'])->default('1');
            $table->timestamps();
            $table->primary(['producto_id', 'color_id']); 

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('producto_colores');
    }
}
