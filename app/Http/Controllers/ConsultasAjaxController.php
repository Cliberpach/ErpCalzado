<?php

namespace App\Http\Controllers;
use Exception;
use Illuminate\Http\Request;

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
}
