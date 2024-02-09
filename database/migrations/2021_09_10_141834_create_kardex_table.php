<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKardexTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kardex', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('producto_id');
            $table->foreign('producto_id')->references('id')->on('productos')->onDelete('cascade');
            $table->unsignedInteger('color_id');
            $table->foreign('color_id')->references('id')->on('colores')->onDelete('cascade');
            $table->unsignedInteger('talla_id');
            $table->foreign('talla_id')->references('id')->on('tallas')->onDelete('cascade');
            $table->text('origen')->nullable();
            $table->text('numero_doc')->nullable();
            $table->date('fecha')->nullable();
            $table->unsignedDecimal('cantidad', 15,2);
            $table->text('descripcion')->nullable();
            $table->unsignedDecimal('precio', 15,2)->nullable();
            $table->unsignedDecimal('importe', 15,2)->nullable();
            $table->unsignedDecimal('stock', 15,2); 
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
        Schema::dropIfExists('kardex');
    }
}
