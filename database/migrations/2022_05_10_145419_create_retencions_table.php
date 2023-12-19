<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRetencionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retencions', function (Blueprint $table) {
            $table->Increments('id');
            $table->unsignedInteger('documento_id')->nullable();
            $table->foreign('documento_id')->references('id')->on('cotizacion_documento')->onDelete('cascade');
            $table->string('serie')->nullable();
            $table->unsignedBigInteger('correlativo')->nullable();
            $table->date('fechaEmision');

            //COMPANY
            $table->string('ruc');
            $table->string('razonSocial');
            $table->string('nombreComercial');
            $table->string('direccion_empresa');
            $table->string('provincia_empresa');
            $table->string('departamento_empresa');
            $table->string('distrito_empresa');
            $table->string('ubigeo_empresa');

            //PROVEEDOR
            $table->string('tipoDoc');
            $table->string('numDoc');
            $table->string('rznSocial');
            $table->string('direccion_proveedor');
            $table->string('provincia_proveedor');
            $table->string('departamento_proveedor');
            $table->string('distrito_proveedor');
            $table->string('ubigeo_proveedor');

            $table->string('observacion')->nullable();
            $table->unsignedDecimal('impRetenido', 15, 2);
            $table->unsignedDecimal('impPagado', 15, 2);
            $table->string('regimen');
            $table->unsignedDecimal('tasa');

            $table->enum('sunat', ['0', '1', '2'])->default('0');

            $table->json('getCdrResponse')->nullable();
            $table->json('getRegularizeResponse')->nullable();
            $table->enum('regularize', ['0', '1'])->default('0');

            $table->enum('estado', ['ACTIVO', 'ANULADO'])->default('ACTIVO');
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
        Schema::dropIfExists('retencions');
    }
}
