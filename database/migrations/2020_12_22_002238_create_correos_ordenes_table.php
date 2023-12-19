<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCorreosOrdenesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('correos_ordenes', function (Blueprint $table) {
            
            $table->Increments('id');
            $table->unsignedInteger('orden_id')->unsigned();
            $table->foreign('orden_id')
                  ->references('id')->on('ordenes')
                  ->onDelete('cascade');
            $table->char('enviado',1);
            $table->string('usuario');
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
        Schema::dropIfExists('correos_ordenes');
    }
}
