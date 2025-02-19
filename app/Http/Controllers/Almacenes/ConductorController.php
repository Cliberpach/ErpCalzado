<?php

namespace App\Http\Controllers\Almacenes;

use App\Almacenes\Conductor;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilidadesController;
use App\Http\Requests\Almacen\Conductor\ConductorStoreRequest;
use App\Http\Requests\Almacen\Conductor\ConductorUpdateRequest;
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
                        'co.tipo_documento_nombre',
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
                                where 
                                td.tabla_id = 3');


        return view('almacenes.conductores.create',compact('tipos_documento'));
    }

    public function edit($id){
        $tipos_documento    =   DB::select('select 
                                td.id,
                                td.simbolo,
                                td.descripcion
                                from tabladetalles as td
                                where 
                                td.tabla_id = 3');

        $conductor          =   Conductor::find($id);

        return view('registros.conductores.edit',compact('conductor','tipos_documento'));
    }

    /*
    array:7 [ // app\Http\Controllers\Registros\ConductorController.php:67
    "_token"                => "40VPBenxpHS6nf6bF8zTNjfnxCLwLU3OGy5CRd1c"
    "tipo_documento"        => "1"
    "nro_documento"         => "80239830"
    "nombre"                => "CLIBER LESTER"
    "apellido"              => "PACHECO PERINANGO"
    "licencia"              => "4124sad"
    "telefono"              => "974585471"
    "modalidad_transporte"  => "PUBLICO"
    "registro_mtc"          =>  "MTC"
    ]
    */
    public function store(ConductorStoreRequest $request){
       
        DB::beginTransaction();
        try {

            $tipo_documento =   DB::select('select 
                                td.* 
                                from tabladetalles as td 
                                where 
                                td.id = ?',
                                [$request->get('tipo_documento')])[0];

            $conductor                          =   new Conductor();
            $conductor->tipo_documento_id       =   $request->get('tipo_documento');
            $conductor->nro_documento           =   $request->get('nro_documento');
            $conductor->nombre_completo         =   mb_strtoupper($request->get('nombre') . ' ' . $request->get('apellido'));
            $conductor->nombres                 =   mb_strtoupper($request->get('nombre'));
            $conductor->apellidos               =   mb_strtoupper($request->get('apellido'));
            $conductor->telefono                =   $request->get('telefono');
            $conductor->licencia                =   mb_strtoupper($request->get('licencia'));
            $conductor->tipo_documento_nombre   =   $tipo_documento->simbolo;
            $conductor->modalidad_transporte    =   $request->get('modalidad_transporte');
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

            $tipo_documento =   DB::select('select 
                                td.* 
                                from tabladetalles as td 
                                where 
                                td.id = ?',
                                [$request->get('tipo_documento')])[0];
            
            $conductor                          =   Conductor::find($id);
            $conductor->tipo_documento_id       =   $request->get('tipo_documento');
            $conductor->nro_documento           =   $request->get('nro_documento');
            $conductor->nombre_completo         =   mb_strtoupper($request->get('nombre') . ' ' . $request->get('apellido'));
            $conductor->nombres                 =   mb_strtoupper($request->get('nombre'));
            $conductor->apellidos               =   mb_strtoupper($request->get('apellido'));
            $conductor->telefono                =   $request->get('telefono');
            $conductor->licencia                =   mb_strtoupper($request->get('licencia'));
            $conductor->tipo_documento_nombre   =   $tipo_documento->simbolo;
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


    /*
array:2 [ // app\Http\Controllers\Ventas\proveedorController.php:24
  "tipo_documento"  => "1"
  "nro_documento"   => "75563122"
]
*/ 
public function consultarDocumento(Request $request){
    try {
        //========= VALIDANDO QUE EL TIPO DOCUMENTO Y N° DOCUMENTO NO SEAN NULL =======
        $tipo_documento =   $request->get('tipo_documento',null);
        $nro_documento  =   $request->get('nro_documento',null);

        if(!$tipo_documento){
            throw new Exception("EL TIPO DE DOCUMENTO ES OBLIGATORIO");
        }

        if(!$nro_documento){
            throw new Exception("EL N° DOC ES OBLIGATORIO");
        }

        if (!is_numeric($nro_documento)) {
            throw new Exception("EL N° DOCUMENTO DEBE SER NUMÉRICO");
        }

        //========= VERIFICANDO QUE EXISTA EL TIPO DOC EN LA BD ========
        $exists_tipo_doc    =   DB::select('select 
                                td.id,
                                td.descripcion
                                from tipos_documento as td
                                where 
                                td.id = ?
                                and td.estado = "ACTIVO"',[$tipo_documento]);

        if(count($exists_tipo_doc) === 0){
            throw new Exception("EL TIPO DE DOC NO EXISTE EN LA BD");
        }

        if($tipo_documento != 6 && $tipo_documento != 8){
            throw new Exception("SOLO SE PUEDEN CONSULTAR DNI Y RUC");
        }

        if ( $tipo_documento == 6 && strlen($nro_documento) != 8) {
            throw new Exception("EL TIPO DE DOCUMENTO DNI DEBE TENER 8 DÍGITOS");
        }

        if ( $tipo_documento == 8 && strlen($nro_documento) != 11) {
            throw new Exception("EL TIPO DE DOCUMENTO RUC DEBE TENER 11 DÍGITOS");
        }


        //======= COMPROBAR QUE NO EXISTA EL DOCUMENTO EN LA TABLA conductores =======
        $existe_nro_documento   =   DB::select('select 
                                    c.id,
                                    c.nombre
                                    from conductores as c
                                    where 
                                    c.tipo_documento_id = ?
                                    and c.nro_documento = ? 
                                    and c.estado = "ACTIVO"',
                                    [$tipo_documento,$nro_documento]);

        if(count($existe_nro_documento) > 0){
            throw new Exception($exists_tipo_doc[0]->descripcion.':'.$nro_documento.'.YA EXISTE EN LA BD');
        }
        
        if($tipo_documento == 6){

            $res_consulta_api   =   UtilidadesController::apiDni($nro_documento);
            $res                =   $res_consulta_api->getData();

            //======= EN CASO LA CONSULTA FUE EXITOSA =====
            if($res->success){
                return response()->json(['success'=>true,'data'=>$res->data,'message'=>'OPERACIÓN COMPLETADA']);
            }else{
                throw new Exception($res->message);
            }
        }

        if($tipo_documento == 8){
            $res_consulta_api   =   UtilidadesController::apiRuc($nro_documento);
            $res                =   $res_consulta_api->getData();

            //======= EN CASO LA CONSULTA FUE EXITOSA =====
            if($res->success){
                return response()->json(['success'=>true,'data'=>$res->data,'message'=>'OPERACIÓN COMPLETADA']);
            }else{
                throw new Exception($res->message);
            }
        }

    } catch (\Throwable $th) {
        return response()->json(['success'=>false,'message'=>$th->getMessage()]);
    }
}

}
