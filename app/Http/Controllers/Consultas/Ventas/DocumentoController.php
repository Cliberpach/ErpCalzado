<?php

namespace App\Http\Controllers\Consultas\Ventas;

use App\Http\Controllers\Controller;
use App\Ventas\Documento\Documento;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DocumentoController extends Controller
{
    public function index()
    {
        return view('consultas.ventas.documentos.index');
    }

    public function getTable(Request $request){
        ini_set('memory_limit', '512M');
        if($request->fecha_desde && $request->fecha_hasta)
        {
            $documentos = Documento::where('estado','!=','ANULADO')->whereBetween('fecha_documento', [$request->fecha_desde, $request->fecha_hasta])->orderBy('id', 'desc')->get();
        }
        else
        {
            $documentos = Documento::where('estado','!=','ANULADO')->orderBy('id', 'desc')->get();
        }



        $coleccion = collect();
        foreach($documentos as $documento){

            $transferencia = 0.00;
            $otros = 0.00;
            $efectivo = 0.00;

            if ($documento->tipo_pago_id == 1) {
                $efectivo = $documento->importe;
            }
            else if ($documento->tipo_pago_id == 2){
                $transferencia = $documento->importe ;
                $efectivo = $documento->efectivo;
            }
            else {
                $otros = $documento->importe;
                $efectivo = $documento->efectivo;
            }

            $coleccion->push([
                'id' => $documento->id,
                'tipo_venta' => $documento->nombreTipo(),
                'tipo_venta_id' => $documento->tipo_venta,
                'forma_pago' => $documento->formaPago(),
                'cliente' => $documento->clienteEntidad->nombre,
                'tipo_pago' => $documento->tipo_pago,
                'serie' => $documento->serie,
                'correlativo' => $documento->correlativo,
                'cliente' => $documento->tipo_documento_cliente.': '.$documento->documento_cliente.' - '.$documento->cliente,
                'empresa' => $documento->empresa,
                'cotizacion_venta' =>  $documento->cotizacion_venta,
                'numero_doc' =>  $documento->serie.'-'.$documento->correlativo,
                'fecha_documento' =>  Carbon::parse($documento->fecha_documento)->format( 'd/m/Y'),
                'estado' => $documento->estado,
                'sunat' => $documento->sunat,
                'otros' => number_format($otros, 2, '.', ''),
                'efectivo' => number_format($efectivo, 2, '.', ''),
                'transferencia' => number_format($transferencia, 2, '.', ''),
                'total' => number_format($documento->total, 2, '.', ''),
            ]);
        }

        $total = $coleccion->sum('total');
        $efectivo_ = $coleccion->sum('efectivo');
        $transferencia_ = $coleccion->sum('transferencia');
        $yape_plin = $coleccion->sum('otros');

        return response()->json([
            'success' => true,
            'ventas' => $coleccion,
            'total' => $total,
            'efectivo' => $efectivo_,
            'transferencia' => $transferencia_,
            'yape_plin' => $yape_plin,
        ]);
    }
}
