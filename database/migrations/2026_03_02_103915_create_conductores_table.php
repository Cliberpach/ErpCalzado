<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConductoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conductores', function (Blueprint $table) {

            $table->increments('id');

            $table->unsignedInteger('tipo_documento_id');

            $table->string('nro_documento', 20);
            $table->index('nro_documento');

            $table->string('nombre_completo', 255);
            $table->string('nombres', 100);
            $table->string('apellidos', 100);

            $table->string('telefono', 20)->nullable();

            $table->string('licencia', 50);

            $table->string('registro_mtc', 160)->nullable();
            $table->string('tipo_documento_nombre', 160)->nullable();

            $table->enum('estado', ['ACTIVO', 'ANULADO'])
                ->default('ACTIVO')
                ->nullable();

            $table->string('modalidad_transporte', 160)->nullable();

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
        Schema::dropIfExists('conductores');
    }
}
