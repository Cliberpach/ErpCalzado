<?php

namespace App\Http\Controllers\Consultas\Compras;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Compras\Documento\Detalle;
use App\Compras\Documento\Documento;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpParser\Comment\Doc;

class DocumentoController extends Controller
{
    public function index()
    {
        return view('consultas.compras.documentos.index');
    }

    public function getTable(Request $request){

        if($request->fecha_desde && $request->fecha_hasta)
        {
            $documentos = Documento::where('estado','!=','ANULADO')->whereBetween('fecha_emision', [$request->fecha_desde, $request->fecha_hasta])->orderBy('id', 'desc')->get();
        }
        else
        {
            $documentos = Documento::where('estado','!=','ANULADO')->orderBy('id', 'desc')->get();
        }
        

        
        $coleccion = collect();
        foreach($documentos as $documento){
            $detalles = Detalle::where('documento_id',$documento->id)->get();
            $documento = Documento::findOrFail($documento->id);
            $subtotal = 0;
            $igv = '';
            $tipo_moneda = '';
            foreach($detalles as $detalle){
                $subtotal = ($detalle->cantidad * $detalle->precio) + $subtotal;
            }
            foreach(tipos_moneda() as $moneda){
                if ($moneda->descripcion == $documento->moneda) {
                    $tipo_moneda= $moneda->simbolo;
                }
            }
            if (!$documento->igv) {
                $igv = $subtotal * 0.18;
                $total = $subtotal + $igv;
                $decimal_total = number_format($total, 2, '.', '');
            }else{
                $calcularIgv = $documento->igv/100;
                $base = $subtotal / (1 + $calcularIgv);
                $nuevo_igv = $subtotal - $base;
                $decimal_total = number_format($subtotal, 2, '.', '');
            }
            
            $coleccion->push([
                'id' => $documento->id,
                'tipo' => $documento->tipo_compra,
                'tipo_pago' => $documento->tipo_pago,
                'proveedor' => $documento->proveedor->descripcion,
                'empresa' => $documento->empresa->razon_social,
                'fecha_emision' =>  Carbon::parse($documento->fecha_emision)->format( 'd/m/Y'),
                'igv' =>  $documento->igv,
                'orden_compra' =>  $documento->orden_compra,
                'subtotal' => $tipo_moneda.' '.number_format($subtotal, 2, '.', ''),
                // 'fecha_entrega' =>  Carbon::parse($documento->fecha_entrega)->format( 'd/m/Y'),
                'estado' => $documento->estado,
               // 'otros' => $tipo_moneda.' '.number_format($otros, 2, '.', ''),
                'condicion' => $documento->condicion->descripcion,
                'total' => $tipo_moneda.' '.number_format($decimal_total, 2, '.', ''),
            ]);
        }

        return response()->json([
            'success' => true,
            'documentos' => $coleccion
        ]);
    }
}
