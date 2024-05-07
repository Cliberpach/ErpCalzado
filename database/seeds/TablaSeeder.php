<?php

use Illuminate\Database\Seeder;
use App\Mantenimiento\Tabla\General;

class TablaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1
        $tabla = new General();
        $tabla->descripcion = 'TIPOS DE MONEDA';
        $tabla->sigla = 'TIPOS DE MONEDA';
        $tabla->editable = '1';
        $tabla->save();

        // 2
        $tabla = new General();
        $tabla->descripcion = 'BANCOS';
        $tabla->sigla = 'BANCOS';
        $tabla->save();

        // 3
        $tabla = new General();
        $tabla->descripcion = 'TIPOS DE DOCUMENTO';
        $tabla->sigla = 'TIPOS DE DOCUMENTO';
        $tabla->save();

        // 4
        $tabla = new General();
        $tabla->descripcion = 'TIPOS DE SEXO';
        $tabla->sigla = 'SEXO';
        $tabla->save();

        // 5
        $tabla = new General();
        $tabla->descripcion = 'TIPOS DE ESTADO CIVIL';
        $tabla->sigla = 'ESTADO CIVIL';
        $tabla->save();

        // 6
        $tabla = new General();
        $tabla->descripcion = 'ZONAS';
        $tabla->sigla = 'ZONAS';
        $tabla->save();

        // 7
        $tabla = new General();
        $tabla->descripcion = 'AREAS';
        $tabla->sigla = 'AREAS';
        $tabla->save();

        // 8
        $tabla = new General();
        $tabla->descripcion = 'CARGOS';
        $tabla->sigla = 'CARGOS';
        $tabla->save();

        // 9
        $tabla = new General();
        $tabla->descripcion = 'PROFESIONES';
        $tabla->sigla = 'PROFESIONES';
        $tabla->save();

       // 10
       $tabla = new General();
       $tabla->descripcion = 'PRESENTACIONES';
       $tabla->sigla = 'PRESENTACIONES';
       $tabla->save();

       // 11
       $tabla = new General();
       $tabla->descripcion = 'TIPOS DE PERSONAS';
       $tabla->sigla = 'PERSONAS';
       $tabla->save();

       // 12
       $tabla = new General();
       $tabla->descripcion = 'GRUPOS SANGUINEOS';
       $tabla->sigla = 'GRUPOS SANGUINEOS';
       $tabla->save();

       // 13
       $tabla = new General();
       $tabla->descripcion = 'UNIDAD MEDIDA';
       $tabla->sigla = 'UNIDAD MEDIDA';
       $tabla->editable = '1';
       $tabla->save();
       //14
       $tabla = new General();
       $tabla->descripcion = 'MODO DE COMPRA';
       $tabla->sigla = 'MODO DE COMPRA';
       $tabla->save();

       //15
       $tabla = new General();
       $tabla->descripcion = 'TIPOS DE DOCUMENTOS TRIBUTARIOS';
       $tabla->sigla = 'TIPO DOCUMENTO TRIBUTARIO';
       $tabla->save();

       //16
       $tabla = new General();
       $tabla->descripcion = 'TIPOS DE DOCUMENTOS DE COMPRA';
       $tabla->sigla = 'TIPO DOCUMENTO COMPRA';
       $tabla->save();

       //17
       $tabla = new General();
       $tabla->descripcion = 'TIPO DE CLIENTE';
       $tabla->sigla = 'TIPO DE CLIENTE';
       $tabla->save();

       //18
       $tabla = new General();
       $tabla->descripcion = 'CONDICION DE REPARTO';
       $tabla->sigla = 'CONDICION DE REPARTO';
       $tabla->editable = '1';
       $tabla->save();

       //19
       $tabla = new General();
       $tabla->descripcion = 'TIPOS DE PAGO (CAJA CHICA)';
       $tabla->sigla = 'TIPO DE PAGO CAJA CHICA';
       $tabla->editable = '1';
       $tabla->save();

       //20
       $tabla = new General();
       $tabla->descripcion = 'HERRAMIENTAS DE PLANTA';
       $tabla->sigla = 'HERRAMIENTAS DE PLANTA';
       $tabla->save();

       //21
       $tabla = new General();
       $tabla->descripcion = 'TIPOS DE VENTA';
       $tabla->sigla = 'TIPO DE VENTA';
       //$tabla->editable = '1';
       $tabla->save();

       //22
       $tabla = new General();
       $tabla->descripcion = 'TIPOS DE TIENDA';
       $tabla->sigla = 'TIPO DE TIENDA';
       $tabla->save();

       //23
       $tabla = new General();
       $tabla->descripcion = 'TIPOS DE NEGOCIO';
       $tabla->sigla = 'TIPO DE NEGOCIO';
       $tabla->save();

       //24
       $tabla = new General();
       $tabla->descripcion = 'RESPONSABLE DE PAGO';
       $tabla->sigla = 'RESPONSABLE DE PAGO';
       $tabla->save();

       //25
       $tabla = new General();
       $tabla->descripcion = 'LINEA COMERCIAL';
       $tabla->sigla = 'LINEA COMERCIAL';
       $tabla->save();

       //26
       $tabla = new General();
       $tabla->descripcion = 'PLAN FATURACION ELECTRONICA';
       $tabla->sigla = 'PFE';
       $tabla->editable = '1';
       $tabla->save();

       //27
       $tabla = new General();
       $tabla->descripcion = 'AMBIENTE DE PRODUCCION FATURACION ELECTRONICA';
       $tabla->sigla = 'APFE';
       $tabla->editable = '1';
       $tabla->save();

       //28
       $tabla = new General();
       $tabla->descripcion = 'ORÍGENES';
       $tabla->sigla = 'OR';
       $tabla->save();

       //29
       $tabla = new General();
       $tabla->descripcion = 'DESTINOS';
       $tabla->sigla = 'DE';
       $tabla->save();

       //30
       $tabla = new General();
       $tabla->descripcion = 'FORMAS DE PAGO';
       $tabla->sigla = 'FP';
       $tabla->save();

       //31
       $tabla = new General();
       $tabla->descripcion = 'TURNOS';
       $tabla->sigla = 'TURNO';
       $tabla->save();

       //32
       $tabla = new General();
       $tabla->descripcion = 'CUENTAS';
       $tabla->sigla = 'CUENTAS';
       $tabla->save();

       //33
       $tabla = new General();
       $tabla->descripcion = 'TIPOS NOTA DE CREDITO';
       $tabla->sigla = 'CODMOTIVO';       
       $tabla->editable = '1';
       $tabla->save();

        //34
        $tabla = new General();
        $tabla->descripcion = 'MOTIVOS DE TRASLADO';
        $tabla->sigla = 'MTRASLADO';
        $tabla->editable = '1';
        $tabla->save();

        //35
        $tabla = new General();
        $tabla->descripcion = 'TIPOS DE ENVIO';
        $tabla->sigla = 'TIPOS DE ENVIO';
        $tabla->editable = '1';
        $tabla->save();

        //36
        $tabla = new General();
        $tabla->descripcion = 'ORIGENES VENTAS';
        $tabla->sigla = 'OV';
        $tabla->editable = '0';
        $tabla->save();

        //37
        $tabla = new General();
        $tabla->descripcion = 'TIPOS PAGO ENVIO';
        $tabla->sigla = 'TPE';
        $tabla->editable = '0';
        $tabla->save();
    }
}
