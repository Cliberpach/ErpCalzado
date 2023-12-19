<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateErrorGuiaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('error_guia', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('guia_id')->nullable();
            $table->foreign('guia_id')->references('id')->on('guias_remision')->onDelete('cascade');
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
        Schema::dropIfExists('error_guia');
    }
}
