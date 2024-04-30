<?php

namespace App\Http\Controllers\Mantenimiento\MetodoEntrega;

use App\Http\Controllers\Controller;
use App\Mantenimiento\Persona\Persona;
use App\Mantenimiento\Persona\PersonaVendedor;
use App\Mantenimiento\Vendedor\Vendedor;
use App\Mantenimiento\MetodoEntrega\MetodoEntrega;

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
        return view('mantenimiento.metodos_entrega.index');
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
                    'sede'      =>  $metodo_entrega->sede,
                    'direccion' =>  $metodo_entrega->departamento.'-'.$metodo_entrega->provincia.'-'.$metodo_entrega->distrito
                                    .'-'.$metodo_entrega->direccion,
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
            'sede'          => 'required',
            'direccion'     => 'required',
            'tipo_envio'    => 'required',
            'departamento'  => 'required',
            'provincia'     => 'required',
            'distrito'      => 'required'
        ];

        $message = [
            'empresa.required'      => 'El campo empresa es obligatorio.',
            'sede.required'         => 'El campo sede es obligatorio.',
            'direccion.required'    => 'El campo dirección es obligatorio.',
            'tipo_envio.required'   => 'El campo tipo de envío es obligatorio.',
            'departamento.required' => 'El campo departamento es obligatorio.',
            'provincia.required'    => 'El campo provincia es obligatorio.',
            'distrito.required'     => 'El campo distrito es obligatorio.'
        ];

        Validator::make($data, $rules, $message)->validate();

        $tipo_envio     =   DB::select ('select * from tabladetalles as td
                            where tabla_id=35 and id=?',[$request->get('tipo_envio')]);

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

        $metodo_entrega                 =   new MetodoEntrega();
        $metodo_entrega->empresa        =   $request->get('empresa');
        $metodo_entrega->sede           =   $request->get('sede');
        $metodo_entrega->direccion      =   $request->get('direccion');
        $metodo_entrega->tipo_envio     =   $tipo_envio[0]->descripcion;
        $metodo_entrega->departamento   =   $departamento[0]->nombre;
        $metodo_entrega->provincia      =   $provincia[0]->nombre;
        $metodo_entrega->distrito       =   $distrito[0]->nombre;
        $metodo_entrega->save();

    
        //Registro de actividad
        $descripcion = "SE AGREGÓ EL MÉTODO DE ENTREGA: ". $metodo_entrega->empresa.' '.$metodo_entrega->tipo_envio;
        $gestion = "metodos_entrega";
        crearRegistro($metodo_entrega, $descripcion , $gestion);



        Session::flash('success', 'Método entrega creado.');
        return redirect()->route('mantenimiento.metodo_entrega.index')->with('guardar', 'success');
    }

    public function edit($id)
    {
        $this->authorize('haveaccess','metodo_entrega.index');
        $metodo_entrega     =       MetodoEntrega::findOrFail($id);
        $tipos_envio        =    DB::select('select td.id,td.descripcion from tablas as t 
                                inner join tabladetalles as td  on t.id=td.tabla_id
                                where t.id=35');

        return view('mantenimiento.metodos_entrega.edit', [
            'metodo_entrega'    => $metodo_entrega,
            'tipos_envio'       =>  $tipos_envio
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('haveaccess','metodo_entrega.index');

        $data = $request->all();

        $rules = [
            'empresa'       => 'required',
            'sede'          => 'required',
            'direccion'     => 'required',
            'tipo_envio'    => 'required',
            'departamento'  => 'required',
            'provincia'     => 'required',
            'distrito'      => 'required'
        ];

        $message = [
            'empresa.required'      => 'El campo empresa es obligatorio.',
            'sede.required'         => 'El campo sede es obligatorio.',
            'direccion.required'    => 'El campo dirección es obligatorio.',
            'tipo_envio.required'   => 'El campo tipo de envío es obligatorio.',
            'departamento.required' => 'El campo departamento es obligatorio.',
            'provincia.required'    => 'El campo provincia es obligatorio.',
            'distrito.required'     => 'El campo distrito es obligatorio.'
        ];

        Validator::make($data, $rules, $message)->validate();

        $tipo_envio     =   DB::select ('select * from tabladetalles as td
        where tabla_id=35 and id=?',[$request->get('tipo_envio')]);

        
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
        
        $metodo_entrega                 =   MetodoEntrega::find($id);
        $metodo_entrega->empresa        =   $request->get('empresa');
        $metodo_entrega->sede           =   $request->get('sede');
        $metodo_entrega->direccion      =   $request->get('direccion');
        $metodo_entrega->tipo_envio     =   $tipo_envio[0]->descripcion;
        $metodo_entrega->departamento   =   $departamento[0]->nombre;
        $metodo_entrega->provincia      =   $provincia[0]->nombre;
        $metodo_entrega->distrito       =   $distrito[0]->nombre;
        $metodo_entrega->save();

    
        //Registro de actividad
        $descripcion = "SE MODIFICÓ EL MÉTODO DE ENTREGA: ". $metodo_entrega->empresa.' '.$metodo_entrega->tipo_envio;
        $gestion = "metodos_entrega";
        crearRegistro($metodo_entrega, $descripcion , $gestion);

        Session::flash('success', 'Método de entrega modificado.');
        return redirect()->route('mantenimiento.metodo_entrega.index')->with('modificar', 'success');
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

            if (!is_null($persona) && !is_null($persona->vendedor)) {
                $existe = true;
            }
        }

        $result = [
            'existe' => $existe,
            'igual_persona' => $igualPersona
        ];

        return response()->json($result);
    }
}
