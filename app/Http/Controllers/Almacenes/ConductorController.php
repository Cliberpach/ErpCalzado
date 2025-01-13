<?php

namespace App\Http\Controllers\Almacenes;

use App\Almacenes\Conductor;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilidadesController;
use App\Http\Requests\Almacen\Conductor\ConductorStoreRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ConductorController extends Controller
{
    public function index(){
        return view('almacenes.conductores.index');
    }

    public function getConductores(Request $request){

        $conductores = DB::table('conductores as co')
                    ->select(
                        'co.id',
                        'co.nombre_completo as nombre',
                        'co.tipo_documento',
                        'co.nro_documento',
                        'co.telefono',
                        'co.licencia'
                    )
                    ->where('co.estado','ACTIVO')
                    ->get();

        return DataTables::of($conductores)
                ->make(true);
    }

    public function create(){

        $tipos_documento    =   DB::select('select 
                                td.id,
                                td.simbolo,
                                td.descripcion
                                from tabladetalles as td
                                where td.id <> 8 and td.tabla_id = 3');


        return view('almacenes.conductores.create',compact('tipos_documento'));
    }

    public function edit($id){
        $tipos_documento    =   TipoDocumento::where('estado','ACTIVO')
                                ->where('id','<>',2)->get();

        $conductor          =   Conductor::find($id);

        return view('registros.conductores.edit',compact('conductor','tipos_documento'));
    }

    /*
    array:7 [ // app\Http\Controllers\Registros\ConductorController.php:67
    "_token"            => "40VPBenxpHS6nf6bF8zTNjfnxCLwLU3OGy5CRd1c"
    "tipo_documento"    => "1"
    "nro_documento"     => "80239830"
    "nombre"            => "CLIBER LESTER"
    "apellido"          => "PACHECO PERINANGO"
    "licencia"          => "4124sad"
    "telefono"          => "974585471"
    ]
    */
    public function store(ConductorStoreRequest $request){
        
        DB::beginTransaction();
        try {

            $conductor                      =   new Conductor();
            $conductor->tipo_documento_id   =   $request->get('tipo_documento');
            $conductor->nro_documento       =   $request->get('nro_documento');
            $conductor->nombre_completo     =   mb_strtoupper($request->get('nombre') . ' ' . $request->get('apellido'));
            $conductor->nombres             =   mb_strtoupper($request->get('nombre'));
            $conductor->apellidos           =   mb_strtoupper($request->get('apellido'));
            $conductor->telefono            =   $request->get('telefono');
            $conductor->licencia            =   mb_strtoupper($request->get('licencia'));
            $conductor->save();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'CONDUCTOR REGISTRADO']);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    /*
    array:7 [ // app\Http\Controllers\Registros\ConductorController.php:67
        "_token"            => "40VPBenxpHS6nf6bF8zTNjfnxCLwLU3OGy5CRd1c"
        "tipo_documento"    => "1"
        "nro_documento"     => "80239830"
        "nombre"            => "CLIBER LESTER"
        "apellido"          => "PACHECO PERINANGO"
        "licencia"          => "4124sad"
        "telefono"          => "974585471"
    ]
    */
    public function update(ConductorUpdateRequest $request,$id){
        DB::beginTransaction();
        try {
            $conductor                      =   Conductor::find($id);
            $conductor->tipo_documento_id   =   $request->get('tipo_documento');
            $conductor->nro_documento       =   $request->get('nro_documento');
            $conductor->nombre_completo     =   mb_strtoupper($request->get('nombre') . ' ' . $request->get('apellido'));
            $conductor->nombres             =   mb_strtoupper($request->get('nombre'));
            $conductor->apellidos           =   mb_strtoupper($request->get('apellido'));
            $conductor->telefono            =   $request->get('telefono');
            $conductor->licencia            =   mb_strtoupper($request->get('licencia'));
            $conductor->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'CONDUCTOR ACTUALIZADO']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage(),'line'=>$th->getLine()]);
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try {
            $conductor                    =   Conductor::find($id);
            $conductor->estado            =   'ANULADO';
            $conductor->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'CONDUCTOR ELIMINADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

     //======== VALIDAR DNI ÚNICO EN LA BASE DE DATOS, COLABORADORES ========    
     public function consultarDni($dni){
        
        try {
            //======== VALIDANDO FORMATO DNI ========
            if(strlen($dni) !== 8){
                throw new Exception("EL DNI DEBE CONTAR CON 8 DÍGITOS");
            }

            //======== VALIDAR DNI ÚNICO =========
            $existe =   DB::select('select 
                        c.id 
                        from conductores as c
                        where c.nro_documento = ? 
                        and c.estado = "ACTIVO"',
                        [$dni]);

            if(count($existe) > 0){
                throw new Exception('El dni ya existe en la tabla conductores');    
            }

            //======== CONSULTANDO DNI EN API RENIEC ========
            $res_consulta_api   =   UtilidadesController::apiDni($dni);
            $res                =   $res_consulta_api->getData();

            //======= EN CASO LA CONSULTA FUE EXITOSA =====
            if($res->success){
                return response()->json(['success'=>true,'data'=>$res->data,'message'=>'OPERACIÓN COMPLETADA']);
            }else{
                throw new Exception($res->message);
            }

        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
      
    }
}
