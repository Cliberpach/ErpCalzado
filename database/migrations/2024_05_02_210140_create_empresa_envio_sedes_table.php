<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpresaEnvioSedesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empresa_envio_sedes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_envio_id');
            $table->foreign('empresa_envio_id')->references('id')->on('empresas_envio');
            $table->string('direccion');
            $table->string('departamento');
            $table->string('provincia');
            $table->string('distrito');
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
        Schema::dropIfExists('empresa_envio_sedes');
    }
}
