<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateColoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('colores', function (Blueprint $table) {
            $table->Increments('id');
            $table->string('descripcion');
            $table->string('codigo', 20)->nullable();
            $table->longText('img_ruta')->nullable();
            $table->string('img_nombre', 200)->nullable();
            $table->enum('estado', ['ACTIVO', 'ANULADO'])->default('ACTIVO');
            $table->enum('tipo', ['COLOR', 'FICTICIO'])->default('COLOR');
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
        Schema::dropIfExists('colores');
    }
}
