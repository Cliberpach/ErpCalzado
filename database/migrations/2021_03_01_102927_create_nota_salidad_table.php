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
            $table->string("numero");
            $table->date("fecha");
            $table->string("origen")->nullable();
            $table->string("destino")->nullable();
            $table->string("observacion")->nullable();
            $table->string("usuario");
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
