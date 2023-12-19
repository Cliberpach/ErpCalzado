<?php

namespace App\Http\Controllers\Ventas\Documentos;

use App\Http\Controllers\Controller;
use App\Ventas\Documento\Documento;
use App\Ventas\Documento\Pago\Transferencia;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class TransferenciaController extends Controller
{
    public function index($id)
    {
        $documento = Documento::findOrFail($id);

        $pagos = DB::table('cotizacion_documento_pago_transferencias')
        ->select('cotizacion_documento_pago_transferencias.*')
        ->where('cotizacion_documento_pago_transferencias.documento_id','=',$id)
        ->where('cotizacion_documento_pago_transferencias.estado','!=','ANULADO')
        ->sum('cotizacion_documento_pago_transferencias.monto');
        
        $montoRestate = $documento->total - $pagos;

        // MONTOS ACUENTA EN LA MONEDA DE LA ORDEN
        $acuenta = $pagos;
        $montoTotal = $documento->total; 

        if ($acuenta == $montoTotal) {
            $documento->estado = "PAGADA";
            $documento->update();
        }else{
            $documento->estado = "PENDIENTE";
            $documento->update();
        }
        
        return view('ventas.documentos.transferencia.index',[
            'documento' => $documento,
            'monto' => $montoTotal,
            'saldo' => number_format($montoRestate, 2, '.', ''),
            'acuenta' =>  number_format($acuenta, 2, '.', ''),
        ]);
    }

    public function getPay($id)
    {
 
        $pagos = DB::table('cotizacion_documento_pago_transferencias')
        ->join('banco_empresas','cotizacion_documento_pago_transferencias.id_banco_empresa','=','banco_empresas.id')
        ->select('cotizacion_documento_pago_transferencias.*','banco_empresas.descripcion as banco')
        ->where('cotizacion_documento_pago_transferencias.documento_id','=',$id)
        ->where('cotizacion_documento_pago_transferencias.estado','!=','ANULADO')
        ->get();

    

        $coleccion = collect([]);

            foreach($pagos as $pago){

                if ($pago->estado == "ACTIVO") {
                    $coleccion->push([
                        'id' => $pago->id,
                        'fecha_pago' => Carbon::parse($pago->fecha_pago)->format( 'd/m/Y'),
                        'entidad'=> $pago->banco,
                        'moneda' => "SOLES",
                        'monto' => 'S/. '.number_format($pago->monto,2,'.',''),
                        ]);
                }
    

            }
            
        
        return DataTables::of($coleccion)->toJson();   
    }
    public function create($id)
    {

        $documento = Documento::findOrFail($id);
        $fecha_hoy = Carbon::now()->toDateString();
        $bancos_empresa = collect([]);
        
        foreach($documento->empresaEntidad->bancos as $moneda_bancos){
            if ($moneda_bancos->tipo_moneda == "SOLES") {
                $bancos_empresa->push([
                    'id' => $moneda_bancos->id,
                    'descripcion'=> $moneda_bancos->descripcion,
                    'tipo_moneda' => $moneda_bancos->tipo_moneda,
                    'num_cuenta'=> $moneda_bancos->num_cuenta,
                    'cci'=> $moneda_bancos->cci,
                    'estado'=> $moneda_bancos->estado,
                ]);
            }

        }

        $pagos = DB::table('cotizacion_documento_pago_transferencias')
        ->select('cotizacion_documento_pago_transferencias.*')
        ->where('cotizacion_documento_pago_transferencias.documento_id','=',$id)
        ->where('cotizacion_documento_pago_transferencias.estado','!=','ANULADO')
        ->sum('cotizacion_documento_pago_transferencias.monto');
        
        $montoRestate = $documento->total - $pagos;
        
        return view('ventas.documentos.transferencia.create',[
            'documento' => $documento,
            'fecha_hoy' => $fecha_hoy,
            'bancos_empresa' => $bancos_empresa,
            'monto' =>  number_format($montoRestate, 2, '.', '')
        ]);
    }

    public function store(Request $request)
    {
 
        $data = $request->all();
        $rules = [
  
            'id_entidad_empresa' => 'required',
            'id_documento' => 'required',
            'archivo' => 'required|mimetypes:application/pdf,image/jpeg,image/png,image/jpg|max:40000',
            'fecha_pago' => 'required',
            'monto' => 'required|numeric',
            'observacion' => 'nullable',

        ];
        
        $message = [
            'id_entidad_empresa.required' => 'Seleccionar una entidad bancaria.',
            'archivo.mimetypes' => 'El campo Archivo no contiene el formato correcto.',
            'archivo.required' => 'El campo Archivo es obligatorio.',
            'archivo.max' => 'El tamaño máximo del Archivo para cargar es de 40 MB.',

            'fecha_pago.required'=> 'El campo Fecha de Pago es obligatorio.',

            'monto.numeric'=> 'El campo Monto debe se numérico.',
            'monto.required'=> 'El campo Monto es obligatorio.',
            

        ];

        Validator::make($data, $rules, $message)->validate();

        $pago = new Transferencia();
        $pago->documento_id = $request->get('id_documento');
        $pago->id_banco_empresa = $request->get('id_entidad_empresa');

        $pago->fecha_pago = Carbon::createFromFormat('d/m/Y', $request->get('fecha_pago'))->format('Y-m-d');
        $pago->monto =  $request->get('monto');

        $pago->observacion =  $request->get('observacion');

        $documento = Documento::findOrFail($request->get('id_documento'));
        $documento->tipo_pago =  "1";
        $documento->update();

        if($request->hasFile('archivo')){                
            $file = $request->file('archivo');
            $name = $file->getClientOriginalName();
            $pago->nombre_archivo = $name;
            $pago->ruta_archivo = $request->file('archivo')->store('public/ventas/documentos/pagos');
        }
        $pago->save();

        
        //Registro de actividad
        $descripcion = "SE AGREGÓ EL PAGO DEL DOCUMENTO DE VENTA (TRANSFERENCIA) CON EL MONTO: ".  $pago->monto ;
        $gestion = "DOCUMENTO DE VENTA - PAGO TRANSFERENCIA";
        eliminarRegistro($pago, $descripcion , $gestion);

        Session::flash('success','Pago creado.');
        return redirect()->route('ventas.documentos.transferencia.pago.index',$request->get('id_documento'))->with('guardar', 'success');

    }


    public function destroy(Request $request)
    {        
        $pago = Transferencia::findOrFail($request->get('pago'));
        $pago->estado = 'ANULADO';
        $pago->update();

        //Registro de actividad
        $descripcion = "SE ELIMINÓ EL PAGO DEL DOCUMENTO DE VENTA (TRANSFERENCIA) CON EL MONTO: ".  $pago->monto ;
        $gestion = "DOCUMENTO DE VENTA - PAGO TRANSFERENCIA";
        eliminarRegistro($pago, $descripcion , $gestion);

        Session::flash('success','Pago eliminado.');
        return redirect()->route('ventas.documentos.transferencia.pago.index', $request->get('amp;documento'))->with('eliminar', 'success');

    }

    public function show(Request $request)
    {                

        $pago = DB::table('cotizacion_documento_pago_transferencias')
        ->join('banco_empresas','cotizacion_documento_pago_transferencias.id_banco_empresa','=','banco_empresas.id')
        ->select('cotizacion_documento_pago_transferencias.*','banco_empresas.*')
        ->where('cotizacion_documento_pago_transferencias.id','=',$request->get('pago'))
        ->where('cotizacion_documento_pago_transferencias.estado','!=','ANULADO')
        ->get();

        // dd($pago[0]);

        $documento = Documento::findOrFail($request->get('documento') );
        return view('ventas.documentos.transferencia.show',[
            'pago' => $pago,
            'documento' => $documento,
        ]);

    }

}