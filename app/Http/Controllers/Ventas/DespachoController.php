<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Ventas\EnvioVenta;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;
use App\Mantenimiento\Empresa\Empresa;

class DespachoController extends Controller
{
    public function index(){
        $this->authorize('haveaccess','despachos.index');
        return view('ventas.despachos.index');
    }

    public function getTable()
    {
        $envios_ventas  = EnvioVenta::select('documento_nro', 'cliente_nombre','fecha_envio_propuesta','fecha_envio','created_at','tipo_envio',
                            'empresa_envio_nombre','sede_envio_nombre','departamento','provincia','distrito',
                            'destinatario_nombre','tipo_pago_envio','monto_envio','entrega_domicilio',
                            'direccion_entrega','estado','documento_id') 
                        ->where('estado', 'ACTIVO')
                        ->orderBy('id', 'desc') 
                        ->get();

        $coleccion = collect([]);
        foreach($envios_ventas as $envio_venta) {
            $coleccion->push([
                'documento_nro'         =>  $envio_venta->documento_nro,
                'cliente_nombre'        =>  $envio_venta->cliente_nombre,
                'fecha_envio_propuesta' =>  $envio_venta->fecha_envio_propuesta,
                'fecha_envio'           =>  $envio_venta->fecha_envio?$envio_venta->fecha_envio:"-",
                'fecha_registro'        =>  Carbon::parse($envio_venta->created_at)->format('Y-m-d H:i:s'),
                'tipo_envio'            =>  $envio_venta->tipo_envio,
                'empresa_envio_nombre'  =>  $envio_venta->empresa_envio_nombre,
                'sede_envio_nombre'     =>  $envio_venta->sede_envio_nombre,
                'ubigeo'                =>  $envio_venta->departamento.' - '.$envio_venta->provincia.' - '.$envio_venta->distrito,
                'tipo_pago_envio'       =>  $envio_venta->tipo_pago_envio,
                'destinatario_nombre'   =>  $envio_venta->destinatario_nombre,
                'monto_envio'           =>  $envio_venta->monto_envio,
                'entrega_domicilio'     =>  $envio_venta->entrega_domicilio,
                'direccion_entrega'     =>  $envio_venta->direccion_entrega,
                'estado'                =>  $envio_venta->estado,
                'documento_id'          =>  $envio_venta->documento_id
                ]);
        }
        return DataTables::of($coleccion)->toJson();
    }


    public function showDetalles($documento_id){
        try {
            $detalles_doc_venta     =   DB::select('select * from cotizacion_documento_detalles as cdd
                                        where cdd.documento_id=?',[$documento_id]);
            
            return response()->json(['success'=>true,'detalles_doc_venta'=>$detalles_doc_venta]);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>"ERROR EN EL SERVIDOR",'exception'=>$th->getMessage()]);
        }
    }

    public function pdfBultos($documento_id,$nro_bultos){
        $empresa = Empresa::first();
        
        $pdf = PDF::loadview('ventas.despachos.pdf-bultos', [
            'empresa'       =>  $empresa,
            'nro_bultos'    =>  $nro_bultos
        ])->setPaper('a4')
        ->setPaper('a4', 'landscape')
        ->setWarnings(false);

        return $pdf->stream('nombre_del_archivo.pdf');
        dd($documento_id .'-'.$nro_bultos);
    }

}
