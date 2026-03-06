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

            $table->string('telefono', 20)->nullable();

            //EMPRESA
            $table->BigInteger('ruc_empresa');
            $table->string('empresa');
            $table->mediumText('direccion_fiscal_empresa');
            $table->unsignedInteger('empresa_id'); //OBTENER NUMERACION DE LA EMPRESA

            $table->unsignedInteger('almacen_id');
            $table->foreign('almacen_id')->references('id')->on('almacenes');

            $table->unsignedBigInteger('sede_id');
            $table->foreign('sede_id')->references('id')->on('empresa_sedes');

            $table->string('almacen_nombre', 160);

            //CLIENTE
            $table->string('tipo_documento_cliente');
            $table->string('documento_cliente', 25);
            $table->mediumText('direccion_cliente');
            $table->string('cliente');
            $table->unsignedInteger('cliente_id'); //OBTENER TIENDAS DEL CLIENTE

            $table->unsignedBigInteger('pedido_id')->nullable();

            $table->enum('tipo_doc_venta_pedido', ['ATENCION', 'FACTURACION'])->nullable();

            $table->date('fecha_documento');
            $table->date('fecha_vencimiento');
            $table->date('fecha_atencion')->nullable();

            $table->string('tipo_venta_id');
            $table->string('tipo_venta_nombre', 160);


            $table->unsignedDecimal('sub_total', 15, 6);
            $table->unsignedDecimal('monto_embalaje', 15, 6)->nullable();
            $table->unsignedDecimal('monto_envio', 15, 6)->nullable();
            $table->unsignedDecimal('total', 15, 6);
            $table->unsignedDecimal('total_igv', 15, 6);
            $table->unsignedDecimal('total_pagar', 15, 6);

            $table->unsignedDecimal('porcentaje_descuento', 15, 6)->nullable();
            $table->unsignedDecimal('monto_descuento', 15, 6)->nullable();


            $table->unsignedInteger('tipo_pago_id')->nullable();
            $table->foreign('tipo_pago_id')->references('id')->on('tipos_pago')->onDelete('cascade');
            $table->unsignedDecimal('efectivo', 15, 6)->nullable()->default(0);
            $table->unsignedDecimal('importe', 15, 6)->nullable()->default(0);

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

            $table->string('igv_check', 2)->nullable();
            $table->unsignedDecimal('igv', 15, 6)->nullable();
            $table->string('moneda');

            $table->string('numero_doc')->nullable();

            $table->BigInteger('cotizacion_venta')->nullable();


            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->mediumText('observacion')->nullable();
            $table->enum('estado', ['ACTIVO', 'ANULADO'])->default('ACTIVO');
            $table->enum('estado_pago', ['PAGADA', 'PENDIENTE', 'ADELANTO', 'CONCRETADA', 'VIGENTE', 'DEVUELTO'])->default('PENDIENTE');

            $table->longText('legenda');

            $table->enum('sunat', ['0', '1', '2'])->default('0');

            $table->json('getCdrResponse')->nullable();
            $table->json('getRegularizeResponse')->nullable();
            $table->enum('regularize', ['0', '1'])->default('0');


            $table->unsignedBigInteger('correlativo');
            $table->string('serie', 20);

            $table->string('ruta_comprobante_archivo')->nullable();
            $table->string('nombre_comprobante_archivo')->nullable();

            $table->BigInteger('convertir')->nullable();

            $table->enum('contingencia', ['0', '1'])->default('0');
            $table->BigInteger('correlativo_contingencia')->nullable();
            $table->string('serie_contingencia')->nullable();
            $table->enum('sunat_contingencia', ['0', '1', '2'])->default('0');

            $table->string('cambio_talla', 1)->nullable();
            $table->string('registrador_nombre', 200)->nullable();

            //===== ANTICIPO ======
            $table->boolean('es_anticipo')->default(0)->nullable();
            $table->unsignedDecimal('saldo_anticipo', 15, 6)->nullable()->default(0.000000);
            $table->unsignedInteger('anticipo_consumido_id')->nullable();
            $table->unsignedDecimal('anticipo_monto_consumido', 15, 6)->nullable()->default(0.000000);
            $table->decimal('anticipo_monto_consumido_sin_igv', 15, 6)->nullable()->default(0.000000);
            $table->string('anticipo_consumido_serie', 20)->nullable();
            $table->unsignedBigInteger('anticipo_consumido_correlativo')->nullable();
            $table->string('anticipo_tipo_venta_id', 20)->nullable();
            $table->decimal('total_anticipos_sunat', 15, 6)->nullable()->default(0.000000);
            $table->decimal('descuento_global_sunat', 15, 6)->nullable()->default(0.000000);

            //========= MONTOS SUNAT =========
            $table->unsignedDecimal('mto_oper_gravadas_sunat', 15, 6)->nullable();
            $table->unsignedDecimal('mto_igv_sunat', 15, 6)->nullable();
            $table->unsignedDecimal('total_impuestos_sunat', 15, 6)->nullable();
            $table->unsignedDecimal('valor_venta_sunat', 15, 6)->nullable();
            $table->unsignedDecimal('sub_total_sunat', 15, 6)->nullable();
            $table->unsignedDecimal('mto_imp_venta_sunat', 15, 6)->nullable();

            //============ CAJA - DESPACHO =========
            $table->unsignedBigInteger('despacho_id')->nullable();
            $table->unsignedBigInteger('caja_id')->nullable();
            $table->unsignedBigInteger('caja_movimiento_id')->nullable();
            $table->string('caja_nombre', 160)->nullable();
            $table->string('condicion_pago_nombre', 200)->nullable();
            $table->enum('estado_despacho', ['PENDIENTE', 'EMBALADO', 'DESPACHADO', 'S/D'])
                ->default('S/D');

            //============ PAGO 1 ============
            $table->unsignedBigInteger('pago_1_cuenta_id')->nullable();
            $table->string('pago_1_banco_nombre', 160)->nullable();
            $table->string('pago_1_nro_cuenta', 100)->nullable();
            $table->string('pago_1_cci', 100)->nullable();
            $table->string('pago_1_celular', 20)->nullable();
            $table->string('pago_1_titular', 200)->nullable();
            $table->string('pago_1_moneda', 160)->nullable();
            $table->date('pago_1_fecha_operacion')->nullable();
            $table->time('pago_1_hora_operacion')->nullable();
            $table->string('pago_1_tipo_pago_nombre', 160)->nullable();
            $table->string('pago_1_nro_operacion', 30)->nullable();
            $table->unsignedDecimal('pago_1_monto', 15, 6)->nullable();
            $table->unsignedInteger('pago_1_tipo_pago_id')->nullable();

            //=========== CONVERSION /REGULARIZACION / GUIAS
            $table->unsignedInteger('resumen_id')->nullable();
            $table->unsignedInteger('convert_de_id')->nullable();
            $table->string('convert_de_serie', 160)->nullable();
            $table->unsignedInteger('convert_en_id')->nullable();
            $table->string('convert_en_serie', 160)->nullable();
            $table->unsignedInteger('guia_id')->nullable();
            $table->unsignedInteger('regularizado_en_id')->nullable();
            $table->unsignedInteger('regularizado_de_id')->nullable();
            $table->string('regularizado_en_serie', 160)->nullable();
            $table->string('regularizado_de_serie', 160)->nullable();
            $table->enum('modo', ['CONSUMO', 'VENTA', 'ATENCION', 'RESERVA'])
                ->default('VENTA');

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
