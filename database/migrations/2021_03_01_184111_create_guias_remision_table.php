<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuiasRemisionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guias_remision', function (Blueprint $table) {
            $table->Increments('id');
            $table->unsignedInteger('documento_id')->nullable();
            $table->foreign('documento_id')->references('id')->on('cotizacion_documento')->onDelete('cascade');
            $table->unsignedInteger('nota_salida_id')->nullable();
            $table->foreign('nota_salida_id')->references('id')->on('nota_salidad')->onDelete('cascade');
            $table->unsignedDecimal('cantidad_productos', 15, 2);
            $table->unsignedDecimal('peso_productos', 15, 3);
            $table->text('tienda')->nullable();

            $table->string('ruc_transporte_oficina')->nullable();
            $table->string('nombre_transporte_oficina')->nullable();

            $table->string('ruc_transporte_domicilio')->nullable();
            $table->string('nombre_transporte_domicilio')->nullable();
            $table->string('direccion_llegada')->nullable();

            $table->mediumText('observacion')->nullable();
            $table->enum('estado',['REGISTRADO','ACEPTADO','NULO'])->default('REGISTRADO');

            $table->enum('sunat',['0','1','2'])->default('0');
            $table->BigInteger('correlativo')->nullable();
            $table->string('serie')->nullable();

            $table->string('ruta_comprobante_archivo')->nullable();
            $table->string('nombre_comprobante_archivo')->nullable();

            $table->string('dni_conductor')->nullable();
            $table->string('placa_vehiculo')->nullable();

            $table->string('ubigeo_partida')->nullable();
            $table->string('ubigeo_llegada')->nullable();

            $table->date('fecha_emision')->nullable();
            $table->text('ruc_empresa')->nullable();
            $table->text('empresa')->nullable();
            $table->text('direccion_empresa')->nullable();
            $table->unsignedInteger('empresa_id');

            $table->text('tipo_documento_cliente')->nullable();
            $table->text('documento_cliente')->nullable();
            $table->text('cliente')->nullable();
            $table->text('direccion_cliente')->nullable();
            $table->unsignedInteger('cliente_id');

            $table->unsignedInteger('motivo_traslado');

            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->json('getCdrResponse')->nullable();
            $table->json('getRegularizeResponse')->nullable();
            $table->enum('regularize', ['0', '1'])->default('0');
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
        Schema::dropIfExists('guias_remision');
    }
}
