<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotaIngresoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nota_ingreso', function (Blueprint $table) {

            $table->Increments('id');

            $table->unsignedInteger('registrador_id'); 
            $table->foreign('registrador_id')->references('id')->on('users');

         
            $table->unsignedInteger('almacen_destino_id'); 
            $table->foreign('almacen_destino_id')->references('id')->on('almacenes');

            $table->string("almacen_destino_nombre",160);

            $table->unsignedBigInteger('sede_id'); 
            $table->foreign('sede_id')->references('id')->on('empresa_sedes');

            $table->unsignedInteger('nota_salida_id')->nullable(); 
            $table->foreign('nota_salida_id')->references('id')->on('nota_salidad');

            $table->string("observacion",200)->nullable();

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
        Schema::dropIfExists('nota_ingreso');
    }
}
