<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCotizacionDocumentoDetallesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cotizacion_documento_detalles', function (Blueprint $table) {
            $table->Increments('id');
            $table->unsignedInteger('documento_id');
            $table->foreign('documento_id')->references('id')->on('cotizacion_documento')->onDelete('cascade');
            $table->unsignedInteger('lote_id');
            // $table->foreign('lote_id')->references('id')->on('lote_productos')->onDelete('cascade');
            $table->string('codigo_producto')->nullable();
            $table->string('unidad');
            $table->string('nombre_producto');
            $table->string('codigo_lote');
            $table->unsignedDecimal('cantidad', 15, 4);
            $table->unsignedDecimal('precio_inicial', 15, 2);
            $table->unsignedDecimal('precio_unitario', 15, 2);
            $table->unsignedDecimal('descuento', 15, 2)->default(0.00);
            $table->unsignedDecimal('dinero', 15, 2)->default(0.00);
            $table->unsignedDecimal('precio_nuevo', 15, 2);
            $table->unsignedDecimal('precio_minimo', 15, 2)->nullable();
            $table->unsignedDecimal('valor_unitario', 15, 2);
            $table->unsignedDecimal('valor_venta', 15, 2);
            $table->enum('estado', ['ACTIVO', 'ANULADO'])->default('ACTIVO');
            $table->enum('eliminado', ['0', '1'])->default('0');
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
        Schema::dropIfExists('cotizacion_documento_detalles');
    }
}
