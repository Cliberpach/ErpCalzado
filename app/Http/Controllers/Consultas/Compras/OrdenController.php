<?php

namespace App\Http\Controllers\Consultas\Compras;

use App\Compras\Orden;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Compras\Detalle;

class OrdenController extends Controller
{
    public function index()
    {
        return view('consultas.compras.ordenes.index');
    }

    public function getTable(Request $request){
        
        $consulta = Orden::where('estado','!=','ANULADO');

        if($request->fecha_desde && $request->fecha_hasta)
        {
            $consulta->whereBetween('fecha_emision', [$request->fecha_desde, $request->fecha_hasta]);
        }

        if($request->proveedor_id)
        {
            $consulta->where('proveedor_id',$request->proveedor_id);
        }

        $ordenes = $consulta->orderBy('id', 'desc')->get();

        
        $coleccion = collect();
        foreach($ordenes as $orden){
            $detalles = Detalle::where('orden_id',$orden->id)->get(); 
            $subtotal = 0; 
            $igv = '';
            $tipo_moneda = '';

            foreach($detalles as $detalle){
                $subtotal = ($detalle->cantidad * $detalle->precio) + $subtotal;
            }

            foreach(tipos_moneda() as $moneda){
                if ($moneda->descripcion == $orden->moneda) {
                    $tipo_moneda= $moneda->simbolo;
                }
            }

            if (!$orden->igv) {
                $igv = $subtotal * 0.18;
                $total = $subtotal + $igv;
                $decimal_total = number_format($total, 2, '.', ''); 
            }else{
                $calcularIgv = $orden->igv/100;
                $base = $subtotal / (1 + $calcularIgv);
                $nuevo_igv = $subtotal - $base;
                $decimal_total = number_format($subtotal, 2, '.', '');
            }

            //Pagos a cuenta
            $pagos = DB::table('pagos')
            ->join('ordenes','pagos.orden_id','=','ordenes.id')
            ->select('pagos.*','ordenes.moneda as moneda_orden')
            ->where('pagos.orden_id','=',$orden->id)
            ->where('pagos.estado','!=','ANULADO')
            ->get();


            // CALCULAR ACUENTA EN MONEDA
            $acuenta = 0;
            $soles = 0;
            
            foreach($pagos as $pago){
                $acuenta = $acuenta + $pago->monto;
                if ($pago->moneda_orden == "SOLES") {
                    $soles = $soles + $pago->monto;
                }else{
                    $soles = $soles + $pago->cambio;
                }
                
            }

            //CALCULAR SALDO
            $saldo = $decimal_total - $acuenta;

            //CAMBIAR ESTADO DE LA ORDEN A PAGADA
        
            if ($saldo == 0.0) {
                $orden->estado = "PAGADA";
                $orden->update();
            }

            

            $coleccion->push([
                'id' => $orden->id,
                'proveedor' => $orden->proveedor->descripcion,
                'fecha_emision' =>  Carbon::parse($orden->fecha_emision)->format( 'd/m/Y'),
                'fecha_entrega' =>  Carbon::parse($orden->fecha_entrega)->format( 'd/m/Y'), 
                'estado' => $orden->estado,
                'total' => $tipo_moneda.' '.number_format($decimal_total, 2, '.', ''),
                'acuenta' => $tipo_moneda.' '.number_format($acuenta, 2, '.', ''),
                'acuenta_soles' => 'S/. '.number_format($soles, 2, '.', ''),
                'saldo' => $tipo_moneda.' '.number_format($saldo, 2, '.', ''),
            ]);
        }

        return response()->json([
            'success' => true,
            'ordenes' => $coleccion
        ]);
    }
}
