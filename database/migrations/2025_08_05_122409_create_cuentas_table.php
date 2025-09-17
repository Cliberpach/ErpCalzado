<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuentas', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('banco_id');
            $table->foreign('banco_id')->references('id')->on('tabladetalles');

            $table->string('banco_nombre', 160);

            $table->string('nro_cuenta', 100);
            $table->string('cci', 100)->nullable();

            $table->string('celular', 20)->nullable();
            $table->string('titular',200);

            $table->string('moneda',160);

            $table->unsignedInteger('registrador_id');
            $table->foreign('registrador_id')->references('id')->on('users');
            $table->string('registrador_nombre', 200);

            $table->unsignedInteger('eliminador_id')->nullable();
            $table->foreign('eliminador_id')->references('id')->on('users');
            $table->string('eliminador_nombre', 200)->nullable();

            $table->enum('estado', ['ACTIVO', 'ANULADO'])->default('ACTIVO');

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
        Schema::dropIfExists('cuentas');
    }
}
