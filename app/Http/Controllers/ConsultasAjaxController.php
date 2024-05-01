<?php

namespace App\Http\Controllers;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsultasAjaxController extends Controller
{
    public function getTipoDocumentos(){
        $tipos_documento = tipos_documento();
        return response()->json($tipos_documento);
    }
    public function tipoClientes(){
        $tipo_clientes = tipo_clientes();
        return response()->json($tipo_clientes);
    }

    public function getDepartamentos(){
        $datos = departamentos();
        return response()->json($datos);
    }
    public function getCodigoPrecioMenor(){
        $datos = codigoPrecioMenor();
        return $datos;
    }

    public function getTipoEnvios(){
        $tipos_envio    =   DB::select('select td.id,td.descripcion from tablas as t 
        inner join tabladetalles as td  on t.id=td.tabla_id
        where t.id=35');

        return response()->json($tipos_envio);
    }

    public function getEmpresasEnvio($tipo_envio){
        try {
            $empresas_envio     =   DB::select(' select * from empresas_envio as ee
                                    where ee.tipo_envio=?',[$tipo_envio]); 

            return response()->json(['success'=>true,'empresas_envio'=>$empresas_envio]);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>"ERROR EN EL SERVIDOR",'exception'=>$th->getMessage()]);
        }
    }

}
