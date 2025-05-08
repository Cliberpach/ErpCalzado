<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlmacenesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('almacenes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->Increments('id');

            $table->unsignedBigInteger('sede_id'); 
            $table->foreign('sede_id')->references('id')->on('empresa_sedes');

            $table->string('descripcion',160);
            $table->string('ubicacion');
            $table->enum('tipo_almacen', ['PRINCIPAL', 'SECUNDARIO'])->default('SECUNDARIO');
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
        Schema::dropIfExists('almacenes');
    }
}
