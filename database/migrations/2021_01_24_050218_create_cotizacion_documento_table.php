<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCotizacionDocumentoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cotizacion_documento', function (Blueprint $table) {
            
            $table->Increments('id');

            //EMPRESA
            $table->BigInteger('ruc_empresa');
            $table->string('empresa');
            $table->mediumText('direccion_fiscal_empresa');
            $table->unsignedInteger('empresa_id'); //OBTENER NUMERACION DE LA EMPRESA
            
            $table->unsignedInteger('almacen_id');
            $table->foreign('almacen_id')->references('id')->on('almacenes');

            $table->unsignedBigInteger('sede_id');
            $table->foreign('sede_id')->references('id')->on('empresa_sedes');
            
            $table->string('almacen_nombre',160);

            //CLIENTE
            $table->string('tipo_documento_cliente');
            $table->BigInteger('documento_cliente');
            $table->mediumText('direccion_cliente');
            $table->string('cliente');
            $table->unsignedInteger('cliente_id'); //OBTENER TIENDAS DEL CLIENTE

            $table->unsignedBigInteger('pedido_id')->nullable();
            $table->foreign('pedido_id')->references('id')->on('pedidos')->onDelete('cascade');

            $table->enum('tipo_doc_venta_pedido', ['ATENCION', 'FACTURACION'])->nullable();


            $table->date('fecha_documento');
            $table->date('fecha_vencimiento');
            $table->date('fecha_atencion')->nullable();

            $table->string('tipo_venta_id');
            $table->string('tipo_venta_nombre',160);


            $table->unsignedDecimal('sub_total', 15, 2);
            $table->unsignedDecimal('monto_embalaje',15,2)->nullable();
            $table->unsignedDecimal('monto_envio',15,2)->nullable();
            $table->unsignedDecimal('total', 15, 2);
            $table->unsignedDecimal('total_igv', 15, 2);
            $table->unsignedDecimal('total_pagar', 15, 2);

            $table->unsignedDecimal('porcentaje_descuento', 15, 2)->nullable();
            $table->unsignedDecimal('monto_descuento', 15, 2)->nullable();


            $table->unsignedInteger('tipo_pago_id')->nullable();
            $table->foreign('tipo_pago_id')->references('id')->on('tipos_pago')->onDelete('cascade');
            $table->unsignedDecimal('efectivo', 15, 2)->nullable()->default(0.00);
            $table->unsignedDecimal('importe', 15, 2)->nullable()->default(0.00);

            $table->foreignId('condicion_id')->nullable()->constrained()->onDelete('SET NULL');
            $table->longText('ruta_xml')->nullable();
            $table->longText('ruta_qr')->nullable();
            $table->longText('hash')->nullable();

            $table->longText('ruta_pago')->nullable();
            $table->longText('ruta_pago_2')->nullable();

            $table->unsignedInteger('banco_empresa_id')->unsigned()->nullable();
            $table->foreign('banco_empresa_id')
                  ->references('id')->on('banco_empresas')
                  ->onDelete('SET NULL');

            $table->string('igv_check',2)->nullable();
            $table->unsignedDecimal('igv',15,4)->nullable();
            $table->string('moneda');

            $table->string('numero_doc')->nullable();

            $table->BigInteger('cotizacion_venta')->nullable();

            
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->mediumText('observacion')->nullable();
            $table->enum('estado',['ACTIVO','ANULADO'])->default('ACTIVO');
            $table->enum('estado_pago',['PAGADA','PENDIENTE','ADELANTO','CONCRETADA','VIGENTE','DEVUELTO'])->default('PENDIENTE');

            $table->longText('legenda');
            
            $table->enum('sunat',['0','1','2'])->default('0');

            $table->json('getCdrResponse')->nullable();
            $table->json('getRegularizeResponse')->nullable();
            $table->enum('regularize',['0','1'])->default('0');


            $table->unsignedBigInteger('correlativo');
            $table->string('serie',20);

            $table->string('ruta_comprobante_archivo')->nullable();
            $table->string('nombre_comprobante_archivo')->nullable();

            $table->BigInteger('convertir')->nullable();

            $table->enum('contingencia', ['0', '1'])->default('0');
            $table->BigInteger('correlativo_contingencia')->nullable();
            $table->string('serie_contingencia')->nullable();
            $table->enum('sunat_contingencia', ['0', '1', '2'])->default('0');
            $table->json('getCdrResponse_contingencia')->nullable();
            $table->json('getRegularizeResponse_contingencia')->nullable();

            $table->string('cambio_talla',1)->nullable();


            $table->string('cdr_response_description')->nullable();
            $table->string('cdr_response_code')->nullable();
            $table->string('cdr_response_id')->nullable();
            $table->string('response_error_message')->nullable();
            $table->string('response_error_code')->nullable();
            $table->longText('ruta_cdr')->nullable();
            $table->longText('cdr_response_notes')->nullable();
            $table->longText('cdr_response_reference')->nullable();

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
        Schema::dropIfExists('cotizacion_documento');
    }
}
