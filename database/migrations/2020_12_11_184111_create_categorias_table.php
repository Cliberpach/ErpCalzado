<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categorias', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->Increments('id');
            $table->string('descripcion');
            $table->enum('estado', ['ACTIVO', 'ANULADO'])->default('ACTIVO');
            $table->enum('tipo', ['CATEGORIA', 'FICTICIO'])->default('CATEGORIA');
            $table->longText('img_ruta')->nullable();
            $table->longText('img_nombre')->nullable();
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
        Schema::dropIfExists('categorias');
    }
}
