<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use App\Ventas\Documento\Documento;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class CajaController extends Controller
{
    public function index()
    {
        $this->authorize('haveaccess','ventascaja.index');
        return view('ventas.caja.index');
    }

    public function getDocument(){

        $fecha_hoy = Carbon::now()->toDateString();
        $documentos = Documento::where('estado','!=','ANULADO')->whereBetween('fecha_documento', [$fecha_hoy, $fecha_hoy])->orderBy('id', 'desc')->get();
        $coleccion = collect([]);

        $hoy = Carbon::now();
        foreach($documentos as $documento){

            $transferencia = 0.00;
            $otros = 0.00;
            $efectivo = 0.00;

            if($documento->tipo_pago_id)
            {
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
            }

            $fecha_v = $documento->created_at;
            $diff =  $fecha_v->diffInDays($hoy);

            $cantidad_notas = count($documento->notas);

            $code = '-';
            if(!empty($documento->getRegularizeResponse))
            {
                $json_data = json_decode($documento->getRegularizeResponse, false);
                $code = $json_data->code;
            }

            $coleccion->push([
                'id' => $documento->id,
                'tipo_venta' => $documento->nombreTipo(),
                'tipo_venta_id' => $documento->tipo_venta,
                'empresa' => $documento->empresaEntidad->razon_social,
                'tipo_pago_id' => $documento->tipo_pago_id,
                'tipo_pago' => $documento->tipo_pago ? $documento->tipo_pago->descripcion : null,
                'ruta_pago_2'   =>  $documento->ruta_pago_2,
                'ruta_pago' => $documento->ruta_pago,
                'banco_empresa_id' => $documento->banco_empresa_id,
                'banco_empresa' => $documento->bancoPagado ? $documento->bancoPagado->descripcion : null,
                'tipo_pago_desc' => $documento->tipo_pago_id ? $documento->tipo_pago->descripcion : '-',
                'numero_doc' =>  $documento->serie.'-'.$documento->correlativo,
                'serie' => $documento->serie,
                'correlativo' => $documento->correlativo,
                'cliente' => $documento->tipo_documento_cliente.': '.$documento->documento_cliente.' - '.$documento->cliente,
                'empresa' => $documento->empresa,
                'empresa_id' => $documento->empresa_id,
                'cliente_id' => $documento->cliente_id,
                'cotizacion_venta' =>  $documento->cotizacion_venta,
                'fecha_documento' =>  Carbon::parse($documento->fecha_documento)->format( 'd/m/Y'),
                'estado' => $documento->estado_pago,
                'condicion' => $documento->condicion->descripcion,
                'condicion_id' => $documento->condicion_id,
                'ruta_pago' => $documento->ruta_pago,
                'cuenta_id' => $documento->banco_empresa_id,
                'cuenta_desc' => $documento->banco_empresa_id ? $documento->bancoPagado->descripcion.':'.$documento->bancoPagado->num_cuenta : '-',
                'importe' => $documento->importe,
                'convertir' => $documento->convertir,
                'efectivo' => $documento->efectivo,
                'sunat' => $documento->sunat,
                'regularize' => $documento->regularize,
                'code' => $code,
                'otros' => $otros,
                'efectivo' => $efectivo,
                'transferencia' => $transferencia,
                'total' => $documento->total_pagar,
                'dias' => (int)(4 - $diff < 0 ? 0  : 4 - $diff),
                'notas' => $cantidad_notas
            ]);
        }

        return DataTables::of($coleccion)->toJson();
    }

    public function getDocumentClient(Request $request)
    {
        $fecha_hoy = Carbon::now()->toDateString();
        $documentos = Documento::where('estado','!=','ANULADO')->where('cliente_id', $request->cliente_id)->where('estado_pago', 'PENDIENTE')->whereBetween('fecha_documento', [$fecha_hoy, $fecha_hoy])->where('condicion_id', $request->condicion_id)->orderBy('id', 'desc')->get();
        $coleccion = collect([]);

        $hoy = Carbon::now();
        foreach($documentos as $documento){

            $transferencia = 0.00;
            $otros = 0.00;
            $efectivo = 0.00;

            if($documento->tipo_pago_id)
            {
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
            }

            $fecha_v = $documento->created_at;
            $diff =  $fecha_v->diffInDays($hoy);

            $cantidad_notas = count($documento->notas);

            $coleccion->push([
                'id' => $documento->id,
                'tipo_venta' => $documento->nombreTipo(),
                'tipo_venta_id' => $documento->tipo_venta,
                'empresa' => $documento->empresaEntidad->razon_social,
                'tipo_pago' => $documento->tipo_pago_id,
                'numero_doc' =>  $documento->serie.'-'.$documento->correlativo,
                'serie' => $documento->serie,
                'correlativo' => $documento->correlativo,
                'cliente' => $documento->tipo_documento_cliente.': '.$documento->documento_cliente.' - '.$documento->cliente,
                'empresa' => $documento->empresa,
                'empresa_id' => $documento->empresa_id,
                'convertir' => $documento->convertir,
                'cotizacion_venta' =>  $documento->cotizacion_venta,
                'fecha_documento' =>  Carbon::parse($documento->fecha_documento)->format( 'd/m/Y'),
                'estado' => $documento->estado_pago,
                'condicion' => $documento->condicion->descripcion,
                'sunat' => $documento->sunat,
                'contingencia' => $documento->contingencia,
                'otros' => 'S/. '.number_format($otros, 2, '.', ''),
                'efectivo' => 'S/. '.number_format($efectivo, 2, '.', ''),
                'transferencia' => 'S/. '.number_format($transferencia, 2, '.', ''),
                'total' => number_format($documento->total_pagar, 2, '.', ''),
                'dias' => (int)(7 - $diff < 0 ? 0  : 7 - $diff),
                'notas' => $cantidad_notas
            ]);
        }

        return response()->json([
            'success' => true,
            'ventas' => $coleccion
        ]);
    }

    public function storePago(Request $request)
    {
        
        try
        {
            DB::beginTransaction();
            $data = $request->all();

            $rules = [
                'tipo_pago_id'=> 'required',
                'efectivo'=> 'required',
                'importe'=> 'required',

            ];

            $message = [
                'tipo_pago_id.required' => 'El campo modo de pago es obligatorio.',
                'importe.required' => 'El campo importe es obligatorio.',
                'efectivo.required' => 'El campo efectivo es obligatorio.'
            ];

            $validator =  Validator::make($data, $rules, $message);

            if ($validator->fails()) {
                /*$clase = $validator->getMessageBag()->toArray();
                $cadena = "";
                foreach($clase as $clave => $valor) {
                    $cadena =  $cadena . "$valor[0] ";
                }*/

                //Session::flash('error_store_pago',$cadena);
                DB::rollBack();
                //return redirect()->route('ventas.caja.index');
                return response()->json([
                    'result' => 'warning',
                    'mensaje' => 'Ocurrió un error de validación.',
                    'data' => array('errors' => $validator->getMessageBag()->toArray()),
                ]);
            }

            $documento = Documento::find($request->venta_id);

            $documento->tipo_pago_id = $request->get('tipo_pago_id');
            $documento->importe = $request->get('importe');
            $documento->efectivo = $request->get('efectivo');
            $documento->estado_pago = 'PAGADA';
            $documento->banco_empresa_id = $request->get('cuenta_id');
            if($request->hasFile('imagen')){
                if(!file_exists(storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'pagos'))) {
                    mkdir(storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'pagos'));
                }
                $documento->ruta_pago = $request->file('imagen')->store('public/pagos',$documento->serie.'-'.$documento->correlativo);
            }
            $documento->update();

            if ($documento->convertir) {
                $doc_convertido = Documento::find($documento->convertir);
                $doc_convertido->estado_pago = $documento->estado_pago;
                $doc_convertido->importe = $documento->importe;
                $doc_convertido->efectivo = $documento->efectivo;
                $doc_convertido->tipo_pago_id = $documento->tipo_pago_id;
                $doc_convertido->banco_empresa_id = $documento->banco_empresa_id;
                $doc_convertido->ruta_pago = $documento->ruta_pago;
                $doc_convertido->update();
            }


            DB::commit();
            return response()->json([
                'result' => 'success',
                'mensaje' => 'Pago realizado exitosamente.',
            ]);
        }
        catch(Exception $e)
        {
            DB::rollBack();
            return response()->json([
                'result' => 'success',
                'mensaje' => 'Pago realizado exitosamente.',
                'data' => array('errors' => array('error' => [$e->getMessage()])),
            ]);
        }
    }
    public function updatePago(Request $request)
    {
     
        try
        {
            DB::beginTransaction();
            $data = $request->all();

            $rules = [
                'tipo_pago_id'=> 'required',
                'efectivo'=> 'required',
                'importe'=> 'required',

            ];

            $message = [
                'tipo_pago_id.required' => 'El campo modo de pago es obligatorio.',
                'importe.required' => 'El campo importe es obligatorio.',
                'efectivo.required' => 'El campo efectivo es obligatorio.'
            ];

            $validator =  Validator::make($data, $rules, $message);

            if ($validator->fails()) {
                DB::rollBack();
                return response()->json([
                    'result' => 'warning',
                    'mensaje' => 'Ocurrió un error de validación.',
                    'data' => array('errors' => $validator->getMessageBag()->toArray()),
                ]);
            }

            $documento = Documento::find($request->venta_id);

            $documento->tipo_pago_id = $request->get('tipo_pago_id');
            $documento->importe = $request->get('importe');
            $documento->efectivo = $request->get('efectivo');
            $documento->estado_pago = 'PAGADA';
            $documento->banco_empresa_id = $request->get('cuenta_id');
            $ruta_pago      =   $documento->ruta_pago;
            $ruta_pago_2    =   $documento->ruta_pago_2;


            //========== PARA IMAGEN 1 ========
            if($request->hasFile('imagen')){
                //Eliminar Archivo anterior
                if($ruta_pago)
                {
                    self::deleteImage($ruta_pago);
                }
                //Agregar nuevo archivo
                if(!file_exists(storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'pagos'))) {
                    mkdir(storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'pagos'));
                }

                $extension              =   $request->file('imagen')->getClientOriginalExtension();
                $nombreImagenPago       =   $documento->serie.'-'.$documento->correlativo.'.'.$extension;
                $documento->ruta_pago   =   $request->file('imagen')->storeAs('public/pagos',$nombreImagenPago);
                
            }else{
                if ($request->get('ruta_pago') == null || $request->get('ruta_pago') == "") {
                    $documento->ruta_pago = "";
                    if($ruta_pago)
                    {
                        self::deleteImage($ruta_pago);
                    }
                }
            }

            //========== PARA IMAGEN 2 ========
            if($request->hasFile('imagen_2')){
                //Eliminar Archivo anterior
                if($ruta_pago_2)
                {
                    self::deleteImage($ruta_pago_2);
                }
                //Agregar nuevo archivo
                if(!file_exists(storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'pagos'))) {
                    mkdir(storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'pagos'));
                }

                $extension              =   $request->file('imagen_2')->getClientOriginalExtension();
                $nombreImagenPago       =   $documento->serie.'-'.$documento->correlativo.'-2'.'.'.$extension;
                $documento->ruta_pago_2 =   $request->file('imagen_2')->storeAs('public/pagos',$nombreImagenPago);
            }else{
                if ($request->get('ruta_pago_2') == null || $request->get('ruta_pago_2') == "") {
                    $documento->ruta_pago_2 = "";
                    if($ruta_pago_2)
                    {
                        self::deleteImage($ruta_pago_2);
                    }
                }
            }

            $documento->update();

            if ($documento->convertir) {
                $doc_convertido = Documento::find($documento->convertir);
                $doc_convertido->estado_pago = $documento->estado_pago;
                $doc_convertido->importe = $documento->importe;
                $doc_convertido->efectivo = $documento->efectivo;
                $doc_convertido->tipo_pago_id = $documento->tipo_pago_id;
                $doc_convertido->banco_empresa_id = $documento->banco_empresa_id;
                $doc_convertido->ruta_pago = $documento->ruta_pago;
                $doc_convertido->update();
            }

            DB::commit();
            return response()->json([
                'result' => 'success',
                'mensaje' => 'Pago actualizado exitosamente.',
            ]);
        }
        catch(Exception $e)
        {
            DB::rollBack();
            return response()->json([
                'result' => 'error',
                'mensaje' => 'Error al intentar actualizar pago',
                'data' => array('errors' => array('error' => [$e->getMessage()])),
            ]);
        }
    }

    public function deleteImage($ruta_pago)
    {
        try{
            $sRutaImagenActual = str_replace('/storage', 'public', $ruta_pago);
            $sNombreImagenActual = str_replace('public/', '', $sRutaImagenActual);
            Storage::disk('public')->delete($sNombreImagenActual);
            return array('success' => true,'mensaje' => 'Imagen eliminada');
        }
        catch(Exception $e)
        {
            return array('success' => false,'mensaje' => $e->getMessage());
        }
    }
}
