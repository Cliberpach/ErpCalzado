<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCopiasSeguridadTable extends Migration
{
    public function up()
    {
        Schema::create('copias_seguridad', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->nullable();
            $table->string('ruta')->nullable();
            $table->unsignedBigInteger('tamano_bytes')->nullable();
            $table->enum('estado', ['GENERANDO', 'COMPLETADO', 'FALLIDO'])->default('GENERANDO');
            $table->text('error')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('copias_seguridad');
    }
}
