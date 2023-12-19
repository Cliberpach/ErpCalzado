<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovimientoCajaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimiento_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_id')->references('id')->on('caja')->onDelete('cascade');
            $table->unsignedInteger('colaborador_id');
            $table->foreign('colaborador_id')->references('id')->on('colaboradores')->onDelete('cascade');
            $table->decimal('monto_inicial');
            $table->decimal('monto_final')->nullable();
            $table->dateTime('fecha_apertura');
            $table->date('fecha'); //nuevo
            $table->dateTime('fecha_cierre')->nullable();
            $table->enum('estado_movimiento',['APERTURA','CIERRE']);
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
        Schema::dropIfExists('movimiento_caja');
    }
}
