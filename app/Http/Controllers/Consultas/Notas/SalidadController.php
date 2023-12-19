<?php

namespace App\Http\Controllers\Consultas\Notas;

use App\Almacenes\NotaSalidad;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalidadController extends Controller
{
    public function index()
    {
        return view('consultas.notas.salidad');
    }

    public function getTable(Request $request){

        try
        {
            if($request->fecha_desde && $request->fecha_hasta)
            {
                $data = NotaSalidad::where('estado','!=','ANULADO')->whereBetween('fecha' , [$request->fecha_desde, $request->fecha_hasta])->orderBy('id', 'desc')->get();
            }
            else
            {
                $data = NotaSalidad::where('estado','!=','ANULADO')->orderBy('id', 'desc')->get();
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
