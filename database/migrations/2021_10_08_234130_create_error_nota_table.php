<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateErrorNotaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('error_nota', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('nota_id')->nullable();
            $table->foreign('nota_id')->references('id')->on('nota_electronica')->onDelete('cascade');
            $table->text('tipo');
            $table->text('descripcion');
            $table->longText('ecxepcion');
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
        Schema::dropIfExists('error_nota');
    }
}
