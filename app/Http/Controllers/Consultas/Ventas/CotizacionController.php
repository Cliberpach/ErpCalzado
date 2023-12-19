<?php

namespace App\Http\Controllers\Consultas\Ventas;

use App\Http\Controllers\Controller;
use App\Ventas\Cotizacion;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CotizacionController extends Controller
{
    public function index()
    {
        return view('consultas.ventas.cotizaciones.index');
    }

    public function getTable(Request $request){

        $consulta = Cotizacion::where('estado','!=','ANULADO');

        if($request->fecha_desde && $request->fecha_hasta)
        {
            $consulta->whereBetween('fecha_documento', [$request->fecha_desde, $request->fecha_hasta]);
        }

        if($request->cliente_id)
        {
            $consulta->where('cliente_id',$request->cliente_id);
        }
        
        $cotizaciones = $consulta->orderBy('id', 'desc')->get();
        
        $coleccion = collect();
        foreach($cotizaciones as $cotizacion){
            $coleccion->push([
                'id' => $cotizacion->id,
                'empresa' => $cotizacion->empresa->razon_social,
                'cliente' => $cotizacion->cliente->nombre,
                'fecha_documento' => Carbon::parse($cotizacion->fecha_documento)->format( 'd/m/Y'),
                'total' => $cotizacion->total,
                'estado' => $cotizacion->estado
            ]);
        }

        return response()->json([
            'success' => true,
            'cotizaciones' => $coleccion
        ]);
    }
}
