<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('almacen_id');
            $table->foreign('almacen_id')->references('id')->on('almacenes');

            $table->unsignedBigInteger('sede_id');
            $table->foreign('sede_id')->references('id')->on('empresa_sedes');

            $table->unsignedInteger('cliente_id');
            $table->string('cliente_nombre');

            $table->unsignedInteger('empresa_id');
            $table->string('empresa_nombre');

            $table->unsignedInteger('user_id');
            $table->string('user_nombre');

            $table->unsignedBigInteger('condicion_id');

            $table->string('moneda');
            $table->bigInteger('pedido_nro');

            $table->unsignedDecimal('sub_total', 15, 2);
            $table->unsignedDecimal('total', 15, 2);
            $table->unsignedDecimal('total_igv', 15, 2);
            $table->unsignedDecimal('total_pagar', 15, 2);
            $table->unsignedDecimal('monto_embalaje',15,2)->nullable();
            $table->unsignedDecimal('monto_envio',15,2)->nullable();

            $table->unsignedDecimal('porcentaje_descuento', 15, 2)->nullable();
            $table->unsignedDecimal('monto_descuento', 15, 2)->nullable();

            $table->date('fecha_registro');

            $table->unsignedInteger('cotizacion_id')->nullable();
            $table->foreign('cotizacion_id')->references('id')->on('cotizaciones')->onDelete('cascade');
            $table->date('fecha_propuesta')->nullable();
            $table->string('cliente_telefono',20)->nullable();
            $table->string('facturado',2)->nullable();
            $table->unsignedDecimal('monto_facturado',15,2)->nullable();
            $table->unsignedDecimal('saldo_facturado',15,2)->nullable();

            $table->unsignedInteger('documento_venta_facturacion_id ');
            $table->foreign('documento_venta_facturacion_id ')->references('id')->on('cotizacion_documento');
            
            $table->string('documento_venta_facturacion_serie',100)->nullable();
            $table->string('documento_venta_facturacion_correlativo',100)->nullable();
            
            $table->enum('estado', ['PENDIENTE', 'ATENDIENDO','FINALIZADO','ANULADO'])->default('PENDIENTE');
            $table->timestamps();

            $table->foreign('cliente_id')->references('id')->on('clientes');
            $table->foreign('empresa_id')->references('id')->on('empresas');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('condicion_id')->references('id')->on('condicions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pedidos');
    }
}
