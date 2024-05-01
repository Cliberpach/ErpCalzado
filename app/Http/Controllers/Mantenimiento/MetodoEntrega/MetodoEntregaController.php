<?php

namespace App\Http\Controllers\Mantenimiento\MetodoEntrega;

use App\Http\Controllers\Controller;
use App\Mantenimiento\Persona\Persona;
use App\Mantenimiento\Persona\PersonaVendedor;
use App\Mantenimiento\Vendedor\Vendedor;
use App\Mantenimiento\MetodoEntrega\MetodoEntrega;
use App\Mantenimiento\MetodoEntrega\EmpresaEnvioSede;
use App\PersonaTrabajador;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class MetodoEntregaController extends Controller
{
    public function index()
    {
        $this->authorize('haveaccess','metodo_entrega.index');
        $tipos_envio    =    DB::select('select td.id,td.descripcion from tablas as t 
                            inner join tabladetalles as td  on t.id=td.tabla_id
                            where t.id=35');

        return view('mantenimiento.metodos_entrega.index',compact('tipos_envio'));
    }

    public function getTable()
    {
        $metodos_entrega = MetodoEntrega::all();
        $coleccion = collect([]);
        foreach($metodos_entrega as $metodo_entrega) {
            if($metodo_entrega->estado == "ACTIVO")
            {
                $coleccion->push([
                    'id'        =>  $metodo_entrega->id,
                    'empresa'   =>  $metodo_entrega->empresa,
                    'tipo_envio'=>  $metodo_entrega->tipo_envio,
                    'fecha' => $metodo_entrega->created_at->format('Y-m-d H:i:s'),                
                ]);
            }

        }
        return DataTables::of($coleccion)->toJson();
    }

    public function create()
    {
        $this->authorize('haveaccess','metodo_entrega.index');
        $tipos_envio    =    DB::select('select td.id,td.descripcion from tablas as t 
                            inner join tabladetalles as td  on t.id=td.tabla_id
                            where t.id=35');

        return view('mantenimiento.metodos_entrega.create',compact('tipos_envio'));
    }

    public function store(Request $request)
    {
        $this->authorize('haveaccess','metodo_entrega.index');
        $data = $request->all();

        $rules = [
            'empresa'       => 'required',
            'tipo_envio'    => 'required',
        ];

        $message = [
            'empresa.required'      => 'El campo empresa es obligatorio.',
            'tipo_envio.required'   => 'El campo tipo de envío es obligatorio.',
        ];

        Validator::make($data, $rules, $message)->validate();

        try {
            $tipo_envio     =   DB::select ('select * from tabladetalles as td
            where tabla_id=35 and id=?',[$request->get('tipo_envio')]);

            $metodo_entrega                 =   new MetodoEntrega();
            $metodo_entrega->empresa        =   $request->get('empresa');
            $metodo_entrega->tipo_envio     =   $tipo_envio[0]->descripcion;
            $metodo_entrega->save();

            //Registro de actividad
            $descripcion = "SE AGREGÓ EL MÉTODO DE ENTREGA: ". $metodo_entrega->empresa.' '.$metodo_entrega->tipo_envio;
            $gestion = "metodos_entrega";
            crearRegistro($metodo_entrega, $descripcion , $gestion);

            return response()->json(['success'=>true,'message'=> $descripcion]);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=> "ERROR EN EL SERVIDOR","exception"=>$th->getMessage()]);
        }

       // Session::flash('success', 'Método entrega creado.');
        // return redirect()->route('mantenimiento.metodo_entrega.index')->with('guardar', 'success');
    }

    public function getMetodoEntrega($id)
    {
        try {
            $metodo_entrega =   MetodoEntrega::find($id);
            
            return response()->json(['success'=>true,'metodo_entrega'=>$metodo_entrega]);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>'ERROR EN EL SERVIDOR','exception'=>$th->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        $this->authorize('haveaccess','metodo_entrega.index');
        $data = $request->all();

        $rules = [
            'empresa_edit'       => 'required',
            'tipo_envio_edit'    => 'required',
        ];

        $message = [
            'empresa_edit.required'      => 'El campo empresa es obligatorio.',
            'tipo_envio_edit.required'   => 'El campo tipo de envío es obligatorio.',
        ];

        Validator::make($data, $rules, $message)->validate();

        DB::beginTransaction();
        try {
            $tipo_envio     =   DB::select ('select * from tabladetalles as td
            where tabla_id=35 and id=?',[$request->get('tipo_envio_edit')]);

            $metodo_entrega                 =   MetodoEntrega::find($request->get('empresa_envio_id'));
            $metodo_entrega->empresa        =   $request->get('empresa_edit');
            $metodo_entrega->tipo_envio     =   $tipo_envio[0]->descripcion;
            $metodo_entrega->save();

            //Registro de actividad
            $descripcion = "SE MODIFICÓ EL MÉTODO DE ENTREGA: ". $metodo_entrega->empresa.' '.$metodo_entrega->tipo_envio;
            $gestion = "metodos_entrega";
            crearRegistro($metodo_entrega, $descripcion , $gestion);

            DB::commit();

            return response()->json(['success'=>true,'message'=> $descripcion]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['success'=>false,'message'=> "ERROR EN EL SERVIDOR","exception"=>$th->getMessage()]);
        }
    }

    public function show($id)
    {
        $this->authorize('haveaccess','vendedor.index');
        $vendedor = Vendedor::findOrFail($id);
        return view('mantenimiento.vendedores.show', [
            'vendedor' => $vendedor
        ]);
    }

    public function destroy($id)
    {           
        $this->authorize('haveaccess','metodo_entrega.index');
        DB::transaction(function () use ($id) {
            $metodo_entrega             =   MetodoEntrega::findOrFail($id);
            $metodo_entrega->estado     =   'ANULADO';
            $metodo_entrega->update();

            //Registro de actividad
            $descripcion = "SE ELIMINÓ EL MÉTODO DE ENTREGA: ". $metodo_entrega->empresa.' '.$metodo_entrega->tipo_envio;
            $gestion = "metodos_entrega";
            eliminarRegistro($metodo_entrega, $descripcion , $gestion);
        });


        Session::flash('success', 'Método de entrega eliminado.');
        return redirect()->route('mantenimiento.metodo_entrega.index')->with('eliminar', 'success');
    }

    public function getSedes($agencia_id){
        try {
            $sedes  = DB::select('select * from empresa_envio_sedes as ees
                        where ees.empresa_envio_id=?',[$agencia_id]);

            return response()->json(['success'=>true,'sedes'=>$sedes]);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>'ERROR EN EL SERVIDOR','exception'=>$th->getMessage()]);
        }
    }

    public function createSede(Request $request){
        $this->authorize('haveaccess','metodo_entrega.index');
        $data = $request->all();

        $rules = [
            'agencia'           => 'required',
            'direccion'         => 'required',
            'departamento'      => 'required',
            'provincia'         => 'required',
            'distrito'          => 'required',
        ];

        $messages = [
            'agencia.required'     => 'El campo agencia es obligatorio.',
            'direccion.required'   => 'El campo dirección es obligatorio.',
            'departamento.required' => 'El campo departamento es obligatorio.',
            'provincia.required'    => 'El campo provincia es obligatorio.',
            'distrito.required'     => 'El campo distrito es obligatorio.',
        ];
        
        Validator::make($data, $rules, $messages)->validate();

        //======== CREANDO LA SEDE ======
        DB::beginTransaction();
        try {
            if (strlen($request->get('departamento')) === 1) {
                $departamento_id = '0' . $request->get('departamento');
            }else{
                $departamento_id    =   $request->get('departamento');   
            }
    
            if (strlen($request->get('provincia')) === 3) {
                $provincia_id       = '0' . $request->get('provincia');
            }else{
                $provincia_id       =   $request->get('provincia');   
            }
    
            if (strlen($request->get('distrito')) === 5) {
                $distrito_id       = '0' . $request->get('distrito');
            }else{
                $distrito_id       =   $request->get('distrito');   
            }

            $departamento   =   DB::select ('select d.nombre from departamentos as d
            where d.id=?',[$departamento_id]);

            $provincia      =   DB::select ('select p.nombre from provincias as p
            where p.id=?',[$provincia_id]);

            $distrito       =   DB::select ('select d.nombre from distritos as d
            where d.id=?',[$distrito_id]);

            $sede                   =   new EmpresaEnvioSede();
            $sede->empresa_envio_id =   $request->get('agencia');
            $sede->direccion        =   $request->get('direccion');
            $sede->departamento     =   $departamento[0]->nombre;
            $sede->provincia        =   $provincia[0]->nombre;
            $sede->distrito         =   $distrito[0]->nombre;
            $sede->save();

            $agencia                =   MetodoEntrega::find($request->get('agencia'));

            DB::commit();

            $message    =   'SE CREO LA NUEVA SEDE: '.$agencia->empresa.' - '.
                            $sede->direccion.'-'.$sede->departamento.'-'.$sede->provincia.'-'.$sede->distrito;

            return response()->json(['success'=>true,'message'=>$message,'nueva_sede'=>$sede]);

        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['success'=>false,'message'=>'ERROR EN EL SERVIDOR','exception'=>$th->getMessage()]);
        }
    }
}
