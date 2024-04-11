<?php

namespace App\Http\Controllers\Compras;

use App\Compras\Banco;
use App\Compras\Proveedor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\RequiredIf;
use Yajra\DataTables\Facades\DataTables;

class ProveedorController extends Controller
{
    public function index()
    {
        $this->authorize('haveaccess','proveedor.index');
        return view('compras.proveedores.index');
    }

    public function getProvider(){
        $this->authorize('haveaccess','proveedor.index');
        $proveedores = DB::table('proveedores')->select('proveedores.*')->where('estado','ACTIVO')->get();
        $coleccion = collect([]);
        foreach($proveedores as $proveedor){
            if ($proveedor->dni) {
                $dni = $proveedor->dni;
            }else{
                $dni = '-';
            }

            if ($proveedor->ruc) {
                $ruc = $proveedor->ruc;
            }else{
                $ruc = '-';
            }

            $coleccion->push([
                'id' => $proveedor->id,
                'descripcion' => $proveedor->descripcion,
                'dni' => $dni,
                'ruc' => $ruc,
                'tipo' => $proveedor->tipo_persona, 
            ]);
        }
        return DataTables::of($coleccion)->toJson();
    }

    public function create()
    {
        $tipos = personas();
        $zonas = zonas();
        $bancos = bancos();
        $monedas = tipos_moneda();
        return view('compras.proveedores.create',[
            'tipos' => $tipos,
            'zonas' => $zonas,
            'bancos' => $bancos,
            'monedas' => $monedas,
        ]);
    }

    public function store(Request $request){        
        $this->authorize('haveaccess','proveedor.index');
        $data = $request->all();
        $rules = [
            'tipo_documento' => 'required',
        ];
        $message = [
            'tipo_documento.required' => 'El campo Tipo es obligatorio.',
        ];
        
        $validator = Validator::make($data, $rules, $message);

        if ($validator->fails()) {
            if ($request->has('type_store')) {
                return response()->json(['errors' => $validator->errors()], 422);
            } else {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }

        if ($request->input('tipo_documento') == 'RUC') {
            $rules = [
                'ruc' => [
                    new RequiredIf($request->input('tipo_documento') == 'RUC'),
                    'numeric',
                    'digits:11',
                    Rule::unique('proveedores','ruc')->where(function ($query) {
                        $query->whereIn('estado',["ACTIVO"])->whereNotNull('ruc');
                    })
                ],
                'tipo_persona'=> 'required',
            ];
            $message = [
                'ruc.required' => 'El campo Ruc es obligatorio.',
                'ruc.unique'=> 'El Ruc ya se encuentra registrado.',
                'ruc.numeric'=> 'El campo Ruc debe se numérico.',
                'ruc.digits'=>'El campo Ruc debe tener 11 dígitos.',
                'tipo_persona.required' => 'El campo Tipo es obligatorio.',
            ];

            $validator = Validator::make($data, $rules, $message);
            if ($validator->fails()) {
                if ($request->has('type_store')) {
                    return response()->json(['errors' => $validator->errors()], 422);
                } else {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
            }

        }else{
            $rules = [
                'dni' => [
                    new RequiredIf($request->input('tipo_documento') == 'DNI'),
                    'numeric',
                    'digits:8',
                    Rule::unique('proveedores','dni')->where(function ($query) {
                        $query->whereIn('estado',["ACTIVO"])->whereNotNull('dni');
                    })
                ],
                'tipo_persona_dni'=> 'required',
            ];
            $message = [
                'dni.digits' => 'El campo Dni debe tener 8 dígitos.',
                'dni.numeric' => 'El campo Dni debe ser numérico.',
                'dni.required' => 'El campo Dni es obligatorio.',
                'dni.unique'=> 'El Dni ya se encuentra registrado.',
                'tipo_persona_dni.required' => 'El campo Tipo es obligatorio.',
            ];
            
            $validator = Validator::make($data, $rules, $message);
            if ($validator->fails()) {
                if ($request->has('type_store')) {
                    return response()->json(['errors' => $validator->errors()], 422);
                } else {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
            }
        }
        
        $rules = [

            'descripcion' => 'required',
            'direccion' => 'required',
            'zona' => 'required',

            'telefono' => 'required',
            'celular' => 'nullable',
            'web' => 'nullable',
            'facebook' => 'nullable',
            'instagram' => 'nullable',
            'correo' => 'required|email',

            'contacto' => 'nullable',
            'correo_contacto' => 'nullable',
            'telefono_contacto' => 'nullable',
            'celular_contacto' => 'nullable',

            'ruc_transporte' => 'nullable',
            'transporte' => 'nullable',
            'direccion_transporte' => 'nullable',

            'direccion_almacen' => 'nullable',
            'estado' => 'required',

            'calidad' => 'nullable',
            'telefono_calidad' => 'nullable',
            'celular_calidad' => 'nullable',
            'correo_calidad' => 'nullable|email',

        ];
        
        $message = [
            'descripcion.required' => 'El campo Descripción es obligatorio.',
            'direccion.required' => 'El campo Dirección es obligatorio.',
            'zona.required' => 'El campo Zona es obligatorio.',

            'correo.required' => 'El campo Correo es obligatorio.',
            'correo.email' => 'El campo Correo debe ser email.',
            // 'telefono.numeric' => 'El campo Teléfono debe ser numérico.',
            // 'telefono.required' => 'El campo Teléfono es obligatorio.',

            // 'celular.numeric' => 'El campo Celular debe ser numérico.',
            // 'telefono_contacto.numeric' => 'El campo Teléfono debe ser numérico.',
            // 'celular_contacto.numeric' => 'El campo Celular debe ser numérico.',

            'ruc_tranporte.numeric' => 'El campo Ruc debe ser numérico.',
            'ruc_tranporte.required' => 'El campo Ruc es obligatorio.',
            'transporte.required' => 'El campo Nombre Completo es obligatorio.',
            'direccion_transporte.required' => 'El campo Direccion es obligatorio.',

            'estado.required' => 'El campo Estado es obligatorio.',
                        
            // 'telefono_calidad.numeric' => 'El campo Teléfono debe ser numérico.',
            // 'celular_calidad.numeric' => 'El campo Celular debe ser numérico.',
            'correo_calidad.email' => 'El campo Correo debe ser email.',
            

        ];

        $validator = Validator::make($data, $rules, $message);
        if ($validator->fails()) {
            if ($request->has('type_store')) {
                return response()->json(['errors' => $validator->errors()], 422);
            } else {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }

        $proveedor = new Proveedor();
        $proveedor->ruc = $request->get('ruc');
        $proveedor->dni = $request->get('dni');
        $proveedor->descripcion = $request->get('descripcion');
        $proveedor->tipo_documento= $request->get('tipo_documento');

        if ($data['tipo_documento'] == "RUC") {
            $proveedor->tipo_persona = $request->get('tipo_persona');
        }else{
            $proveedor->tipo_persona = $request->get('tipo_persona_dni');
        }
        $proveedor->direccion = $request->get('direccion');
        $proveedor->correo = $request->get('correo');

        $proveedor->web = $request->get('web');
        $proveedor->facebook = $request->get('facebook');
        $proveedor->instagram = $request->get('instagram');

        $proveedor->zona = $request->get('zona');
        $proveedor->telefono = $request->get('telefono');
        $proveedor->celular = $request->get('celular');


        $proveedor->contacto = $request->get('contacto');
        $proveedor->celular_contacto = $request->get('celular_contacto');
        $proveedor->telefono_contacto = $request->get('telefono_contacto');
        $proveedor->correo_contacto = $request->get('correo_contacto');

        $proveedor->calidad = $request->get('calidad');
        $proveedor->celular_calidad = $request->get('celular_calidad');
        $proveedor->telefono_calidad = $request->get('telefono_calidad');
        $proveedor->correo_calidad = $request->get('correo_calidad');
        
        $proveedor->ruc_transporte = $request->get('ruc_transporte');
        $proveedor->transporte = $request->get('transporte');
        $proveedor->direccion_transporte = $request->get('direccion_transporte');
        $proveedor->direccion_almacen = $request->get('direccion_almacen');
        
        $proveedor->estado_transporte = $request->get('estado_transporte');
        $proveedor->estado_documento = $request->get('estado');

        $proveedor->save();

        //Llenado de Bancos
        if($request->has('entidades_tabla')){
            $entidadesJSON = $request->get('entidades_tabla');
            $entidadtabla = json_decode($entidadesJSON[0]);

            if ($entidadtabla) {
                foreach ($entidadtabla as $entidad) {
                    Banco::create([
                        'proveedor_id' => $proveedor->id,
                        'descripcion' => $entidad->entidad,
                        'tipo_moneda' => $entidad->moneda,
                        'num_cuenta' => $entidad->cuenta,
                        'cci' => $entidad->cci,
                        'itf' => $entidad->itf,
                    ]);
                }
            }
        }

        if($request->has('type_store')){
            if($proveedor->tipo_documento   ==  "RUC"){
                $ultimoProveedor    =   ["nroDoc"=>$proveedor->ruc,"descripcion"=>$proveedor->descripcion,"id"=>$proveedor->id];
            }
            if($proveedor->tipo_documento   ==  "DNI"){
                $ultimoProveedor    =   ["nroDoc"=>$proveedor->dni,"descripcion"=>$proveedor->descripcion,"id"=>$proveedor->id];
            }
            return response()->json(['success' => true,
            'message'=>'PROVEEDOR REGISTRADO',
            'data'=>$ultimoProveedor]);
        }
        

        //Registro de actividad
        $descripcion = "SE AGREGÓ EL PROVEEDOR CON LA DESCRIPCION: ". $proveedor->descripcion;
        $gestion = "PROVEEDORES";
        crearRegistro($proveedor, $descripcion , $gestion);

        Session::flash('success','Proveedor creado.');
        return redirect()->route('compras.proveedor.index')->with('guardar', 'success');
    }

    public function destroy($id)
    {
        
        $this->authorize('haveaccess','proveedor.index');
        $proveedor = Proveedor::findOrFail($id);
        $proveedor->estado = 'ANULADO';
        $proveedor->update();

        //Registro de actividad
        $descripcion = "SE ELIMINÓ EL PROVEEDOR CON LA DESCRIPCION: ". $proveedor->descripcion;
        $gestion = "PROVEEDORES";
        eliminarRegistro($proveedor, $descripcion , $gestion);

        Session::flash('success','Proveedor eliminado.');
        return redirect()->route('compras.proveedor.index')->with('eliminar', 'success');

    }

    public function show($id)
    {
        $this->authorize('haveaccess','proveedor.index');
        $banco = Banco::where('proveedor_id',$id)
        ->where('estado','ACTIVO')
        ->get();
        $proveedor = Proveedor::findOrFail($id);
        return view('compras.proveedores.show', [
            'proveedor' => $proveedor,
            'banco' => $banco,
        ]);

    }

    public function edit($id)
    {
        $this->authorize('haveaccess','proveedor.index');
        $proveedor = Proveedor::findOrFail($id);
        $banco = Banco::where('proveedor_id',$id)
        ->where('estado','ACTIVO')
        ->get();
        $tipos = personas();
        $zonas = zonas();
        $bancos = bancos();
        $monedas = tipos_moneda();
        return view('compras.proveedores.edit', [
            'proveedor' => $proveedor,
            'tipos' => $tipos,
            'zonas' => $zonas,
            'bancos' => $bancos, 
            'monedas' => $monedas,
            'banco' => $banco,
        ]);

    }

    public function update(Request $request, $id){
        $this->authorize('haveaccess','proveedor.index');

        $data = $request->all();
        $rules = [
            'tipo_documento' => 'required',
        ];
        $message = [
            'tipo_documento.required' => 'El campo Tipo es obligatorio.',
        ];
        Validator::make($data, $rules, $message)->validate();
        if ($request->input('tipo_documento') == 'RUC') {
            $rules = [
                'ruc' => [
                    new RequiredIf($request->input('tipo_documento') == 'RUC'),
                    'numeric',
                    'digits:11',
                    Rule::unique('proveedores','ruc')->where(function ($query) {
                        $query->whereIn('estado',["ACTIVO"])->whereNotNull('ruc');
                    })->ignore($id)
                ],
                'tipo_persona'=> 'required',
            ];
            $message = [
                'ruc.required' => 'El campo Ruc es obligatorio.',
                'ruc.unique'=> 'El campo Ruc debe de ser campo único.',
                'ruc.numeric'=> 'El campo Ruc debe se numérico.',
                'ruc.digits'=>'El campo Ruc debe tener 11 dígitos.',
                'tipo_persona.required' => 'El campo Tipo es obligatorio.',
            ];
            Validator::make($data, $rules, $message)->validate();
        }else{
            $rules = [
                'dni' => [
                    new RequiredIf($request->input('tipo_documento') == 'DNI'),
                    'numeric',
                    'digits:8',
                    Rule::unique('proveedores','dni')->where(function ($query) {
                        $query->whereIn('estado',["ACTIVO"])->whereNotNull('dni');
                    })->ignore($id)
                ],
                'tipo_persona_dni'=> 'required',
            ];
            $message = [
                'dni.digits' => 'El campo Dni debe tener 8 dígitos.',
                'dni.numeric' => 'El campo Dni debe ser numérico.',
                'dni.required' => 'El campo Dni es obligatorio.',
                'dni.unique'=> 'El campo Dni debe de ser campo único.',
                'tipo_persona_dni.required' => 'El campo Tipo es obligatorio.',
            ];
            Validator::make($data, $rules, $message)->validate();
        }
        
        $rules = [

            'descripcion' => 'required',
            'direccion' => 'required',
            'zona' => 'required',

            'telefono' => 'required',
            'celular' => 'nullable',
            'web' => 'nullable',
            'facebook' => 'nullable',
            'instagram' => 'nullable',
            
            'correo' => 'required|email',

            'contacto' => 'nullable',
            'correo_contacto' => 'nullable',
            'telefono_contacto' => 'nullable',
            'celular_contacto' => 'nullable',

            'ruc_transporte' => 'nullable',
            'transporte' => 'nullable',
            'direccion_transporte' => 'nullable',

            'direccion_almacen' => 'nullable',
            'estado' => 'required',

            'calidad' => 'nullable',
            'telefono_calidad' => 'nullable',
            'celular_calidad' => 'nullable',
            'correo_calidad' => 'nullable|email',

        ];
        
        $message = [
            'descripcion.required' => 'El campo Descripción es obligatorio.',
            'direccion.required' => 'El campo Dirección es obligatorio.',
            'zona.required' => 'El campo Zona es obligatorio.',

            'correo.required' => 'El campo Correo es obligatorio.',
            'correo.email' => 'El campo Correo debe ser email.',
            // 'telefono.numeric' => 'El campo Teléfono debe ser numérico.',
            'telefono.required' => 'El campo Teléfono es obligatorio.',

            // 'celular.numeric' => 'El campo Celular debe ser numérico.',
            // 'telefono_contacto.numeric' => 'El campo Teléfono debe ser numérico.',
            // 'celular_contacto.numeric' => 'El campo Celular debe ser numérico.',

            'estado.required' => 'El campo Estado es obligatorio.',
            // 'telefono_calidad.numeric' => 'El campo Teléfono debe ser numérico.',
            // 'celular_calidad.numeric' => 'El campo Celular debe ser numérico.',
            'correo_calidad.email' => 'El campo Correo debe ser email.',
            

        ];

        Validator::make($data, $rules, $message)->validate();

        $proveedor = Proveedor::findOrFail($id);
        $proveedor->ruc = $request->get('ruc');
        $proveedor->dni = $request->get('dni');
        $proveedor->descripcion = $request->get('descripcion');
        $proveedor->tipo_documento= $request->get('tipo_documento');

        if ($data['tipo_documento'] == "RUC") {
            $proveedor->tipo_persona = $request->get('tipo_persona');
        }else{
            $proveedor->tipo_persona = $request->get('tipo_persona_dni');
        }
        $proveedor->direccion = $request->get('direccion');
        $proveedor->correo = $request->get('correo');

        $proveedor->web = $request->get('web');
        $proveedor->facebook = $request->get('facebook');
        $proveedor->instagram = $request->get('instagram');

        $proveedor->zona = $request->get('zona');
        $proveedor->telefono = $request->get('telefono');
        $proveedor->celular = $request->get('celular');
        
        $proveedor->contacto = $request->get('contacto');
        $proveedor->celular_contacto = $request->get('celular_contacto');
        $proveedor->telefono_contacto = $request->get('telefono_contacto');
        $proveedor->correo_contacto = $request->get('correo_contacto');

        $proveedor->calidad = $request->get('calidad');
        $proveedor->celular_calidad = $request->get('celular_calidad');
        $proveedor->telefono_calidad = $request->get('telefono_calidad');
        $proveedor->correo_calidad = $request->get('correo_calidad');
        
        $proveedor->ruc_transporte = $request->get('ruc_transporte');
        $proveedor->transporte = $request->get('transporte');
        $proveedor->direccion_transporte = $request->get('direccion_transporte');
        $proveedor->direccion_almacen = $request->get('direccion_almacen');

        $proveedor->estado_transporte = $request->get('estado_transporte');
        $proveedor->estado_documento = $request->get('estado');

        $proveedor->update();

        $entidadesJSON = $request->get('entidades_tabla');
        $entidadtabla = json_decode($entidadesJSON[0]);

        if ($entidadtabla) {

            $bancos = Banco::where('proveedor_id', $proveedor->id)->get();
            foreach ($bancos as $banco) {
                $banco->estado= "ANULADO";
                $banco->update();
            }
            foreach ($entidadtabla as $entidad) {
                Banco::create([
                    'proveedor_id' => $proveedor->id,
                    'descripcion' => $entidad->entidad,
                    'tipo_moneda' => $entidad->moneda,
                    'num_cuenta' => $entidad->cuenta,
                    'cci' => $entidad->cci,
                    'itf' => $entidad->itf,
                ]);
            }

        }else{
            $bancos = Banco::where('proveedor_id', $proveedor->id)->get();
            foreach ($bancos as $banco) {
                $banco->estado= "ANULADO";
                $banco->update();
            }
        }

        //Registro de actividad
        $descripcion = "SE MODIFICÓ EL PROVEEDOR CON LA DESCRIPCION: ". $proveedor->descripcion;
        $gestion = "PROVEEDORES";
        modificarRegistro($proveedor, $descripcion , $gestion);

        Session::flash('success','Proveedor modificada.');
        return redirect()->route('compras.proveedor.index')->with('modificar', 'success');

    }
}

