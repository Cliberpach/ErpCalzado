<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotaCreditoComprasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nota_credito_compras', function (Blueprint $table) {
            $table->Increments('id');
            $table->unsignedInteger('documento_id');
            $table->foreign('documento_id')->references('id')->on('compra_documentos')->onDelete('cascade');

            $table->string('numDocfectado');
            $table->string('desMotivo');

            $table->string('tipoDoc');
            $table->date('fechaEmision')->nullable();
            $table->string('tipoMoneda')->default('PEN');

            //PROVEEDOR
            $table->string('cod_tipo_documento_proveedor')->nullable();
            $table->string('tipo_documento_proveedor')->nullable();
            $table->BigInteger('documento_proveedor')->nullable();
            $table->mediumText('direccion_proveedor')->nullable();
            $table->string('proveedor')->nullable();

            $table->BigInteger('correlativo')->nullable();
            $table->string('serie')->nullable();

            $table->string('ruta_comprobante_archivo')->nullable();
            $table->string('nombre_comprobante_archivo')->nullable();


            $table->unsignedDecimal('mtoOperGravadas', 15, 2);
            $table->unsignedDecimal('mtoIGV', 15, 2);
            $table->unsignedDecimal('totalImpuestos', 15, 2);
            $table->unsignedDecimal('mtoImpVenta', 15, 2);

            $table->longText('ruta_qr')->nullable();

            //LEYENDA
            $table->string('code');
            $table->mediumText('value');

            $table->enum('estado', ['ACTIVO', 'ANULADO'])->default('ACTIVO');

            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('nota_credito_compras');
    }
}
