<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResumenesDetallesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resumenes_detalles', function (Blueprint $table) {
            $table->unsignedBigInteger('resumen_id');
            $table->unsignedInteger('documento_id');
            $table->string('documento_serie', 5)->nullable();
            $table->bigInteger('documento_correlativo')->nullable();
            $table->decimal('documento_subtotal', 10, 2)->nullable();
            $table->decimal('documento_igv', 10, 2)->nullable();
            $table->decimal('documento_total', 10, 2)->nullable();
            $table->string('documento_doc_cliente', 20)->nullable();

            $table->enum('estado', ['ACTIVO', 'ANULADO'])->default('ACTIVO');
            
            $table->primary(['resumen_id', 'documento_id']);
            $table->foreign('resumen_id')->references('id')->on('resumenes')->onDelete('cascade');
            $table->foreign('documento_id')->references('id')->on('cotizacion_documento')->onDelete('cascade');
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
        Schema::dropIfExists('resumenes_detalles');
    }
}
