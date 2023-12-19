<?php

namespace App\Http\Controllers\Egreso;

use App\Http\Controllers\Controller;
use App\Mantenimiento\Empresa\Empresa;
use App\Pos\DetalleMovimientoEgresosCaja;
use App\Pos\Egreso;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade as PDF;

class EgresoController extends Controller
{
    public function index()
    {
        return view('Egreso.index');
    }
    public function getEgresos()
    {
        $datos = Egreso::where('estado', 'ACTIVO')->get();
        $data = array();
        foreach ($datos as $key => $value) {
            array_push($data, array(
                'id' => $value->id,
                'descripcion' => $value->descripcion,
                'monto' => $value->importe + $value->efectivo,
                'estado' => $value->estado,
                'usuario' => $value->usuario,
                'created_at' => Carbon::createFromFormat('Y-m-d H:i:s', $value->created_at)->format('Y-m-d h:i:s'),
                'tipoDocumento' => $value->tipoDocumento->descripcion,
                'documento' => $value->documento == null ? "-" : $value->documento
            ));
        }
        return DataTables::of($data)->toJson();
    }
    public function store(Request $request)
    {
        $egreso = new Egreso();
        $egreso->tipodocumento_id = 120;
        $egreso->cuenta_id = $request->cuenta;
        $egreso->documento = $request->documento;
        $egreso->descripcion = $request->descripcion;
        $egreso->monto = $request->monto;
        $egreso->importe = $request->importe;
        $egreso->efectivo = $request->efectivo;
        $egreso->tipo_pago_id = $request->modo_pago;
        $egreso->usuario = Auth()->user()->usuario;
        $egreso->user_id = auth()->user()->id;
        $egreso->save();
        $detalleMovimientoEgreso = new DetalleMovimientoEgresosCaja();
        $detalleMovimientoEgreso->mcaja_id = movimientoUser()->id;
        $detalleMovimientoEgreso->egreso_id = $egreso->id;
        $detalleMovimientoEgreso->save();


        return redirect()->route('Egreso.index');
    }
    public function update(Request $request, $id)
    {
        $egreso = Egreso::findOrFail($id);
        $egreso->cuenta_id = $request->cuenta_editar;
        $egreso->documento = $request->documento_editar;
        $egreso->descripcion = $request->descripcion_editar;
        $egreso->monto = $request->monto_editar;
        $egreso->importe = $request->importe_editar;
        $egreso->efectivo = $request->efectivo_editar;
        $egreso->tipo_pago_id = $request->modo_pago_editar;
        $egreso->save();
        return redirect()->route('Egreso.index');
    }
    public function getEgreso(Request $request)
    {
        return Egreso::findOrFail($request->id);
    }
    public function destroy($id)
    {
        $egreso = Egreso::findOrFail($id);
        $egreso->estado = "ANULADO";
        $egreso->save();
        return redirect()->route('Egreso.index');
    }
    public function recibo(Request $request, $size)
    {
        $egreso = Egreso::findOrFail($request->egreso_id);
        $empresa = Empresa::first();
        if ($size == 80) {
            $pdf = PDF::loadView('Egreso.Imprimir.ticket', compact('egreso','empresa'));
            $pdf->setpaper([0, 0, 226.772, 651.95]);
        }
        else{
            $pdf = PDF::loadView('Egreso.Imprimir.normal', compact('egreso','empresa'));
        }
        return $pdf->stream('recibo.pdf');
    }
}
