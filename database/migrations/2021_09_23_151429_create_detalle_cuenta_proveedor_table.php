<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleCuentaProveedorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_cuenta_proveedor', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('cuenta_proveedor_id');
            $table->foreign('cuenta_proveedor_id')->references('id')->on('cuenta_proveedor')->onDelete('cascade');
            $table->foreignId('mcaja_id')->references('id')->on('movimiento_caja')->onDelete('cascade');
            $table->date('fecha');
            $table->text('observacion');
            $table->text('ruta_imagen')->nullable();
            $table->unsignedInteger('tipo_pago_id');
            $table->unsignedDecimal('monto', 15, 2);
            $table->foreign('tipo_pago_id')->references('id')->on('tipos_pago')->onDelete('cascade');
            $table->unsignedDecimal('efectivo', 15, 2)->nullable()->default(0.00);
            $table->unsignedDecimal('importe', 15, 2)->nullable()->default(0.00);
            $table->unsignedDecimal('saldo')->nullable();
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
        Schema::dropIfExists('detalle_cuenta_proveedor');
    }
}
