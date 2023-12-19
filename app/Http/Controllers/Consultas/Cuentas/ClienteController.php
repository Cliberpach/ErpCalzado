<?php

namespace App\Http\Controllers\Consultas\Cuentas;

use App\Http\Controllers\Controller;
use App\Ventas\CuentaCliente;
use Exception;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        return view('consultas.cuentas.cliente');
    }

    public function getTable(Request $request){

        try
        {
            if($request->fecha_desde && $request->fecha_hasta)
            {
                $cuentas = CuentaCliente::where('estado','!=','ANULADO')->whereBetween('fecha_doc' , [$request->fecha_desde, $request->fecha_hasta])->orderBy('id', 'desc')->get();
            }
            else
            {
                $cuentas = CuentaCliente::where('estado','!=','ANULADO')->orderBy('id', 'desc')->get();
            }
            
            $coleccion = collect();

            foreach ($cuentas as $key => $value) {
                $coleccion->push([
                    "id"=>$value->id,
                    "cliente"=>$value->documento->clienteEntidad->nombre,
                    "numero_doc"=>$value->documento->numero_doc,
                    "fecha_doc"=>strval($value->documento->fecha_documento) ,
                    "monto"=>$value->documento->total,
                    "acta"=>$value->acta,
                    "saldo"=>$value->saldo,
                    "estado"=>$value->estado
                ]);
            }

            return response()->json([
                'success' => true,
                'cuentas' => $coleccion
            ]);
        }
        catch(Exception $e)
        {
            return response()->json([
                'success' => false,
                'mensaje' => $e->getMessage()
            ]);
        }
    }
}
