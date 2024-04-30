<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetodosEntregaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('metodos_entrega', function (Blueprint $table) {
            $table->id();
            $table->string('empresa');
            $table->string('sede');
            $table->string('direccion')->nullable();
            $table->string('distrito');
            $table->string('departamento');
            $table->string('provincia');
            $table->string('tipo_envio');
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
        Schema::dropIfExists('agencias');
    }
}
