<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuentaProveedorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuenta_proveedor', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('compra_documento_id');
            $table->foreign('compra_documento_id')->references('id')->on('compra_documentos')->onDelete('cascade');
            $table->text('acta')->nullable();
            $table->unsignedDecimal('saldo')->nullable()->default(0.00);
            $table->unsignedDecimal('monto')->nullable()->default(0.00);
            $table->enum('estado',['PENDIENTE','PAGADO','ANULADO'])->default('PENDIENTE');
            $table->date('fecha_doc')->nullable();
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
        Schema::dropIfExists('cuenta_proveedor');
    }
}
