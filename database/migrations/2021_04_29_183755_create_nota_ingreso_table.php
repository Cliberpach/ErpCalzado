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
            $table->string("numero");
            $table->date("fecha");
            $table->string("origen")->nullable();
            $table->string("destino")->nullable();
            $table->string("usuario");

            $table->string('moneda')->nullable();
            $table->unsignedDecimal('tipo_cambio', 15, 4)->nullable();
            $table->unsignedDecimal('dolar', 15, 4)->nullable();
            $table->unsignedDecimal('total', 15, 4)->nullable();
            $table->unsignedDecimal('total_soles', 15, 4)->nullable();
            $table->unsignedDecimal('total_dolares', 15, 4)->nullable();
            $table->unsignedDecimal('total_mas_igv_soles', 15, 4)->nullable();
            $table->unsignedDecimal('total_mas_igv_dolares', 15, 4)->nullable();

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
