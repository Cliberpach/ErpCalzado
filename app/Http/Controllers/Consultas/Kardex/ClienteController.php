<?php

namespace App\Http\Controllers\Consultas\Kardex;

use App\Almacenes\Kardex;
use App\Http\Controllers\Controller;
use App\Ventas\Documento\Detalle;
use App\Ventas\Documento\Documento;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        return view('consultas.kardex.cliente');
    }

    public function getTable(Request $request){

        $consulta = Documento::where('estado','!=','ANULADO')->where('sunat','!=','2');
        
        if($request->fecha_desde && $request->fecha_hasta)
        {
            $consulta->whereBetween('fecha_documento', [$request->fecha_desde, $request->fecha_hasta]);
        }

        if($request->cliente_id)
        {
            $consulta->where('cliente_id',$request->cliente_id);
        }

        $documentos = $consulta->orderBy('id', 'desc')->get();

        $coleccion = collect();
        foreach($documentos as $documento){
            $detalles = Detalle::where('estado','ACTIVO')->where('documento_id',$documento->id)->get();
            foreach($detalles as $detalle)
            {
                $coleccion->push([
                    'numero_doc' => $documento->serie.'-'.$documento->correlativo,
                    'fecha' => $documento->fecha_documento,
                    'cliente' => $documento->clienteEntidad->nombre,
                    'codigo' => $detalle->codigo_producto,
                    'cantidad' => $detalle->cantidad,
                    'talla' => $detalle->nombre_talla,
                    'color' => $detalle->nombre_color,
                    'producto' => $detalle->nombre_producto,
                    'costo' => $detalle->producto->precio_compra?$detalle->producto->precio_compra:0.0,
                    'precio' => $detalle->precio_unitario_nuevo,
                    'importe' => number_format($detalle->importe_nuevo, 2)
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'kardex' => $coleccion,
        ]);
    }
}
