<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablaDetallesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tabladetalles', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->Increments('id');
            $table->string('descripcion');
            $table->string('nombre')->nullable();
            $table->string('simbolo')->nullable();
            $table->string('parametro')->nullable();
            $table->string('operacion')->nullable();
            $table->unsignedInteger('tabla_id')->unsigned();
            $table->foreign('tabla_id')
                  ->references('id')->on('tablas')
                  ->onDelete('cascade');
            $table->string('tipo')->nullable();
            $table->boolean('editable')->default(0);
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
        Schema::dropIfExists('tabladetalles');
    }
}
