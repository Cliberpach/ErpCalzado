<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use App\Mantenimiento\Empresa\Empresa;
use App\Ventas\Cliente;
use App\Ventas\CuentaCliente;
use App\Ventas\DetalleCuentaCliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Session;

class CuentaClienteController extends Controller
{
    public function index() {
        $fecha_hoy = Carbon::now()->toDateString();
        return view('ventas.cuentaCliente.index',compact('fecha_hoy'));
    }

    public function getTable() {
        $datos = array();
        $cuentaCliente = CuentaCliente::where('estado', '!=', 'ANULADO')->get();
        foreach ($cuentaCliente as $key => $value) {
            $detalle_ultimo = DetalleCuentaCliente::where('cuenta_cliente_id',$value->id)->get()->last();

            $total_pagar = $value->documento->total - $value->documento->notas->sum("mtoImpVenta");

            $nuevo_monto = $total_pagar - $value->detalles->sum("monto");
            $detalle_ultimo->saldo = $nuevo_monto;
            $detalle_ultimo->update();

            if(!empty($detalle_ultimo))
            {
                if($detalle_ultimo->saldo == 0)
                {
                    $cuenta = CuentaCliente::find($value->id);
                    $cuenta->saldo=0;
                    $cuenta->estado='PAGADO';
                    $cuenta->update();
                }
                else{
                    $cuenta = CuentaCliente::find($value->id);
                    $cuenta->saldo=$detalle_ultimo->saldo;
                    $cuenta->estado='PENDIENTE';
                    $cuenta->update();
                }
            }

            $acta =  $value->detalles->sum("monto");
            if ($acta < $value->monto) {
                $cuenta = CuentaCliente::find($value->id);
                $cuenta->estado = 'PENDIENTE';
                $cuenta->update();
            } else {
                $cuenta = CuentaCliente::find($value->id);
                $cuenta->estado = 'PAGADO';
                $cuenta->update();
            }

            $cuenta_cliente = CuentaCliente::find($value->id);

            array_push($datos,array(
                "id"=>$cuenta_cliente->id,
                "cliente"=>$cuenta_cliente->documento->clienteEntidad->nombre,
                "numero_doc"=>$cuenta_cliente->documento->serie.' - '.$cuenta_cliente->documento->correlativo,
                "fecha_doc"=>$cuenta_cliente->fecha_doc,
                "monto"=>$cuenta_cliente->documento->total - $cuenta_cliente->documento->notas->sum("mtoImpVenta"),
                "acta"=> number_format(round($acta, 2), 2),
                "saldo"=>$cuenta_cliente->saldo,
                "estado"=>$cuenta_cliente->estado
            ));
        }
        return DataTables::of($datos)->toJson();
    }

    public function getDatos(Request $request) {

        $cuenta = CuentaCliente::findOrFail($request->id);
        return array(
            "id"=>$cuenta->id,
            "cliente"=>$cuenta->documento->clienteEntidad->nombre,
            "numero"=>$cuenta->documento->numero_doc,
            "fecha"=>$cuenta->fecha_doc,
            "monto"=>$cuenta->monto,
            "acta"=>$cuenta->acta,
            "saldo"=>$cuenta->saldo,
            "estado"=>$cuenta->estado,
            "detalle"=> $cuenta->detalles
        );
    }

    public function consulta(Request $request)
    {
        $cuentas = DB::table('cuenta_cliente')
        ->join('cotizacion_documento', 'cotizacion_documento.id', '=', 'cuenta_cliente.cotizacion_documento_id')
        ->join('clientes', 'clientes.id', '=', 'cotizacion_documento.cliente_id')
        ->when($request->get('cliente'), function ($query, $request) {
            return $query->where('clientes.id', $request);
        })
        ->when($request->get('estado'), function ($query, $request) {
            return $query->where('cuenta_cliente.estado',$request);
        })
        ->select(
            'cuenta_cliente.*',
            'clientes.nombre as cliente',
            'cotizacion_documento.numero_doc as numero_doc',
            'cotizacion_documento.created_at as fecha_doc',
            'cotizacion_documento.total as monto'
        )->get();
            return $cuentas;
    }

    public function detallePago(Request $request, $id)
    {
        DB::beginTransaction();
        $CuentaCliente = CuentaCliente::findOrFail($id);
        if($request->pago == "A CUENTA")
        {
            $detallepago = new DetalleCuentaCliente();
            $detallepago->cuenta_cliente_id = $CuentaCliente->id;
            $detallepago->mcaja_id = movimientoUser()->id;
            $detallepago->monto = $request->cantidad;
            $detallepago->importe=$request->importe_venta;
            $detallepago->efectivo=$request->efectivo_venta;
            $detallepago->tipo_pago_id=$request->modo_pago;
            $detallepago->observacion = $request->pago.' - '.$request->observacion;
            $detallepago->fecha = $request->fecha;
            $detallepago->save();

            if($CuentaCliente->saldo - $request->cantidad < 0)
            {
                DB::rollBack();
                Session::flash('error', 'Ocurrió un error, al parecer ingreso un monto superior al saldo.');
                return redirect()->route('cuentaCliente.index');
            }

            $CuentaCliente->saldo = $CuentaCliente->saldo - $request->cantidad;
            $CuentaCliente->update();

            $detallepago->saldo = $CuentaCliente->saldo;
            $detallepago->update();

            if($request->hasFile('imagen')){
                $detallepago->ruta_imagen = $request->file('imagen')->store('public/cuenta/cobrar');
                $detallepago->update();
            }

            if($CuentaCliente->saldo == 0)
            {
                $CuentaCliente->estado='PAGADO';
                $CuentaCliente->save();
            }
        }
        else{
            if($CuentaCliente->saldo != $request->cantidad)
            {
                DB::rollBack();
                Session::flash('error', 'Ocurrió un error, al parecer ingreso un monto diferente al saldo.');
                return redirect()->route('cuentaCliente.index');
            }
            $detallepago = new DetalleCuentaCliente();
            $detallepago->cuenta_cliente_id = $CuentaCliente->id;
            $detallepago->mcaja_id = movimientoUser()->id;
            $detallepago->monto = $request->cantidad;
            $detallepago->importe=$request->importe_venta;
            $detallepago->efectivo=$request->efectivo_venta;
            $detallepago->tipo_pago_id=$request->modo_pago;
            $detallepago->observacion = $request->pago.' - '.$request->observacion;
            $detallepago->fecha = $request->fecha;
            $detallepago->save();

            if($CuentaCliente->saldo - $request->cantidad < 0)
            {
                DB::rollBack();
                Session::flash('error', 'Ocurrió un error, al parecer ingreso un monto superior al saldo.');
                return redirect()->route('cuentaCliente.index');
            }

            $CuentaCliente->saldo = $CuentaCliente->saldo - $request->cantidad;
            $CuentaCliente->update();

            $detallepago->saldo = $CuentaCliente->saldo;
            $detallepago->update();

            if($request->hasFile('imagen')){
                $detallepago->ruta_imagen = $request->file('imagen')->store('public/cuenta/cobrar');
                $detallepago->update();
            }

            if($CuentaCliente->saldo == 0)
            {
                $CuentaCliente->estado='PAGADO';
                $CuentaCliente->save();
            }
        }
        /*else{
            $cliente = $CuentaCliente->documento->clienteEntidad;
            $cuentasFaltantes = CuentaCliente::where('estado','PENDIENTE')->get();
            $cantidadRecibida = $request->cantidad;
            $cantidadRecibidaEfectivo=$request->efectivo_venta;
            $cantidadRecibidaImporte=$request->importe_venta;
            foreach ($cuentasFaltantes as $key => $cuenta) {
                if($cuenta->documento->clienteEntidad->id == $cliente->id && $cantidadRecibida != 0)
                {
                    $detallepago = new DetalleCuentaCliente();
                    $detallepago->mcaja_id = movimientoUser()->id;
                    $detallepago->cuenta_cliente_id = $cuenta->id;
                    $detallepago->monto = 0;
                    $detallepago->observacion=$request->pago.' - '.$request->observacion;
                    $detallepago->fecha = $request->fecha;
                    $detallepago->tipo_pago_id=$request->modo_pago;
                    $detallepago->save();
                    if($cuenta->saldo > $cantidadRecibida)
                    {
                        if($cantidadRecibidaEfectivo == 0)
                        {
                            $detallepago->efectivo = 0;
                            $detallepago->importe = $cantidadRecibidaImporte;
                            $detallepago->monto = $cantidadRecibida;
                            $cuenta->saldo = $cuenta->saldo - $cantidadRecibida;
                            $cantidadRecibidaImporte = 0;

                            $cantidadRecibida = $cantidadRecibidaEfectivo + $cantidadRecibidaImporte;
                        }
                        else
                        {
                            $detallepago->efectivo = $cantidadRecibidaEfectivo;
                            $detallepago->importe = $cantidadRecibidaImporte;
                            $detallepago->monto = $cantidadRecibida;
                            $cuenta->saldo = $cuenta->saldo - $cantidadRecibida;
                            $cantidadRecibidaEfectivo = 0;
                            $cantidadRecibidaImporte = 0;

                            $cantidadRecibida = $cantidadRecibidaEfectivo + $cantidadRecibidaImporte;
                        }
                    }
                    else{
                        if($cantidadRecibidaEfectivo == 0)
                        {
                            $importe = 0;
                            if($cantidadRecibidaImporte > $cuenta->saldo)
                            {
                                $importe = $cantidadRecibidaImporte - $cuenta->saldo;
                            }else
                            {
                                $importe = $cantidadRecibidaImporte;
                            }

                            $detallepago->efectivo  = $cantidadRecibidaEfectivo;
                            $detallepago->importe = $cuenta->saldo;
                            $detallepago->monto = $cuenta->saldo;
                            $cantidadRecibidaImporte = $importe;
                            $cantidadRecibida = $cantidadRecibidaEfectivo + $cantidadRecibidaImporte;

                            $cuenta->update();
                        }
                        else
                        {
                            if($cantidadRecibidaImporte == 0)
                            {
                                $efectivo = 0;
                                if($cantidadRecibidaEfectivo > $cuenta->saldo)
                                {
                                    $efectivo = $cantidadRecibidaEfectivo - $cuenta->saldo;
                                }else
                                {
                                    $efectivo = $cantidadRecibidaEfectivo;
                                }
                                $detallepago->efectivo  = $efectivo;
                                $detallepago->importe = $cantidadRecibidaImporte;
                                $detallepago->monto = $cuenta->saldo;
                                $cantidadRecibidaEfectivo = $cantidadRecibidaEfectivo - $efectivo;

                                $cantidadRecibida = $cantidadRecibidaEfectivo + $cantidadRecibidaImporte;
                            }
                            else
                            {
                                $detallepago->efectivo = $cantidadRecibidaEfectivo;
                                $detallepago->importe = $cuenta->saldo - $cantidadRecibidaEfectivo;
                                $detallepago->monto = $detallepago->efectivo + $detallepago->importe;
                                $cantidadRecibidaImporte =  $cantidadRecibidaImporte - ($cuenta->saldo - $cantidadRecibidaEfectivo);
                                $cantidadRecibidaEfectivo = 0;
                                $cantidadRecibida = $cantidadRecibidaEfectivo + $cantidadRecibidaImporte;
                            }
                        }
                        $cuenta->saldo = 0;
                    }

                    $detallepago->update();
                    if($request->hasFile('imagen')){
                        $detallepago->ruta_imagen = $request->file('imagen')->store('public/cuenta/cobrar');
                        $detallepago->update();
                    }

                    $cuenta->update();

                    if($cuenta->saldo == 0)
                    {
                        $cuenta->estado='PAGADO';
                        $cuenta->update();
                    }
                }
            }

        }*/

        DB::commit();
        Session::flash('success', 'Pago agregado correctamene');
        return redirect()->route('cuentaCliente.index');
    }

    public function reporte($id)
    {
        $cuenta = CuentaCliente::findOrFail($id);
        $cliente = Cliente::find($cuenta->documento->cliente_id);
        $empresa = Empresa::first();
        $pdf = PDF::loadview('ventas.documentos.impresion.detalle_cuenta',[
            'cuenta' => $cuenta,
            'detalles' => $cuenta->detalles,
            'cliente' => $cliente,
            'empresa' => $empresa
            ])->setPaper('a4');
        return $pdf->stream('CUENTA-'.$cuenta->id.'.pdf');
    }

    public function detalle(Request $request)
    {
        $estado = $request->estado;
        $id = $request->id;
        //$cuentas = CuentaCliente::where('cliente_id',$request->id)->where('estado', $request->estado);
        $cuentas = DB::table('cuenta_cliente')
        ->join('cotizacion_documento', 'cotizacion_documento.id', '=', 'cuenta_cliente.cotizacion_documento_id')
        ->join('clientes', 'clientes.id', '=', 'cotizacion_documento.cliente_id')
        ->select(
            'cuenta_cliente.*',
        )
        ->where('cotizacion_documento.cliente_id',$id)
        ->where('cuenta_cliente.estado',$estado)
        ->get();
        $cliente = Cliente::find($request->id);
        $empresa = Empresa::first();
        $pdf = PDF::loadview('ventas.documentos.impresion.detalle_cuenta_cliente',[
            'cuentas' => $cuentas,
            'cliente' => $cliente,
            'empresa' => $empresa
            ])->setPaper('a4');
        return $pdf->stream('CUENTAS-'.$cliente->nombre_comercial.'.pdf');
    }

    public function imagen($id)
    {
        $detalle = DetalleCuentaCliente::find($id);
        $ruta = storage_path().'/app/'.$detalle->ruta_imagen;
        return response()->download($ruta);
    }
}
