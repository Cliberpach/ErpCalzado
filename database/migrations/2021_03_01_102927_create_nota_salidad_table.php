<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotaSalidadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nota_salidad', function (Blueprint $table) {

            $table->Increments('id');

            $table->unsignedBigInteger('sede_id'); 
            $table->foreign('sede_id')->references('id')->on('empresa_sedes');

            $table->unsignedInteger('registrador_id');
            $table->foreign('registrador_id')->references('id')->on('users');

            $table->unsignedBigInteger('almacen_origen_id');
            $table->foreign('almacen_origen_id')->references('id')->on('almacenes');

            $table->unsignedBigInteger('almacen_destino_id');
            $table->foreign('almacen_destino_id')->references('id')->on('almacenes');

            $table->string('registrador_nombre',200);
            $table->string('almacen_origen_nombre',160);
            $table->string('almacen_destino_nombre',160);

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
        Schema::dropIfExists('nota_salidad');
    }
}
