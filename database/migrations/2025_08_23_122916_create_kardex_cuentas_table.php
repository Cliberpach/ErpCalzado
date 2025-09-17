<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKardexCuentasTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kardex_cuentas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('cuenta_bancaria_id');
            $table->foreign('cuenta_bancaria_id')->references('id')->on('cuentas');

            $table->unsignedInteger('venta_id')->nullable();
            $table->foreign('venta_id')->references('id')->on('cotizacion_documento');

            $table->unsignedBigInteger('egreso_id')->nullable();
            $table->foreign('egreso_id')->references('id')->on('egreso');

            $table->unsignedBigInteger('pago_cliente_id')->nullable();
            $table->foreign('pago_cliente_id')->references('id')->on('detalle_cuenta_cliente');

            $table->unsignedBigInteger('pago_proveedor_id')->nullable();
            $table->foreign('pago_proveedor_id')->references('id')->on('detalle_cuenta_proveedor');

            $table->unsignedInteger('registrador_id')->comment('USUARIO QUE REGISTRA');
            $table->foreign('registrador_id')->references('id')->on('users');
            $table->string('registrador_nombre', 200);

            $table->unsignedInteger('metodo_pago_id')->nullable();
            $table->foreign('metodo_pago_id')->references('id')->on('tipos_pago');
            $table->string('metodo_pago_nombre', 160);

            $table->dateTime('fecha_registro');
            $table->string('documento', 160);
            $table->string('banco_abreviatura');
            $table->string('nro_cuenta', 30);

            $table->decimal('monto', 20, 4)->unsigned();

            $table->enum('tipo_documento', ['VENTA','EGRESO', 'COBRANZA', 'PAGO PROVEEDOR']);
            $table->enum('tipo_operacion', ['INGRESO', 'EGRESO']);

            $table->enum('estado', ['ACTIVO', 'ANULADO'])->default('ACTIVO');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kardex_cuentas');
    }
}
