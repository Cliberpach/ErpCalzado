<?php

namespace App\Http\Controllers\Consultas\Notas;

use App\Almacenes\NotaIngreso;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IngresoController extends Controller
{
    public function index()
    {
        return view('consultas.notas.ingreso');
    }

    public function getTable(Request $request){

        try
        {
            if($request->fecha_desde && $request->fecha_hasta)
            {
                $data = NotaIngreso::where('estado','!=','ANULADO')->whereBetween('fecha' , [$request->fecha_desde, $request->fecha_hasta])->orderBy('id', 'desc')->get();
            }
            else
            {
                $data = NotaIngreso::where('estado','!=','ANULADO')->orderBy('id', 'desc')->get();
            }

            return response()->json([
                'success' => true,
                'notas' => $data
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
