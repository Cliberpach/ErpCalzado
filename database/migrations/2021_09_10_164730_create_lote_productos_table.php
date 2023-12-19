<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoteProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lote_productos', function (Blueprint $table) {
            $table->Increments('id');
            $table->string('codigo_lote')->nullable();
            $table->unsignedInteger('compra_documento_id')->nullable();
            $table->foreign('compra_documento_id')->references('id')->on('compra_documentos')->onDelete('SET NULL');

            $table->unsignedInteger('nota_ingreso_id')->nullable();
            $table->foreign('nota_ingreso_id')->references('id')->on('nota_ingreso')->onDelete('SET NULL');

            $table->unsignedInteger('producto_id');
            $table->foreign('producto_id')->references('id')->on('productos')->onDelete('cascade');

            $table->unsignedDecimal('cantidad', 15,4);
            $table->unsignedDecimal('cantidad_logica', 15,4);
            $table->unsignedDecimal('cantidad_inicial', 15,4)->nullable();

            $table->date('fecha_vencimiento');
            $table->date('fecha_entrega');
            $table->mediumText('observacion')->nullable();

            $table->char('confor_almacen')->nullable();

            $table->enum('estado',['0','1'])->default('1');
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
        Schema::dropIfExists('lote_productos');
    }
}
