<?php

namespace App\Http\Controllers;

use App\Ventas\Documento\Documento;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class BuscarController extends Controller
{
    public function index()
    {
        $hoy = Carbon::now()->toDateString();
        return view('buscar',compact('hoy'));
    }

    public function getDocumento(Request $request)
    {
        try
        {
            $tipo_documento = $request->tipo_documento;
            $fecha_emision = $request->fecha_emision;
            $serie = $request->serie;
            $correlativo = $request->correlativo;
            $documento = $request->documento;
            $total = $request->total;

            $comprobantes = Documento::where('tipo_venta_id',$tipo_documento)->where('fecha_documento',$fecha_emision)->where('serie',$serie)->where('correlativo',$correlativo)->where('documento_cliente',$documento)->where('total',$total)->where('estado','!=','ANULADO')->get();

            if(count($comprobantes) > 0)
            {
                foreach($comprobantes as $item)
                {
                    $item['numero_doc'] = $item->serie.'-'.$item->correlativo;
                }
                return response()->json([
                    'success' => true,
                    'mensaje' => 'Documento encontrado',
                    'comprobantes' => $comprobantes,
                    'data' => $request->all(),
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'Documento no encontrado',
                    'data' => $request->all(),
                ]);
            }
        }
        catch(Exception $e)
        {
            return response()->json([
                'success' => false,
                'mensaje' => $e->getMessage(),
                'data' => $request->all(),
            ]);
        }
    }
}
