<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotaCreditoComprasDetalleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nota_credito_compras_detalle', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('nota_id');
            $table->foreign('nota_id')->references('id')->on('nota_credito_compras')->onDelete('cascade');

            $table->string('codProducto');
            $table->string('unidad');
            $table->longText('descripcion');
            $table->unsignedDecimal('cantidad', 15, 2);
            $table->unsignedInteger('detalle_id')->nullable();
            $table->unsignedInteger('producto_id')->nullable();

            $table->unsignedDecimal('mtoBaseIgv', 15, 2)->nullable();
            $table->unsignedDecimal('porcentajeIgv', 15, 2)->nullable();
            $table->unsignedDecimal('igv', 15, 2)->nullable();
            $table->unsignedDecimal('tipAfeIgv', 15, 2)->nullable();

            $table->unsignedDecimal('totalImpuestos', 15, 2)->nullable();
            $table->unsignedDecimal('mtoValorVenta', 15, 2)->nullable();
            $table->unsignedDecimal('mtoValorUnitario', 15, 2)->nullable();
            $table->unsignedDecimal('mtoPrecioUnitario', 15, 2)->nullable();
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
        Schema::dropIfExists('nota_credito_compras_detalle');
    }
}
