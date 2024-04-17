<?php

namespace App\Http\Controllers\Consultas\Kardex;

use App\Almacenes\Kardex;
use App\Http\Controllers\Controller;
use App\Compras\Documento\Detalle;
use App\Compras\Documento\Documento;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index()
    {
        return view('consultas.kardex.proveedor');
    }

    public function getTable(Request $request){

        $consulta = Documento::where('estado','!=','ANULADO');

        if($request->fecha_desde && $request->fecha_hasta)
        {
            $consulta->whereBetween('fecha_emision', [$request->fecha_desde, $request->fecha_hasta]);
        }

        if($request->proveedor_id)
        {
            $consulta->where('proveedor_id',$request->proveedor_id);
        }

        $documentos = $consulta->orderBy('id', 'desc')->get();

        $coleccion = collect();
        foreach($documentos as $documento){
            $detalles = Detalle::where('estado','ACTIVO')->where('documento_id',$documento->id)->get();
            foreach($detalles as $detalle)
            {
                $coleccion->push([
                    'numero_doc' => $documento->serie_tipo.'-'.$documento->numero_tipo,
                    'fecha' => $documento->fecha_emision,
                    'proveedor' => $documento->proveedor->descripcion,
                    'codigo' => $detalle->producto_codigo,
                    'cantidad' => $detalle->cantidad,
                    'producto' => $detalle->producto_nombre,
                    'color' => $detalle->color_nombre,
                    'talla' => $detalle->talla_nombre,
                    'costo' => $detalle->producto->precio_compra?$detalle->producto->precio_compra:0.0,
                    'precio' => $detalle->precio_mas_igv_soles,
                    'importe' => number_format($detalle->cantidad * $detalle->precio_mas_igv_soles , 2)
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'kardex' => $coleccion,
        ]);
    }
}
