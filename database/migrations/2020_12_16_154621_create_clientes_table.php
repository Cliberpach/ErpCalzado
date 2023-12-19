<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->Increments('id');
            $table->string('tipo_documento');
            $table->string('documento', 25);
            $table->mediumText('nombre')->nullable();
            $table->string('nombre_comercial')->nullable();
            $table->string('codigo')->nullable();
            $table->unsignedInteger('tabladetalles_id')->unsigned()->nullable();
            $table->foreign('tabladetalles_id')->references('id')->on('tabladetalles')->onDelete('cascade');
            $table->char('departamento_id', 2)->nullable();
            $table->foreign('departamento_id')->references('id')->on('departamentos')->onDelete('cascade');
            $table->char('provincia_id', 4)->nullable();
            $table->foreign('provincia_id')->references('id')->on('provincias')->onDelete('cascade');
            $table->char('distrito_id', 6)->nullable();
            $table->foreign('distrito_id')->references('id')->on('distritos')->onDelete('cascade');
            $table->string('direccion')->nullable();
            $table->string('zona')->nullable();
            $table->string('correo_electronico')->nullable();
            $table->string('telefono_movil')->nullable();
            $table->string('telefono_fijo')->nullable();
            $table->string('moneda_credito')->nullable();
            $table->unsignedDecimal('limite_credito', 15,2)->nullable();
            $table->string('nombre_contacto')->nullable();
            $table->string('telefono_contacto')->nullable();
            $table->string('correo_electronico_contacto')->nullable();

            $table->string('direccion_negocio')->nullable();
            $table->date('fecha_aniversario')->nullable();
            $table->text('observaciones')->nullable();

            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('web')->nullable();

            $table->string('hora_inicio')->nullable();
            $table->string('hora_termino')->nullable();

            $table->string('nombre_propietario')->nullable();
            $table->string('direccion_propietario')->nullable();
            $table->date('fecha_nacimiento_prop')->nullable();
            $table->string('celular_propietario')->nullable();
            $table->string('correo_propietario')->nullable();

            //CRM
            $table->enum('crm',['0','1'])->default('0');

            $table->enum('agente_retencion', ['0', '1'])->default('0');
            $table->unsignedDecimal('tasa_retencion', 8, 2)->nullable();
            $table->unsignedDecimal('monto_mayor', 8, 2)->nullable();

            $table->string('activo')->default('SIN VERIFICAR');
            $table->enum('estado',['ACTIVO','ANULADO'])->default('ACTIVO');
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
        Schema::dropIfExists('clientes');
    }
}
