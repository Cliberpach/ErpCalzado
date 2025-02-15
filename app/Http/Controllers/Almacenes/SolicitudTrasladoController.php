<?php

namespace App\Http\Controllers\Almacenes;

use App\Almacenes\Talla;
use App\Almacenes\Traslado;
use App\Almacenes\TrasladoDetalle;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SolicitudTrasladoController extends Controller
{
    public function index(){
        return view('almacenes.solicitudes_traslado.index');
    }

    public function getSolicitudesTraslado(Request $request){

        $traslados  =   DB::table('traslados as t')
                        ->join('almacenes as ao', 'ao.id', '=', 't.almacen_origen_id')
                        ->join('almacenes as ad', 'ad.id', '=', 't.almacen_destino_id')
                        ->join('empresa_sedes as eso', 'eso.id', '=', 't.sede_origen_id')
                        ->join('empresa_sedes as esd', 'esd.id', '=', 't.sede_destino_id')
                        ->select(
                            DB::raw('CONCAT("TR-", t.id) as simbolo'),
                            't.id',
                            'ao.descripcion as almacen_origen_nombre',
                            'ad.descripcion as almacen_destino_nombre',
                            't.observacion',
                            'eso.direccion as sede_origen_direccion',
                            'esd.direccion as sede_destino_direccion',
                            't.created_at as fecha_registro',
                            't.fecha_traslado',
                            't.registrador_nombre',
                            't.estado'
                        )->get();

        return DataTables::of($traslados)->make(true);

    }


    public function confirmarShow($id){

        $traslado   =   Traslado::find($id);
        $detalle    =   TrasladoDetalle::where('traslado_id',$id)->get();
        $tallas     =   Talla::where('estado','ACTIVO')->get();

        return view('almacenes.solicitudes_traslado.confirmar_show',compact('tallas','detalle','traslado'));
    }

/*
array:1 [
  "traslado_id" => "1"
]
*/ 
    public function confirmarStore(Request $request){
        
        //======== INCREMENTANDO STOCK EN ALMACÉN DESTINO ========
         
    }

}
