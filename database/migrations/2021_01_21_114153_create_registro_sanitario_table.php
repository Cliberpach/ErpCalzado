<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistroSanitarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registro_sanitario', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('producto_id')->unsigned();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();;
            $table->string('ruta_archivo_word')->nullable();
            $table->string('archivo_word')->nullable();;
            $table->string('ruta_archivo_pdf')->nullable();
            $table->string('archivo_pdf')->nullable();;
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
        Schema::dropIfExists('registro_sanitario');
    }
}
