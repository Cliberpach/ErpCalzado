<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRetencionDetallesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retencion_detalles', function (Blueprint $table) {
            $table->Increments('id');
            $table->unsignedInteger('retencion_id');
            $table->foreign('retencion_id')->references('id')->on('retencions')->onDelete('cascade');
            $table->unsignedInteger('documento_id')->nullable();
            $table->foreign('documento_id')->references('id')->on('cotizacion_documento')->onDelete('cascade');
            $table->string('tipoDoc');
            $table->string('numDoc');
            $table->date('fechaEmision');
            $table->date('fechaRetencion');
            $table->string('moneda');
            $table->unsignedDecimal('impTotal', 15, 2);
            $table->unsignedDecimal('impPagar', 15, 2);
            $table->unsignedDecimal('impRetenido', 15, 2);

            $table->string('moneda_pago')->default("PEN");
            $table->unsignedDecimal('importe_pago', 15, 2);
            $table->date('fecha_pago');

            $table->date('fecha_tipo_cambio');
            $table->unsignedBigInteger('factor')->default(1);
            $table->string('monedaObj')->default("PEN");
            $table->string('monedaRef')->default("PEN");
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
        Schema::dropIfExists('retencion_detalles');
    }
}
