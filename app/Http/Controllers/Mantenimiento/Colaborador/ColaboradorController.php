<?php

namespace App\Http\Controllers\Mantenimiento\Colaborador;

use App\Http\Controllers\Controller;
use App\Mantenimiento\Colaborador\Colaborador;
use App\Mantenimiento\Persona\Persona;
use App\PersonaTrabajador;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class ColaboradorController extends Controller
{
    public function index()
    {
        $this->authorize('haveaccess','colaborador.index');
        return view('mantenimiento.colaboradores.index');
    }

    public function getTable()
    {
        $colaboradores =Colaborador::get();
        //dd($colaboradores);
        $coleccion = collect([]);
        foreach($colaboradores as $colaborador) {
            if($colaborador->persona->estado=="ACTIVO")
            {
                $coleccion->push([
                'id' => $colaborador->id,
                'documento' => $colaborador->persona->getDocumento(),
                'apellidos_nombres' => $colaborador->persona->getApellidosYNombres(),
                'telefono_movil' => $colaborador->persona->telefono_movil,
                'area' => $colaborador->getArea(),
                'cargo' =>$colaborador->getCargo(),
             ]);
            }
        }
        return DataTables::of($coleccion)->toJson();
    }

    public function create()
    {
        $this->authorize('haveaccess','colaborador.index');
        return view('mantenimiento.colaboradores.create');
    }

    public function store(Request $request)
    {
        $this->authorize('haveaccess','colaborador.index');
        $data = $request->all();
        $rules = [
            'tipo_documento' => 'required',
            'documento' => ['required', Rule::unique('personas','documento')->where(function ($query) {
                $query->whereIn('estado',["ACTIVO"]);
            })],
            'nombres' => 'required',
            'apellido_paterno' => 'required',
            'apellido_materno' => 'required',
            'fecha_nacimiento' => 'required',
            'sexo' => 'required',
        ];

        $message = [
            'tipo_documento.required' => 'El campo nombre es obligatorio.',
            'documento.unique' => 'Ya existe una persona (vendedor o colaborador) con este documento.',
            'nombres.required' => 'El campo nombres es obligatorio.',
            'apellido_paterno.required' => 'El campo apellido paterno es obligatorio.',
            'apellido_materno.required' => 'El campo apellido materno es obligatorio.',
            'fecha_nacimiento.required' => 'El campo fecha de nacimiento es obligatorio.',
            'sexo.required' => 'El campo sexo es obligatorio.',
        ];

        Validator::make($data, $rules, $message)->validate();
        $persona = new Persona();
        $persona->tipo_documento = $request->get('tipo_documento');
        $persona->documento = $request->get('documento');
        $persona->codigo_verificacion = $request->get('codigo_verificacion');
        $persona->nombres = $request->get('nombres');
        $persona->apellido_paterno = $request->get('apellido_paterno');
        $persona->apellido_materno = $request->get('apellido_materno');
        $persona->fecha_nacimiento = $request->get('fecha_nacimiento');
        $persona->sexo = $request->get('sexo');
        $persona->estado_civil = $request->get('estado_civil');
        $persona->departamento_id = str_pad($request->get('departamento'), 2, "0", STR_PAD_LEFT);
        $persona->provincia_id = str_pad($request->get('provincia'), 4, "0", STR_PAD_LEFT);
        $persona->distrito_id = str_pad($request->get('distrito'), 6, "0", STR_PAD_LEFT);
        $persona->direccion = $request->get('direccion');
        $persona->correo_electronico = $request->get('correo_electronico');
        $persona->telefono_movil = $request->get('telefono_movil');
        $persona->telefono_fijo = $request->get('telefono_fijo');
        $persona->correo_corporativo= $request->get('correo_corporativo');
        $persona->telefono_trabajo= $request->get('telefono_trabajo');
        $persona->estado_documento = $request->get('estado_documento');
        $persona->save();

        $colaborador = new Colaborador();
        $colaborador->persona_id = $persona->id;
        $colaborador->area = $request->get('area');
        $colaborador->profesion = $request->get('profesion');
        $colaborador->cargo = $request->get('cargo');
        $colaborador->telefono_referencia = $request->get('telefono_referencia');
        $colaborador->contacto_referencia = $request->get('contacto_referencia');
        $colaborador->grupo_sanguineo = $request->get('grupo_sanguineo');
        $colaborador->alergias = $request->get('alergias');
        $colaborador->numero_hijos = $request->get('numero_hijos');
        $colaborador->sueldo = $request->get('sueldo');
        $colaborador->sueldo_bruto = $request->get('sueldo_bruto');
        $colaborador->sueldo_neto = $request->get('sueldo_neto');
        $colaborador->moneda_sueldo = $request->get('moneda_sueldo');
        $colaborador->tipo_banco = $request->get('tipo_banco');
        $colaborador->numero_cuenta = $request->get('numero_cuenta');

        if($request->hasFile('imagen')){
            $file = $request->file('imagen');
            $name = $file->getClientOriginalName();
            $colaborador->nombre_imagen = $name;
            $colaborador->ruta_imagen = $request->file('imagen')->store('public/colaboradores/imagenes');
        }

        $colaborador->fecha_inicio_actividad = $request->get('fecha_inicio_actividad');
        $colaborador->fecha_fin_actividad = $request->get('fecha_fin_actividad');
        $colaborador->fecha_inicio_planilla = $request->get('fecha_inicio_planilla');
        $colaborador->fecha_fin_planilla = $request->get('fecha_fin_planilla');
        $colaborador->save();
        //Registro de actividad
        $descripcion = "SE AGREGÓ EL colaborador CON EL NOMBRE: ". $colaborador->persona->nombres.' '.$colaborador->persona->apellido_paterno.' '.$colaborador->persona->apellido_materno;
        $gestion = "colaboradores";
        crearRegistro($colaborador, $descripcion , $gestion);



        Session::flash('success','Colaborador creado.');
        return redirect()->route('mantenimiento.colaborador.index')->with('guardar', 'success');
    }

    public function edit($id)
    {
        $this->authorize('haveaccess','colaborador.index');
        $colaborador = Colaborador::findOrFail($id);
        return view('mantenimiento.colaboradores.edit', [
            'colaborador' => $colaborador
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('haveaccess','colaborador.index');
        $data = $request->all();
        $colaborador = Colaborador::findOrFail($id);
        $rules = [
            'tipo_documento' => 'required',
            'documento' => ['required', Rule::unique('personas','documento')->where(function ($query) {
                $query->whereIn('estado',["ACTIVO"]);
            })->ignore($colaborador->persona->id)],
            'nombres' => 'required',
            'apellido_paterno' => 'required',
            'apellido_materno' => 'required',
            'fecha_nacimiento' => 'required',
            'sexo' => 'required',
        ];

        $message = [
            'tipo_documento.required' => 'El campo nombre es obligatorio.',
            'documento.unique' => 'Ya existe una persona (vendedor o colaborador) con este documento.',
            'nombres.required' => 'El campo nombres es obligatorio.',
            'apellido_paterno.required' => 'El campo apellido paterno es obligatorio.',
            'apellido_materno.required' => 'El campo apellido materno es obligatorio.',
            'fecha_nacimiento.required' => 'El campo fecha de nacimiento es obligatorio.',
            'sexo.required' => 'El campo sexo es obligatorio.',
        ];

        Validator::make($data, $rules, $message)->validate();

        $persona =  $colaborador->persona;
        $persona->tipo_documento = $request->get('tipo_documento');
        $persona->documento = $request->get('documento');
        $persona->codigo_verificacion = $request->get('codigo_verificacion');
        $persona->nombres = $request->get('nombres');
        $persona->apellido_paterno = $request->get('apellido_paterno');
        $persona->apellido_materno = $request->get('apellido_materno');
        $persona->fecha_nacimiento = $request->get('fecha_nacimiento');
        $persona->sexo = $request->get('sexo');
        $persona->estado_civil = $request->get('estado_civil');
        $persona->departamento_id = str_pad($request->get('departamento'), 2, "0", STR_PAD_LEFT);
        $persona->provincia_id = str_pad($request->get('provincia'), 4, "0", STR_PAD_LEFT);
        $persona->distrito_id = str_pad($request->get('distrito'), 6, "0", STR_PAD_LEFT);
        $persona->direccion = $request->get('direccion');
        $persona->correo_electronico = $request->get('correo_electronico');
        $persona->telefono_movil = $request->get('telefono_movil');
        $persona->telefono_fijo = $request->get('telefono_fijo');
        $persona->correo_corporativo= $request->get('correo_corporativo');
        $persona->telefono_trabajo= $request->get('telefono_trabajo');
        $persona->estado_documento = $request->get('estado_documento');
        $persona->update();


        $colaborador->area = $request->get('area');
        $colaborador->profesion = $request->get('profesion');
        $colaborador->cargo = $request->get('cargo');
        $colaborador->telefono_referencia = $request->get('telefono_referencia');
        $colaborador->contacto_referencia = $request->get('contacto_referencia');
        $colaborador->grupo_sanguineo = $request->get('grupo_sanguineo');
        $colaborador->alergias = $request->get('alergias');
        $colaborador->numero_hijos = $request->get('numero_hijos');
        $colaborador->sueldo = $request->get('sueldo');
        $colaborador->sueldo_bruto = $request->get('sueldo_bruto');
        $colaborador->sueldo_neto = $request->get('sueldo_neto');
        $colaborador->moneda_sueldo = $request->get('moneda_sueldo');
        $colaborador->tipo_banco = $request->get('tipo_banco');
        $colaborador->numero_cuenta = $request->get('numero_cuenta');

        if($request->hasFile('imagen')){
            //Eliminar Archivo anterior
            Storage::delete($colaborador->ruta_imagen);
            //Agregar nuevo archivo
            $file = $request->file('imagen');
            $name = $file->getClientOriginalName();
            $colaborador->nombre_imagen = $name;
            $colaborador->ruta_imagen = $request->file('imagen')->store('public/colaboradores/imagenes');
        }else{
            if ($colaborador->ruta_imagen) {
                //Eliminar Archivo anterior
                Storage::delete($colaborador->ruta_imagen);
                $colaborador->nombre_imagen = '';
                $colaborador->ruta_imagen = '';
            }
        }

        $colaborador->fecha_inicio_actividad = $request->get('fecha_inicio_actividad');
        $colaborador->fecha_fin_actividad = $request->get('fecha_fin_actividad');
        $colaborador->fecha_inicio_planilla = $request->get('fecha_inicio_planilla');
        $colaborador->fecha_fin_planilla = $request->get('fecha_fin_planilla');
        $colaborador->update();
        //Registro de actividad
        $descripcion = "SE MODIFICÓ EL colaborador CON EL NOMBRE: ". $colaborador->persona->nombres.' '.$colaborador->persona->apellido_paterno.' '.$colaborador->persona->apellido_materno;
        $gestion = "colaboradores";
        modificarRegistro($colaborador, $descripcion , $gestion);



        Session::flash('success','Colaborador modificado.');
        return redirect()->route('mantenimiento.colaborador.index')->with('modificar', 'success');
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
        DB::transaction(function() use ($id) {

            $colaborador= Colaborador::findOrFail($id);
            $colaborador->estado = 'ANULADO';
            $colaborador->update();
            $persona=$colaborador->persona;
            $persona->estado = 'ANULADO';
            $persona->update();

            //Registro de actividad
            $descripcion = "SE ELIMINÓ EL COLABORADOR CON EL NOMBRE: ". $colaborador->persona->nombres.' '.$colaborador->persona->apellido_paterno.' '.$colaborador->persona->apellido_materno;
            $gestion = "colaboradores";
            eliminarRegistro($colaborador, $descripcion , $gestion);

        });



        Session::flash('success','Colaborador eliminado.');
        return redirect()->route('mantenimiento.colaborador.index')->with('eliminar', 'success');
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
}