<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompraDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compra_documentos', function (Blueprint $table) {
            $table->increments('id');
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento')->nullable();
            $table->date('fecha_entrega')->nullable();
            
            $table->unsignedInteger('empresa_id');
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');

            $table->unsignedInteger('proveedor_id');
            $table->foreign('proveedor_id')->references('id')->on('proveedores')->onDelete('cascade');

            $table->BigInteger('orden_compra')->nullable();
            $table->foreignId('condicion_id')->nullable()->constrained()->onDelete('SET NULL');
            $table->string('modo_compra')->nullable();
            $table->string('numero_tipo');
            $table->string('serie_tipo');
            $table->string('numero_doc')->nullable();
            $table->string('tipo_compra');
            $table->string('tipo_pago')->nullable();
            $table->string('moneda');

            $table->string('igv_check',2)->nullable();
            $table->char('igv',3)->nullable();
            $table->unsignedDecimal('tipo_cambio', 15, 4)->nullable();
            $table->unsignedDecimal('dolar', 15, 4)->nullable();

            $table->unsignedDecimal('sub_total', 15, 4)->nullable();
            $table->unsignedDecimal('total_igv', 15, 4)->nullable();
            $table->unsignedDecimal('percepcion', 15, 4)->nullable();
            $table->unsignedDecimal('total', 15, 4)->nullable();
            $table->unsignedDecimal('total_pagar', 15, 4)->nullable();

            $table->unsignedDecimal('sub_total_soles', 15, 4)->nullable();
            $table->unsignedDecimal('total_igv_soles', 15, 4)->nullable();
            $table->unsignedDecimal('percepcion_soles', 15, 4)->nullable();
            $table->unsignedDecimal('total_soles', 15, 4)->nullable();

            $table->unsignedDecimal('sub_total_dolares', 15, 4)->nullable();
            $table->unsignedDecimal('total_igv_dolares', 15, 4)->nullable();
            $table->unsignedDecimal('percepcion_dolares', 15, 4)->nullable();
            $table->unsignedDecimal('total_dolares', 15, 4)->nullable();

            $table->mediumText('observacion')->nullable();
            $table->BigInteger('usuario_id');

            $table->enum('estado',['VIGENTE','PENDIENTE','ADELANTO','CONCRETADA','ANULADO','PAGADA'])->default('VIGENTE');

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
        Schema::dropIfExists('compra_documentos');
    }
}
