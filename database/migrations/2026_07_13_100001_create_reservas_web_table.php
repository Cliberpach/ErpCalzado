<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fase F del carrito (ecommerceMerris,
 * docs/PLANIFICATIONS/2026-07-11-carrito-plan.md §7): un pedido online
 * confirmado en checkout crea una reserva acá y separa stock al instante.
 * Un usuario de ErpCalzado valida el pago manualmente (fuera del sistema)
 * y confirma o anula — no hay expiración automática, decisión de negocio.
 */
class CreateReservasWebTable extends Migration
{
    public function up()
    {
        Schema::create('reservas_web', function (Blueprint $table) {
            $table->id();

            $table->string('codigo_pedido_ecommerce')->unique();

            $table->string('cliente_nombre');
            $table->string('cliente_email');
            $table->string('cliente_telefono')->nullable();
            $table->text('cliente_direccion')->nullable();

            $table->unsignedBigInteger('almacen_id')->default(1);
            $table->decimal('total', 10, 2);

            $table->enum('estado', ['PENDIENTE', 'CONFIRMADO', 'ANULADO'])->default('PENDIENTE');

            $table->timestamp('fecha_reserva')->useCurrent();
            $table->timestamp('fecha_resolucion')->nullable();
            $table->unsignedBigInteger('usuario_id')->nullable()->comment('Quién confirmó/anuló');
            $table->string('motivo_anulacion')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reservas_web');
    }
}
