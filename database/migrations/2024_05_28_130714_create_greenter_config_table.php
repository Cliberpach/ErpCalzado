<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGreenterConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('greenter_config', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('empresa_id')->unsigned();
            $table->longText('ruta_certificado');
            $table->string('nombre_certificado');
            $table->longText('id_api_guia_remision')->nullable();
            $table->longText('clave_api_guia_remision')->nullable();
            $table->enum('modo', ['BETA', 'PRODUCCION'])->default('BETA');
            $table->string('sol_user',100);
            $table->string('sol_pass',100);
            $table->foreign('empresa_id')
                  ->references('id')->on('empresas')
                  ->onDelete('cascade');
            


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
        Schema::dropIfExists('greenter_config');
    }
}
