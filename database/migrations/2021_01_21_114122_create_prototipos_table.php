<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrototiposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prototipos', function (Blueprint $table) {
            $table->Increments('id');
            $table->string('producto');
            $table->date('fecha_registro')->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->string('registro')->nullable();
            $table->string('ruta_imagen')->nullable();
            $table->string('imagen')->nullable();
            $table->string('ruta_archivo_word')->nullable();
            $table->string('archivo_word')->nullable();
            $table->BigInteger('usuario_id');
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
        Schema::dropIfExists('prototipos');
    }
}
