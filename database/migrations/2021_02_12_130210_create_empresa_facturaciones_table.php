<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpresaFacturacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empresa_facturaciones', function (Blueprint $table) {
            $table->Increments('id');
            $table->unsignedInteger('empresa_id')->unsigned()->nullable();
            $table->foreign('empresa_id')
                  ->references('id')->on('empresas')
                  ->onDelete('cascade');
            $table->string('ruta_certificado_pfx')->nullable();
            $table->string('nombre_certificado_pfx')->nullable();
            $table->longText('contra_certificado')->nullable();
            $table->string('sol_user')->nullable();
            $table->string('sol_pass')->nullable();
            $table->string('plan')->nullable();
            $table->string('ambiente')->nullable();
            //////////////////////////////////////////////////
            $table->string('ruta_certificado_pem')->nullable();
            $table->longText('certificado')->nullable();
            $table->longText('token_code')->nullable();
            $table->BigInteger('fe_id')->nullable();
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
        Schema::dropIfExists('empresa_facturaciones');
    }
}
