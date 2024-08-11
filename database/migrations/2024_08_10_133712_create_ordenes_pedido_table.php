<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdenesPedidoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordenes_pedido', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedInteger('user_id'); 
            $table->string('user_nombre'); 
            $table->date('fecha_propuesta_atencion')->nullable();
            $table->string('observacion',260)->nullable();
            $table->timestamps(); 

            $table->foreign('user_id')->references('id')->on('users'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ordenes_pedido');
    }
}
