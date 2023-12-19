<?php

namespace App\Http\Controllers\Consultas\Caja;

use App\Http\Controllers\Controller;
use App\Ventas\Documento\Detalle;
use App\Ventas\Documento\Documento;
use App\Ventas\NotaDetalle;
use Exception;
use Illuminate\Http\Request;

class UtilidadController extends Controller
{
    public function index()
    {
        return view('consultas.caja.utilidad');
    }

    public function getTable(Request $request){
        try
        {
            if($request->fecha_desde && $request->fecha_hasta)
            {
                $ventas = Documento::where('estado','!=','ANULADO')->whereBetween('fecha_documento' , [$request->fecha_desde, $request->fecha_hasta])->orderBy('id', 'desc')->get();
            }
            else
            {
                $ventas = Documento::where('estado','!=','ANULADO')->orderBy('id', 'desc')->get();
            }

            $coleccion = collect();

            foreach ($ventas as $venta) {
                $detalles = Detalle::where('documento_id',$venta->id)->get();
                foreach($detalles as $detalle)
                {
                    $precom = $detalle->lote->detalle_compra ? ($detalle->lote->detalle_compra->precio_soles + ($detalle->lote->detalle_compra->costo_flete_soles / $detalle->lote->detalle_compra->cantidad)) : $detalle->lote->detalle_nota->costo_soles;
                    $utilidad =  number_format(($detalle->precio_nuevo - $precom),2);

                    $coleccion->push([
                        "fecha_doc" => $venta->fecha_documento,
                        "cantidad" => $detalle->cantidad,
                        "producto" => $detalle->lote->producto->nombre,
                        "precio_venta" => number_format($detalle->precio_nuevo,2),
                        "precio_compra" => number_format($precom,2),
                        "utilidad" =>$utilidad,
                        "importe" => number_format(($detalle->cantidad) * $utilidad,2),
                        "valorVenta"=>$detalle->valor_venta
                    ]);

                    $notaDetalle = NotaDetalle::where("detalle_id",$detalle->id)->get();
                    foreach($notaDetalle as $nota){
                        $precom_nota = $nota->detalle->lote->detalle_compra ? ($nota->detalle->lote->detalle_compra->precio_soles + ($nota->detalle->lote->detalle_compra->costo_flete_soles / $nota->detalle->lote->detalle_compra->cantidad)) : $nota->detalle->lote->detalle_nota->costo_soles;
                        $utilidad_nota =  number_format(($nota->mtoPrecioUnitario - $precom_nota),2);

                        $coleccion->push([
                            "fecha_doc" => $nota->nota_dev->fechaEmision,
                            "cantidad" => $nota->cantidad,
                            "producto" => $nota->detalle->lote->producto->nombre,
                            "precio_venta" => number_format($nota->mtoPrecioUnitario,2),
                            "precio_compra" => number_format($precom_nota,2),
                            "utilidad" =>$utilidad_nota,
                            "importe" => "-".number_format(($nota->cantidad) * $utilidad_nota,2),
                            "valorVenta"=>number_format(($nota->cantidad) * $nota->mtoPrecioUnitario,2)
                        ]);
                    }
                }

                $deta = Detalle::where('documento_id',$venta->id)->get();
               
            }

            return response()->json([
                'success' => true,
                'ventas' => $coleccion
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
