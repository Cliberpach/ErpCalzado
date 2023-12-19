<?php

use App\Mantenimiento\Condicion;
use Illuminate\Database\Seeder;
use App\Mantenimiento\Tabla\Detalle;
use App\Ventas\TipoPago;

class TablaDetalleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Tipo de Monedas
        $detalle = new Detalle();
        $detalle->descripcion = "SOLES";
        $detalle->simbolo = 'S/.';
        $detalle->parametro = 'PEN';
        $detalle->estado = 'ACTIVO';
        $detalle->editable = 1;
        $detalle->tabla_id = 1;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "DOLARES";
        $detalle->simbolo = '$';
        $detalle->parametro = 'USD';
        $detalle->estado = 'ACTIVO';
        $detalle->editable = 1;
        $detalle->tabla_id = 1;
        $detalle->save();

        //Bancos
        $detalle = new Detalle();
        $detalle->descripcion = "BANCO DE LA NACION";
        $detalle->simbolo = "BN";
        $detalle->estado = 'ACTIVO';
        $detalle->editable = 1;
        $detalle->tabla_id = 2;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "INTERCONTINENTAL";
        $detalle->simbolo = "INTERCONTINENTAL";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 2;
        $detalle->editable = 1;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "MI BANCO";
        $detalle->simbolo = "MI BANCO";
        $detalle->estado = 'ACTIVO';
        $detalle->editable = 1;
        $detalle->tabla_id = 2;
        $detalle->save();

        // TIPO DE DOCUMENTO
        $detalle = new Detalle();
        $detalle->descripcion = "DOCUMENTO NACIONAL DE IDENTIDAD";
        $detalle->simbolo = 'DNI';
        $detalle->estado = 'ACTIVO';
        $detalle->editable = 1;
        $detalle->parametro = 1; //TIPO DE DOCUMENTO SUNAT POR CLIENTE
        $detalle->tabla_id = 3;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "CARNET DE EXTRANJERIA";
        $detalle->simbolo = 'CARNET EXT.';
        $detalle->estado = 'ACTIVO';
        $detalle->editable = 1;
        $detalle->parametro = 4; //TIPO DE DOCUMENTO SUNAT POR CLIENTE
        $detalle->tabla_id = 3;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "REGISTRO UNICO DE CONTRIBUYENTES";
        $detalle->simbolo = 'RUC';
        $detalle->estado = 'ACTIVO';
        $detalle->editable = 1;
        $detalle->parametro = 6; //TIPO DE DOCUMENTO SUNAT POR CLIENTE
        $detalle->tabla_id = 3;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "PASAPORTE";
        $detalle->simbolo = 'PASAPORTE';
        $detalle->estado = 'ACTIVO';
        $detalle->editable = 1;
        $detalle->parametro = 7; //TIPO DE DOCUMENTO SUNAT POR CLIENTE
        $detalle->tabla_id = 3;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "PARTIDA DE NACIMIENTO";
        $detalle->parametro = 0; //TIPO DE DOCUMENTO SUNAT POR CLIENTE
        $detalle->simbolo = 'P. NAC.';
        $detalle->estado = 'ACTIVO';
        $detalle->editable = 1;
        $detalle->tabla_id = 3;
        $detalle->save();

        // SEXO
        $detalle = new Detalle();
        $detalle->descripcion = "HOMBRE";
        $detalle->simbolo = 'H';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 4;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "MUJER";
        $detalle->simbolo = 'M';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 4;
        $detalle->save();

        // ESTADO CIVIL
        $detalle = new Detalle();
        $detalle->descripcion = "SOLTERO";
        $detalle->simbolo = 'S';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 5;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "CASADO";
        $detalle->simbolo = 'C';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 5;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "DIVORCIADO";
        $detalle->simbolo = 'D';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 5;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "VIUDO";
        $detalle->simbolo = 'V';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 5;
        $detalle->save();

        // ZONAS
        $detalle = new Detalle();
        $detalle->descripcion = "NORTE";
        $detalle->simbolo = 'NORTE';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 6;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "NOROESTE";
        $detalle->simbolo = 'NOROESTE';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 6;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "CENTRO";
        $detalle->simbolo = 'CENTRO';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 6;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "SUR";
        $detalle->simbolo = 'SUR';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 6;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "SURESTE";
        $detalle->simbolo = 'SURESTE';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 6;
        $detalle->save();

        // --------------------------NO MODIFICABLE-------------------------------------------------
        // AREAS
        $detalle = new Detalle();
        $detalle->descripcion = "GERENCIA GENERAL";
        $detalle->simbolo = 'GERENCIA GENERAL';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 7;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "CONTABILIDAD";
        $detalle->simbolo = 'CONTABILIDAD';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 7;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "ALMACÉN";
        $detalle->simbolo = 'ALMACÉN';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 7;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "FÁBRICA";
        $detalle->simbolo = 'FÁBRICA';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 7;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "COMERCIAL";
        $detalle->simbolo = 'COMERCIAL';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 7;
        $detalle->save();

        //CARGOS
        $detalle = new Detalle();
        $detalle->descripcion = "GERENTE GENERAL";
        $detalle->simbolo = 'GERENTE GENERAL';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 8;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "ASISTENTE DE CONTABILIDAD";
        $detalle->simbolo = 'ASISTENTE DE CONTABILIDAD';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 8;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "ASISTENTE DE ALMACÉN";
        $detalle->simbolo = 'ASISTENTE DE ALMACÉN';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 8;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "OPERARIO DE FÁBRICA";
        $detalle->simbolo = 'OPERARIO DE FÁBRICA';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 8;
        $detalle->save();

        //PROFESIONES
        $detalle = new Detalle();
        $detalle->descripcion = "INGENIERO(A) INDUSTRIAL";
        $detalle->simbolo = 'ING. INDUSTRIAL';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 9;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "INGENIERO(A) DE SISTEMAS";
        $detalle->simbolo = 'ING. SISTEMAS';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 9;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "INGENIERO(A) AGROINDUSTRIAL";
        $detalle->simbolo = 'ING. AGROINDUSTRIAL';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 9;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "CONTADOR PÚBLICO";
        $detalle->simbolo = 'CONTADOR PÚBLICO';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 9;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "ADMINISTRADOR";
        $detalle->simbolo = 'ADMINISTRADOR';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 9;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "TÉCNICO DE MAQUINARIA";
        $detalle->simbolo = 'TÉCNICO DE MAQUINARIA';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 9;
        $detalle->save();

        //PRESENTACIONES
        $detalle = new Detalle();
        $detalle->descripcion = "PAQUETE";
        $detalle->simbolo = 'PAQ';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 10;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "UNIDAD";
        $detalle->simbolo = 'UND';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 10;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "GALON";
        $detalle->simbolo = 'G';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 10;
        $detalle->save();

        //TIPOS DE PERSONAS
        $detalle = new Detalle();
        $detalle->descripcion = "PERSONA NATURAL";
        $detalle->simbolo = 'PERSONA NATURAL';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 11;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "PERSONA JURIDICA";
        $detalle->simbolo = 'PERSONA JURIDICA';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 11;
        $detalle->save();

        // Grupos sanguíneos
        $detalle = new Detalle();
        $detalle->descripcion = "O NEGATIVO";
        $detalle->simbolo = 'O-';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 12;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "O POSITIVO";
        $detalle->simbolo = 'O+';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 12;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "A NEGATIVO";
        $detalle->simbolo = 'A-';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 12;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "A POSITIVO";
        $detalle->simbolo = 'A+';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 12;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "B NEGATIVO";
        $detalle->simbolo = 'B-';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 12;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "B POSITIVO";
        $detalle->simbolo = 'B+';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 12;
        $detalle->save();

        // UNIDAD DE MEDIDA
        $detalle = new Detalle();
        $detalle->descripcion = "BOBINAS";
        $detalle->simbolo = "4A";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "BALDE";
        $detalle->simbolo = "BJ";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "BARRILES";
        $detalle->simbolo = "BLL";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "BOLSA";
        $detalle->simbolo = "BG";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "BOTELLAS";
        $detalle->simbolo = "BO";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "CAJA";
        $detalle->simbolo = "BX";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "CARTONES";
        $detalle->simbolo = "CT";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();


        $detalle = new Detalle();
        $detalle->descripcion = "CENTIMETRO CUADRADO";
        $detalle->simbolo = "CMK";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "CENTIMETRO CUBICO";
        $detalle->simbolo = "CMQ";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "CENTIMETRO LINEAL";
        $detalle->simbolo = "CMT";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "CIENTO DE UNIDADES";
        $detalle->simbolo = "CEN";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "CILINDRO";
        $detalle->simbolo = "CY";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "CONOS";
        $detalle->simbolo = "CJ";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "DOCENA";
        $detalle->simbolo = "DZN";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "DOCENA POR 10**6";
        $detalle->simbolo = "DZP";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "FARDO";
        $detalle->simbolo = "BE";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "GALON INGLES (4,545956L)";
        $detalle->simbolo = "GLI";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "GRAMO";
        $detalle->simbolo = "GRM";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "GRUESA";
        $detalle->simbolo = "GRO";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "HECTOLITRO";
        $detalle->simbolo = "HLT";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "HOJA";
        $detalle->simbolo = "LEF";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "JUEGO";
        $detalle->simbolo = "SET";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "KILOGRAMO";
        $detalle->simbolo = "KGM";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "KILOMETRO";
        $detalle->simbolo = "KTM";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();


        $detalle = new Detalle();
        $detalle->descripcion = "KILOVATIO HORA";
        $detalle->simbolo = "KWH";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "KIT";
        $detalle->simbolo = "KIT";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "LATAS";
        $detalle->simbolo = "CA";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "LIBRAS";
        $detalle->simbolo = "LBR";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "LITRO";
        $detalle->simbolo = "LTR";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "MEGAWATT HORA";
        $detalle->simbolo = "MWH";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "MEGAWATT HORA";
        $detalle->simbolo = "MWH";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "METRO";
        $detalle->simbolo = "MTR";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "METRO CUADRADO";
        $detalle->simbolo = "MTK";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "METRO CUBICO";
        $detalle->simbolo = "MTQ";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "MILIGRAMOS";
        $detalle->simbolo = "MGM";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "MILILITRO";
        $detalle->simbolo = "MLT";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "MILIMETRO";
        $detalle->simbolo = "MMT";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "MILIMETRO CUADRADO";
        $detalle->simbolo = "MMK";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "MILIMETRO CUBICO";
        $detalle->simbolo = "MMQ";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();


        $detalle = new Detalle();
        $detalle->descripcion = "MILLARES";
        $detalle->simbolo = "MIL";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();


        $detalle = new Detalle();
        $detalle->descripcion = "MILLON DE UNIDADES";
        $detalle->simbolo = "UM";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();


        $detalle = new Detalle();
        $detalle->descripcion = "ONZAS";
        $detalle->simbolo = "ONZ";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "PALETAS";
        $detalle->simbolo = "PF";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "PAQUETE";
        $detalle->simbolo = "PK";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "PAR";
        $detalle->simbolo = "PR";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "PIES";
        $detalle->simbolo = "FOT";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "PIES CUADRADOS";
        $detalle->simbolo = "FTK";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "PIES CUBICOS";
        $detalle->simbolo = "FTQ";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "PIEZAS";
        $detalle->simbolo = "C62";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "PLIEGO";
        $detalle->simbolo = "ST";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "PULGADAS";
        $detalle->simbolo = "INH";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "RESMA";
        $detalle->simbolo = "RM";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "TAMBOR";
        $detalle->simbolo = "DR";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "TONELADA CORTA";
        $detalle->simbolo = "STN";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "TONELADA LARGA";
        $detalle->simbolo = "LTN";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "TONELADAS";
        $detalle->simbolo = "TNE";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "TUBOS";
        $detalle->simbolo = "TU";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "UNIDAD (BIENES)";
        $detalle->simbolo = "NIU";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "UNIDAD (SERVICIOS)";
        $detalle->simbolo = "ZZ";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "US GALON (3,7843 L)";
        $detalle->simbolo = "GLL";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "YARDA";
        $detalle->simbolo = "YRD";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "YARDA CUADRADA";
        $detalle->simbolo = "YDK";
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 13;
        $detalle->save();


        //MODO DE COMPRA
        $detalle = new Detalle();
        $detalle->descripcion = "CONTADO 30 DÍAS";
        $detalle->simbolo = 'CONTADO';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 14;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "CONTRATO DE CRÉDITO";
        $detalle->simbolo = 'CREDITO';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 14;
        $detalle->save();

        // TIPO DE DOCUMENTO TRIBUTARIOS
        $detalle = new Detalle();
        $detalle->descripcion = "FACTURA";
        $detalle->simbolo = 'FAC';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 15;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "BOLETA";
        $detalle->simbolo = 'BOL';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 15;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "RECIBO";
        $detalle->simbolo = 'REC';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 15;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "NOTA DE CRÉDITO";
        $detalle->simbolo = 'NC';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 15;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "NOTA DE DÉBITO";
        $detalle->simbolo = 'ND';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 15;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "GUÍA DE REMISIÓN";
        $detalle->simbolo = 'GR';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 15;
        $detalle->save();

        // TIPO DE DOCUMENTO COMPRA
        $detalle = new Detalle();
        $detalle->descripcion = "FACTURA";
        $detalle->simbolo = 'FAC';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 16;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "BOLETA";
        $detalle->simbolo = 'BOL';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 16;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "RECIBO";
        $detalle->simbolo = 'REC';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 16;
        $detalle->save();

        //TIPO DE CLIENTE
        $detalle = new Detalle();
        $detalle->descripcion = "NORMAL";
        $detalle->simbolo = 'NORMAL';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 17;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "DISTRIBUIDOR"; // REVISAR PRODUCTO EL TIPO DE CLIENTE DISTRIBUIDOR CAMBIAR NOMBRE
        $detalle->simbolo = 'DISTRIBUIDOR';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 17;
        $detalle->save();

         //TIPO DE CONDICION DE REPARTO
         $detalle = new Detalle();
         $detalle->descripcion = "OFICINA";
         $detalle->simbolo = 'OFICINA';
         $detalle->estado = 'ACTIVO';
         $detalle->editable = 1;
         $detalle->tabla_id = 18;
         $detalle->save();

         $detalle = new Detalle();
         $detalle->descripcion = "DOMICILIO";
         $detalle->simbolo = 'DOMICILIO';
         $detalle->estado = 'ACTIVO';
         $detalle->editable = 1;
         $detalle->tabla_id = 18;
         $detalle->save();


        //TIPO DE PAGO CAJA CHICA
        $detalle = new Detalle();
        $detalle->descripcion = "EFECTIVO";
        $detalle->simbolo = 'EFEC.';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 19;
        $detalle->editable = 1;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "DEPOSITO";
        $detalle->simbolo = 'DEP.';
        $detalle->estado = 'ACTIVO';
        $detalle->editable = 1;
        $detalle->tabla_id = 19;
        $detalle->save();

        //TIPO DE DOCUMENTO (VENTA)
        $detalle = new Detalle();
        $detalle->descripcion = "FACTURA ELECTRÓNICA";
        $detalle->nombre = "FACTURA ELECTRÓNICA";
        $detalle->simbolo = '01';
        $detalle->parametro = 'F';
        $detalle->operacion = '0101';
        $detalle->estado = 'ACTIVO';
        $detalle->tipo = 'VENTA';
        $detalle->tabla_id = 21;
        $detalle->editable = 1;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "BOLETA ELECTRÓNICA";
        $detalle->nombre = "BOLETA ELECTRÓNICA";
        $detalle->simbolo = '03';
        $detalle->parametro = 'B';
        $detalle->operacion = '0101';
        $detalle->estado = 'ACTIVO';
        $detalle->tipo = 'VENTA';
        $detalle->tabla_id = 21;
        $detalle->editable = 1;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "NOTA DE VENTA";
        $detalle->nombre = "NOTA DE VENTA";
        $detalle->simbolo = '04';
        $detalle->parametro = 'N';
        $detalle->operacion = '0101';
        $detalle->estado = 'ACTIVO';
        $detalle->tipo = 'VENTA';
        $detalle->tabla_id = 21;
        $detalle->editable = 1;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "NOTA DE CRÉDITO";
        $detalle->simbolo = '07';
        $detalle->parametro = 'FF';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 21;
        $detalle->editable = 1;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "NOTA DE DÉBITO";
        $detalle->simbolo = '08';
        $detalle->parametro = 'FF';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 21;
        $detalle->editable = 1;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "GUIA DE REMISIÓN REMITENTE ELECTRÓNICA";
        $detalle->nombre = "GUIA DE REMISIÓN";
        $detalle->simbolo = '09';
        $detalle->parametro = 'T';
        $detalle->estado = 'ACTIVO';
        $detalle->editable = 1;
        $detalle->tabla_id = 21;
        $detalle->save();

        // TIPO DE TIENDA

        $detalle = new Detalle();
        $detalle->descripcion = "FISICA";
        $detalle->simbolo = 'FISICA';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 22;
        $detalle->editable = 1;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "VIRTUAL";
        $detalle->simbolo = 'VIRTUAL';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 22;
        $detalle->editable = 1;
        $detalle->save();

        // TIPO DE NEGOCIO

        $detalle = new Detalle();
        $detalle->descripcion = "MARKET";
        $detalle->simbolo = 'MARKET';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 23;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "CASA ORGANICA";
        $detalle->simbolo = 'CASA ORGANICA';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 23;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "BIOTIENDA";
        $detalle->simbolo = 'BIOTIENDA';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 23;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "TIENDA NATURISTA";
        $detalle->simbolo = 'T. NATURAL';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 23;
        $detalle->save();

        // Responsable de Pago
        $detalle = new Detalle();
        $detalle->descripcion = "CANCELA SOCIO";
        $detalle->simbolo = 'CANCELA SOCIO';
        $detalle->estado = 'ACTIVO';
        $detalle->editable = 1;
        $detalle->tabla_id = 24;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "CANCELA ECOVALLE";
        $detalle->simbolo = 'CANCELA ECOVALLE';
        $detalle->estado = 'ACTIVO';
        $detalle->editable = 1;
        $detalle->tabla_id = 24;
        $detalle->save();

        //Linea Comercial
        $detalle = new Detalle();
        $detalle->descripcion = "BELLEZA";
        $detalle->simbolo = 'BELLEZA';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 25;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "CONTROL DE PESO";
        $detalle->simbolo = 'CONTROL DE PESO';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 25;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "ENERGIZANTES";
        $detalle->simbolo = 'ENERGIZANTES';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 25;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "NUTRICIÓN";
        $detalle->simbolo = 'NUTRICIÓN';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 25;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "SISTEMA CIRCULATORIO";
        $detalle->simbolo = 'SIST. CIRCULATORIO';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 25;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "SISTEMA DIGESTIVO";
        $detalle->simbolo = 'SIST. DIGESTIVO';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 25;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "SISTEMA ESTRUCTURAL";
        $detalle->simbolo = 'SIST. ESTRUCTURAL';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 25;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "SISTEMA GLANDULAR";
        $detalle->simbolo = 'SIST. GLANDULAR';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 25;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "SISTEMA INMUNOLÓGICO";
        $detalle->simbolo = 'SIST. INMUNOLÓGICO';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 25;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "SISTEMA NERVIOSO";
        $detalle->simbolo = 'SIST. NERVIOSO';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 25;
        $detalle->save();


        $detalle = new Detalle();
        $detalle->descripcion = "SISTEMA RESPIRATORIO";
        $detalle->simbolo = 'SIST. RESPIRATORIO';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 25;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "SISTEMA URINARIO";
        $detalle->simbolo = 'SIST. URINARIO';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 25;
        $detalle->save();

        //Plan Facturacion electronica
        $detalle = new Detalle();
        $detalle->descripcion = "free";
        $detalle->simbolo = 'free';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 26;
        $detalle->editable = 1;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "premium";
        $detalle->simbolo = 'premium';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 26;
        $detalle->editable = 1;
        $detalle->save();

        //Ambiente de produccion facturacion electronica
        $detalle = new Detalle();
        $detalle->descripcion = "beta";
        $detalle->simbolo = 'beta';
        $detalle->estado = 'ACTIVO';
        $detalle->editable = 1;
        $detalle->tabla_id = 27;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "produccion";
        $detalle->simbolo = 'produccion';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 27;
        $detalle->editable = 1;
        $detalle->save();

        //Origenes
        $detalle = new Detalle();
        $detalle->descripcion = "IMPORT EXCEL";
        $detalle->simbolo = 'OE';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 28;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "PROVEEDOR";
        $detalle->simbolo = 'OP';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 28;
        $detalle->save();

        //Destinos
        $detalle = new Detalle();
        $detalle->descripcion = "CLIENTE";
        $detalle->simbolo = 'OP';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 29;
        $detalle->save();

        //Formas de Pago
        $detalle = new Detalle();
        $detalle->descripcion = "CONTADO";
        $detalle->simbolo = 'Contado';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 30;
        $detalle->editable = 1;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "CRÉDITO";
        $detalle->simbolo = 'Credito';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 30;
        $detalle->editable = 1;
        $detalle->save();


        //Turnos
        $detalle = new Detalle();
        $detalle->descripcion = "MAÑANA";
        $detalle->simbolo = 'AM';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 31;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "TARDE";
        $detalle->simbolo = 'PM';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 31;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "NOCHE";
        $detalle->simbolo = 'PM';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 31;
        $detalle->save();

        //CUENTAS
        $detalle = new Detalle();
        $detalle->descripcion = "DEVOLUCION";
        $detalle->simbolo = 'DEV';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 32;
        $detalle->editable = 1;
        $detalle->save();

        //Tipo nota de credito
        //TIPO DE DOCUMENTO (VENTA)
        $detalle = new Detalle();
        $detalle->descripcion = "Anulación de operación";
        $detalle->nombre = "Anulación de operación";
        $detalle->simbolo = '01';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 33;
        $detalle->editable = 1;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "Anulación por error en el RUC";
        $detalle->nombre = "Anulación por error en el RUC";
        $detalle->simbolo = '02';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 33;
        $detalle->editable = 1;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "Correción por error en la descripcion";
        $detalle->nombre = "Correción por error en la descripcion";
        $detalle->simbolo = '03';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 33;
        $detalle->editable = 1;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "Descuento global";
        $detalle->nombre = "Descuento global";
        $detalle->simbolo = '04';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 33;
        $detalle->editable = 1;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "Descuento por item";
        $detalle->nombre = "Descuento por item";
        $detalle->simbolo = '05';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 33;
        $detalle->editable = 1;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "Descuento por item";
        $detalle->nombre = "Descuento por item";
        $detalle->simbolo = '06';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 33;
        $detalle->editable = 1;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "Devolución por item";
        $detalle->nombre = "Devolución por item";
        $detalle->simbolo = '07';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 33;
        $detalle->editable = 1;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "Bonificación";
        $detalle->nombre = "Bonificacion";
        $detalle->simbolo = '08';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 33;
        $detalle->editable = 1;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "Disminución en el valor";
        $detalle->nombre = "Disminución en el valor";
        $detalle->simbolo = '09';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 33;
        $detalle->editable = 1;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "Otros conceptos";
        $detalle->nombre = "Otros conceptos";
        $detalle->simbolo = '10';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 33;
        $detalle->editable = 1;
        $detalle->save();

        // GUIA DE REMISION
        // MOTIVOS DE TRASLADO
        $detalle = new Detalle();
        $detalle->descripcion = "VENTA";
        $detalle->nombre = "VENTA";
        $detalle->simbolo = '01';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 34;
        $detalle->editable = 1;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "VENTA SUJETA A CONFIRMACION DEL COMPRADOR";
        $detalle->nombre = "VENTA SUJETA A CONFIRMACION DEL COMPRADOR";
        $detalle->simbolo = '14';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 34;
        $detalle->editable = 1;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "COMPRA";
        $detalle->nombre = "COMPRA";
        $detalle->simbolo = '02';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 34;
        $detalle->editable = 1;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "TRASLADO ENTRE ESTABLECIMIENTOS DE LA MISMA EMPRESA";
        $detalle->nombre = "TRASLADO ENTRE ESTABLECIMIENTOS DE LA MISMA EMPRESA";
        $detalle->simbolo = '04';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 34;
        $detalle->editable = 1;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "TRASLADO EMISOR ITINERANTE CP";
        $detalle->nombre = "TRASLADO EMISOR ITINERANTE CP";
        $detalle->simbolo = '18';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 34;
        $detalle->editable = 1;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "IMPORTACION";
        $detalle->nombre = "IMPORTACION";
        $detalle->simbolo = '08';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 34;
        $detalle->editable = 1;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "EXPORTACION";
        $detalle->nombre = "EXPORTACION";
        $detalle->simbolo = '09';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 34;
        $detalle->editable = 1;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "TRASLADO A ZONA PRIMARIA";
        $detalle->nombre = "TRASLADO A ZONA PRIMARIA";
        $detalle->simbolo = '19';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 34;
        $detalle->editable = 1;
        $detalle->save();

        $detalle = new Detalle();
        $detalle->descripcion = "OTROS";
        $detalle->nombre = "OTROS";
        $detalle->simbolo = '13';
        $detalle->estado = 'ACTIVO';
        $detalle->tabla_id = 34;
        $detalle->editable = 1;
        $detalle->save();

        //---------TIPO PAGO-----------

        $tipo = new TipoPago();
        $tipo->descripcion = "EFECTIVO";
        $tipo->simbolo = 'EFECTIVO';
        $tipo->estado = 'ACTIVO';
        $tipo->editable = 1;
        $tipo->save();

        $tipo = new TipoPago();
        $tipo->descripcion = "TRANSFERENCIA";
        $tipo->simbolo = "TRANSFERENCIA";
        $tipo->estado = 'ACTIVO';
        $tipo->editable = 1;
        $tipo->save();

        $tipo = new TipoPago();
        $tipo->descripcion = "YAPE/PLIN";
        $tipo->simbolo = "YAPE/PLIN";
        $tipo->estado = 'ACTIVO';
        $tipo->editable = 1;
        $tipo->save();

        //----------CONDICION------------
        $condicion = new Condicion();
        $condicion->descripcion = 'CONTADO';
        $condicion->slug = 'Contado';
        $condicion->tabladetalle_id = 160;
        $condicion->dias = 0;
        $condicion->save();

        $condicion = new Condicion();
        $condicion->descripcion = 'CRÉDITO';
        $condicion->slug = 'Credito';
        $condicion->tabladetalle_id = 161;
        $condicion->dias = 10;
        $condicion->save();

    }
}
