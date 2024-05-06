<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnviosVentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('envios_ventas', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('documento_id');
            $table->foreign('documento_id')->references('id')->on('cotizacion_documento')->onDelete('cascade');
            $table->string('departamento');
            $table->string('provincia');
            $table->string('distrito');
            $table->unsignedBigInteger('empresa_envio_id');
            $table->foreign('empresa_envio_id')->references('id')->on('empresas_envio');
            $table->string('empresa_envio_nombre');
            $table->unsignedBigInteger('sede_envio_id');
            $table->foreign('sede_envio_id')->references('id')->on('empresa_envio_sedes');
            $table->string('sede_envio_nombre');
            $table->string('tipo_envio');
            $table->string('destinatario_dni');
            $table->string('destinatario_nombre');
            $table->unsignedInteger('cliente_id');
            $table->foreign('cliente_id')->references('id')->on('clientes');            
            $table->string('cliente_nombre');
            $table->string('tipo_pago_envio');
            $table->decimal('monto_envio', 10, 2);
            $table->char('entrega_domicilio', 2);
            $table->string('direccion_entrega')->nullable();
            $table->string('documento_nro');
            $table->date('fecha_envio');
            $table->string('origen_venta');
            $table->text('observaciones')->nullable();
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
        Schema::dropIfExists('envios_ventas');
    }
}
