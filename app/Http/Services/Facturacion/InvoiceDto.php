<?php

namespace App\Http\Services\Facturacion;

use App\Greenter\Utils\Util;
use App\Ventas\Cliente;
use App\Ventas\Documento\Detalle;
use App\Ventas\Documento\Documento;
use Exception;
use Greenter\Ws\Services\SunatEndpoints;
use Illuminate\Support\Facades\DB;

class InvoiceDto
{

    public function getDtoSunat(int $id):array
    {
        //====== OBTENER EL DOCUMENTO DE VENTA =========
        $documento      =   Documento::findOrFail($id);
        $lst_items      =   Detalle::where('documento_id', $id)->whereNull('anticipo_id')->get();
        $lst_anticipos  =   Detalle::where('documento_id', $id)->whereNotNull('anticipo_id')->get();
        $codigos        =   $this->getCodigos($documento);
        $clienteBD      =   Cliente::find($documento->cliente_id);
        $codigos        =   $this->getCodigos($documento);

        $dto                    =   [];
        $dto['documento']       =   $documento;
        $dto['lst_items']       =   $lst_items;
        $dto['lst_anticipos']   =   $lst_anticipos;
        $dto['codigos']         =   $codigos;
        $dto['cliente_bd']      =   $clienteBD;
        $dto['codigos']         =   $codigos;

        return $dto;
    }

    public function getCodigos(Documento $documento): array
    {
        $tipo_documento_cliente =   null;
        $tipo_doc_facturacion   =   null;

        if ($documento->tipo_venta_id   ==  127) {   //====== FACTURA ====
            $tipo_documento_cliente =   '6';    //======= RUC ====
            $tipo_doc_facturacion   =   '01';
        }
        if ($documento->tipo_venta_id   ==  128) {   //======== BOLETA =====
            $tipo_documento_cliente =   '1';    //====== DNI ======
            $tipo_doc_facturacion   =   '03';
        }

        return ['tipo_documento_cliente'=>$tipo_documento_cliente,'tipo_doc_facturacion'=>$tipo_doc_facturacion];
    }

    public function controlConfiguracionGreenter(Util $util)
    {
        //==== OBTENIENDO CONFIGURACIÓN DE GREENTER ======
        $greenter_config    =   DB::select('SELECT
                                gc.ruta_certificado,
                                gc.id_api_guia_remision,
                                gc.modo,
                                gc.clave_api_guia_remision,
                                e.ruc,
                                e.razon_social,
                                e.direccion_fiscal,
                                e.ubigeo,
                                e.direccion_llegada,
                                gc.sol_user,
                                gc.sol_pass
                                FROM greenter_config AS gc
                                INNER JOIN empresas AS e ON e.id=gc.empresa_id
                                INNER JOIN configuracion AS c ON c.propiedad = gc.modo
                                WHERE gc.empresa_id=1 AND c.slug="AG"');


        if (count($greenter_config) === 0) {
            throw new Exception('NO SE ENCONTRÓ NINGUNA CONFIGURACIÓN PARA GREENTER');
        }

        if (!$greenter_config[0]->sol_user) {
            throw new Exception('DEBE ESTABLECER LA CREDENCIAL SOL_USER');
        }
        if (!$greenter_config[0]->sol_pass) {
            throw new Exception('DEBE ESTABLECER LA CREDENCIAL SOL_PASS');
        }
        if ($greenter_config[0]->modo !== "BETA" && $greenter_config[0]->modo !== "PRODUCCION") {
            throw new Exception('NO SE HA CONFIGURADO EL AMBIENTE BETA O PRODUCCIÓN PARA GREENTER');
        }

        $see    =   null;
        if ($greenter_config[0]->modo === "BETA") {
            //===== MODO BETA ======
            $see = $util->getSee(SunatEndpoints::FE_BETA, $greenter_config[0]);
        }

        if ($greenter_config[0]->modo === "PRODUCCION") {
            //===== MODO PRODUCCION ======
            $see = $util->getSee(SunatEndpoints::FE_PRODUCCION, $greenter_config[0]);
        }

        if (!$see) {
            throw new Exception('ERROR EN LA CONFIGURACIÓN DE GREENTER, SEE ES NULO');
        }

        return $see;
    }

}
