<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTiposClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipos_clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 160);
            $table->enum('estado', ['ACTIVO', 'ANULADO'])->default('ACTIVO');

            $table->unsignedInteger('user_creator_id');
            $table->foreign('user_creator_id')->references('id')->on('users');

            $table->unsignedInteger('user_editor_id')->nullable();
            $table->foreign('user_editor_id')->references('id')->on('users');

            $table->unsignedInteger('user_deletor_id')->nullable();
            $table->foreign('user_deletor_id')->references('id')->on('users');

            $table->string('user_creator_name',191);
            $table->string('user_editor_name',191)->nullable();
            $table->string('user_deletor_name',191)->nullable();

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
        Schema::dropIfExists('tipos_clientes');
    }
}
