<?php

namespace App\Http\Controllers\Mantenimiento\Colaborador;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilidadesController;
use App\Http\Requests\Mantenimiento\Colaborador\ColaboradorStoreRequest;
use App\Mantenimiento\Colaborador\Colaborador;
use App\Mantenimiento\Persona\Persona;
use App\Mantenimiento\Sedes\Sede;
use App\PersonaTrabajador;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class ColaboradorController extends Controller
{
    public function index()
    {
        $this->authorize('haveaccess','colaborador.index');
        return view('mantenimiento.colaboradores.index');
    }

    public function getColaboradores(Request $request){

        $colaboradores  =   DB::table('colaboradores as co')
                            ->join('tabladetalles as td', 'td.id', '=', 'co.cargo_id')
                            ->join('empresa_sedes as es','es.id','co.sede_id')
                            ->select(
                                'co.id', 
                                'es.nombre as sede_nombre',
                                'co.nombre',
                                'co.direccion',
                                'co.telefono',
                                'co.nro_documento',
                                'co.dias_trabajo',
                                'co.dias_descanso',
                                'co.pago_mensual',
                                'td.descripcion as cargo_nombre',
                                'co.estado',
                                'co.tipo_documento_nombre'
                            )
                            ->where('co.estado','ACTIVO')
                            ->get();

        return DataTables::of($colaboradores)
                ->make(true);
    }

    public function create()
    {
        $this->authorize('haveaccess','colaborador.index');

        $sedes  =   Sede::where('estado','ACTIVO')->get();
    
        return view('mantenimiento.colaboradores.create',compact('sedes'));
    }


/*
array:10 [
  "_token"              =>  "uA8DojLYx1bnlHUStJrLNeX1e0TYsd5OkhYgNM2o"
  "tipo_documento"      =>  "6"
  "nro_documento"       =>  "75608753"
  "nombre"              =>  "LUIS DANIEL ALVA LUJAN"
  "cargo"               =>  "27"
  "direccion"           =>  "AV CHAVIMOCHIC 1234"
  "telefono"            =>  null
  "dias_trabajo"        =>  "24"
  "dias_descanso"       =>  "10"
  "pago_mensual"        =>  null
  "sede"                =>  1
]
*/ 
    public function store(ColaboradorStoreRequest $request)
    {
        $this->authorize('haveaccess','colaborador.index');

        DB::beginTransaction();
        try {

            //======= OBTENIENDO NOMBRE DEL TIPO DOCUMENTO =====
            $tipo_documento =   DB::select('select 
                                td.* 
                                from tabladetalles as td 
                                where 
                                td.id = ?',
                                [$request->get('tipo_documento')])[0];

            $colaborador                        =   new Colaborador();
            $colaborador->tipo_documento_id     =   $request->get('tipo_documento');
            $colaborador->tipo_documento_nombre =   $tipo_documento->simbolo;
            $colaborador->nombre                =   Str::upper($request->get('nombre'));
            $colaborador->cargo_id              =   $request->get('cargo');
            $colaborador->direccion             =   Str::upper($request->get('direccion'));
            $colaborador->telefono              =   $request->get('telefono');
            $colaborador->dias_trabajo          =   $request->get('dias_trabajo');
            $colaborador->dias_descanso         =   $request->get('dias_descanso');
            $colaborador->pago_mensual          =   $request->get('pago_mensual');
            $colaborador->nro_documento         =   $request->get('nro_documento');
            $colaborador->pago_dia              =   $request->get('pago_mensual')/30;
            $colaborador->sede_id               =   $request->get('sede');
            $colaborador->save();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'COLABORADOR REGISTRADO']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }

    }

    public function edit($id)
    {
        $this->authorize('haveaccess','colaborador.index');

        $colaborador = Colaborador::findOrFail($id);
        $sedes  =   Sede::where('estado','ACTIVO')->get();

        return view('mantenimiento.colaboradores.edit', [
            'colaborador'   =>  $colaborador,
            'sedes'         =>  $sedes  
        ]);
    }

/*
array:10 [
  "_token"                  => "uA8DojLYx1bnlHUStJrLNeX1e0TYsd5OkhYgNM2o"
  "tipo_documento"          => "6"
  "nro_documento"           => "75608753"
  "nombre"                  => "LUIS DANIEL ALVA LUJAN"
  "cargo"                   => "27"
  "direccion"               => "AV CHAVIMOCHIC 1234"
  "telefono"                => "974585471"
  "dias_trabajo"            => "24.00"
  "dias_descanso"           => "12.00"
  "pago_mensual"            => "1400.00"
  "sede"                    => "1"
]    
*/ 
    public function update(Request $request, $id)
    {
        $this->authorize('haveaccess','colaborador.index');
        DB::beginTransaction();
        try {

            $colaborador                    =   Colaborador::find($id);

            //======= VERIFICAR QUE EL COLABORADOR A CAMBIAR NO TENGA CAJAS ABIERTAS ======
            $movimiento =   DB::select('select 
                            dmc.movimiento_id 
                            from detalles_movimiento_caja as dmc
                            where 
                            dmc.colaborador_id = ? 
                            and dmc.fecha_salida is null',
                            [$id]);
       
            if($colaborador->sede_id != $request->get('sede') &&  count($movimiento) != 0 ){
                throw new Exception("PERTENECES A UNA CAJA ABIERTA EN LA SEDE ACTUAL, NO PUEDES CAMBIARTE DE SEDE HASTA QUE LA CIERRES");
            }


            $colaborador->tipo_documento_id =   $request->get('tipo_documento');
            $colaborador->nombre            =   Str::upper($request->get('nombre'));
            $colaborador->cargo_id          =   $request->get('cargo');
            $colaborador->direccion         =   Str::upper($request->get('direccion'));
            $colaborador->telefono          =   $request->get('telefono');
            $colaborador->dias_trabajo      =   $request->get('dias_trabajo');
            $colaborador->dias_descanso     =   $request->get('dias_descanso');
            $colaborador->pago_mensual      =   $request->get('pago_mensual');
            $colaborador->nro_documento     =   $request->get('nro_documento');
            $colaborador->pago_dia          =   $request->get('pago_mensual')/30;
            $colaborador->sede_id           =   $request->get('sede');
            $colaborador->update();

            //======== ACTUALIZAR USUARIO EN CASO TENGA =======
            DB::table('users')
            ->where('colaborador_id', '=', $colaborador->id)
            ->update([
                'sede_id'       =>  $request->get('sede'),
                'updated_at'    =>  now()
            ]);  

            DB::commit();
            return response()->json(['success'=>true,'message'=>'COLABORADOR ACTUALIZADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function show($id)
    {
        $colaborador = Colaborador::findOrFail($id);
        return view('mantenimiento.colaboradores.show', [
            'colaborador' => $colaborador
        ]);
    }

    public function destroy($id)
    {
        $this->authorize('haveaccess','colaborador.index');
        DB::beginTransaction();
        try {

            //======= VERIFICAR QUE EL COLABORADOR A CAMBIAR NO TENGA CAJAS ABIERTAS ======
            $movimiento =   DB::select('select 
                            dmc.movimiento_id 
                            from detalles_movimiento_caja as dmc
                            where 
                            dmc.colaborador_id = ? 
                            and dmc.fecha_salida is null',
                            [$id]);
   
            if(count($movimiento) != 0 ){
                throw new Exception("ESTE COLABORADOR NO PUEDE ELIMINARSE, PORQUE TIENE UNA CAJA ABIERTA");
            }

            $colaborador                    =   Colaborador::find($id);
            $colaborador->estado            =   'ANULADO';
            $colaborador->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'COLABORADOR ELIMINADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function getDni(Request $request)
    {
        $data = $request->all();
        $existe = false;
        $igualPersona = false;
        if (!is_null($data['tipo_documento']) && !is_null($data['documento'])) {
            if (!is_null($data['id'])) {
                $persona = Persona::findOrFail($data['id']);
                if ($persona->tipo_documento == $data['tipo_documento'] && $persona->documento == $data['documento']) {
                    $igualPersona = true;
                } else {
                    $persona = Persona::where([
                        ['tipo_documento', '=', $data['tipo_documento']],
                        ['documento', $data['documento']],
                        ['estado', 'ACTIVO']
                    ])->first();
                }
            } else {
                $persona = Persona::where([
                    ['tipo_documento', '=', $data['tipo_documento']],
                    ['documento', $data['documento']],
                    ['estado', 'ACTIVO']
                ])->first();
            }

            if (!is_null($persona) && (!is_null($persona->colaborador) || !is_null($persona->vendedor))) {
                $existe = true;
            }
        }

        $result = [
            'existe' => $existe,
            'igual_persona' => $igualPersona
        ];

        return response()->json($result);
    }

    public function consultarDni($dni){
        try {
            //======== VALIDANDO FORMATO DNI ========
            if(strlen($dni) !== 8){
                throw new Exception("EL DNI DEBE CONTAR CON 8 DÍGITOS");
            }

            //======== VALIDAR DNI ÚNICO =========
            $existe =   DB::select('select 
                        c.id 
                        from colaboradores as c
                        where c.nro_documento = ? 
                        and c.estado = "ACTIVO"',
                        [$dni]);

            if(count($existe) > 0){
                throw new Exception('El dni ya existe en la tabla colaboradores');    
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