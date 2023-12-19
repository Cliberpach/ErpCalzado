<?php

namespace App\Http\Controllers\Consultas\Cuentas;

use App\Compras\CuentaProveedor;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProveedorController extends Controller
{
    public function index()
    {
        return view('consultas.cuentas.proveedor');
    }

    public function getTable(Request $request){

        try
        {
            if($request->fecha_desde && $request->fecha_hasta)
            {
                $cuentas = CuentaProveedor::where('estado','!=','ANULADO')->whereBetween('fecha_doc' , [$request->fecha_desde, $request->fecha_hasta])->orderBy('id', 'desc')->get();
            }
            else
            {
                $cuentas = CuentaProveedor::where('estado','!=','ANULADO')->orderBy('id', 'desc')->get();
            }
            
            $coleccion = collect();

            foreach ($cuentas as $key => $value) {
                $coleccion->push([
                    "id"=>$value->id,
                    "proveedor"=>$value->documento->proveedor->descripcion,
                    "numero_doc"=>$value->documento->numero_doc,
                    "fecha_doc"=>strval($value->documento->created_at) ,
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
