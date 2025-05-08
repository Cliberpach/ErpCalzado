<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpresaSedesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empresa_sedes', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('empresa_id'); 
            $table->foreign('empresa_id')->references('id')->on('empresas');

            $table->string('nombre',160);
            $table->string('ruc', 20);
            $table->string('razon_social');
            $table->string('direccion');
            $table->string('telefono')->nullable();
            $table->string('correo')->nullable();
            $table->unsignedBigInteger('departamento_id');
            $table->unsignedBigInteger('provincia_id');
            $table->unsignedBigInteger('distrito_id');
            $table->string('departamento_nombre');
            $table->string('provincia_nombre');
            $table->string('distrito_nombre');
            $table->string('codigo_local')->nullable();
            $table->enum('tipo_sede', ['PRINCIPAL', 'SECUNDARIA'])->default('SECUNDARIA');
            $table->string('serie')->nullable();
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
        Schema::dropIfExists('empresa_sedes');
    }
}
