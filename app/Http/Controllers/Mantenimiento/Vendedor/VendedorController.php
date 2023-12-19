<?php

namespace App\Http\Controllers\Mantenimiento\Vendedor;

use App\Http\Controllers\Controller;
use App\Mantenimiento\Persona\Persona;
use App\Mantenimiento\Persona\PersonaVendedor;
use App\Mantenimiento\Vendedor\Vendedor;
use App\PersonaTrabajador;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class VendedorController extends Controller
{
    public function index()
    {
        $this->authorize('haveaccess','vendedor.index');
        return view('mantenimiento.vendedores.index');
    }

    public function getTable()
    {
        $vendedores = Vendedor::all();
        $coleccion = collect([]);
        foreach($vendedores as $vendedor) {
            if($vendedor->persona->estado == "ACTIVO")
            {
                $coleccion->push([
                    'id' => $vendedor->id,
                    'documento' => $vendedor->persona->getDocumento(),
                    'apellidos_nombres' => $vendedor->persona->getApellidosYNombres(),
                    'telefono_movil' => $vendedor->persona->telefono_movil,
                    'area' => $vendedor->getArea(),
                    'cargo' =>$vendedor->getCargo(),
                ]);
            }

        }
        return DataTables::of($coleccion)->toJson();
    }

    public function create()
    {
        $this->authorize('haveaccess','vendedor.index');
        return view('mantenimiento.vendedores.create');
    }

    public function store(Request $request)
    {
        $this->authorize('haveaccess','vendedor.index');
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

        $vendedor = new Vendedor();
        $vendedor->persona_id = $persona->id;
        $vendedor->area = $request->get('area');
        $vendedor->profesion = $request->get('profesion');
        $vendedor->cargo = $request->get('cargo');
        $vendedor->telefono_referencia = $request->get('telefono_referencia');
        $vendedor->contacto_referencia = $request->get('contacto_referencia');
        $vendedor->grupo_sanguineo = $request->get('grupo_sanguineo');
        $vendedor->alergias = $request->get('alergias');
        $vendedor->numero_hijos = $request->get('numero_hijos');
        $vendedor->sueldo = $request->get('sueldo');
        $vendedor->sueldo_bruto = $request->get('sueldo_bruto');
        $vendedor->sueldo_neto = $request->get('sueldo_neto');
        $vendedor->moneda_sueldo = $request->get('moneda_sueldo');
        $vendedor->tipo_banco = $request->get('tipo_banco');
        $vendedor->numero_cuenta = $request->get('numero_cuenta');

        if($request->hasFile('imagen')){
            $file = $request->file('imagen');
            $name = $file->getClientOriginalName();
            $vendedor->nombre_imagen = $name;
            $vendedor->ruta_imagen = $request->file('imagen')->store('public/colaboradores/imagenes');
        }

        $vendedor->fecha_inicio_actividad = $request->get('fecha_inicio_actividad');
        $vendedor->fecha_fin_actividad = $request->get('fecha_fin_actividad');
        $vendedor->fecha_inicio_planilla = $request->get('fecha_inicio_planilla');
        $vendedor->fecha_fin_planilla = $request->get('fecha_fin_planilla');
        $vendedor->save();
        //Registro de actividad
        $descripcion = "SE AGREGÓ EL VENDEDOR CON EL NOMBRE: ". $vendedor->persona->nombres.' '.$vendedor->persona->apellido_paterno.' '.$vendedor->persona->apellido_materno;
        $gestion = "vendedores";
        crearRegistro($vendedor, $descripcion , $gestion);



        Session::flash('success', 'Vendedor creado.');
        return redirect()->route('mantenimiento.vendedor.index')->with('guardar', 'success');
    }

    public function edit($id)
    {
        $this->authorize('haveaccess','vendedor.index');
        $vendedor = Vendedor::findOrFail($id);
        return view('mantenimiento.vendedores.edit', [
            'vendedor' => $vendedor
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('haveaccess','vendedor.index');
        $data = $request->all();

        $vendedor = Vendedor::findOrFail($id);
        $rules = [
            'tipo_documento' => 'required',
            'documento' => ['required', Rule::unique('personas','documento')->where(function ($query) {
                $query->whereIn('estado',["ACTIVO"]);
            })->ignore($vendedor->persona->id)],
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
        
        $persona =  $vendedor->persona;
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


        $vendedor->area = $request->get('area');
        $vendedor->profesion = $request->get('profesion');
        $vendedor->cargo = $request->get('cargo');
        $vendedor->telefono_referencia = $request->get('telefono_referencia');
        $vendedor->contacto_referencia = $request->get('contacto_referencia');
        $vendedor->grupo_sanguineo = $request->get('grupo_sanguineo');
        $vendedor->alergias = $request->get('alergias');
        $vendedor->numero_hijos = $request->get('numero_hijos');
        $vendedor->sueldo = $request->get('sueldo');
        $vendedor->sueldo_bruto = $request->get('sueldo_bruto');
        $vendedor->sueldo_neto = $request->get('sueldo_neto');
        $vendedor->moneda_sueldo = $request->get('moneda_sueldo');
        $vendedor->tipo_banco = $request->get('tipo_banco');
        $vendedor->numero_cuenta = $request->get('numero_cuenta');

        if($request->hasFile('imagen')){
            //Eliminar Archivo anterior
            Storage::delete($vendedor->ruta_imagen);
            //Agregar nuevo archivo
            $file = $request->file('imagen');
            $name = $file->getClientOriginalName();
            $vendedor->nombre_imagen = $name;
            $vendedor->ruta_imagen = $request->file('imagen')->store('public/vendedores/imagenes');
        }else{
            if ($vendedor->ruta_imagen) {
                //Eliminar Archivo anterior
                Storage::delete($vendedor->ruta_imagen);
                $vendedor->nombre_imagen = '';
                $vendedor->ruta_imagen = '';
            }
        }

        $vendedor->fecha_inicio_actividad = $request->get('fecha_inicio_actividad');
        $vendedor->fecha_fin_actividad = $request->get('fecha_fin_actividad');
        $vendedor->fecha_inicio_planilla = $request->get('fecha_inicio_planilla');
        $vendedor->fecha_fin_planilla = $request->get('fecha_fin_planilla');
        $vendedor->update();
        //Registro de actividad
        $descripcion = "SE MODIFICÓ EL VENDEDOR CON EL NOMBRE: ". $vendedor->persona->nombres.' '.$vendedor->persona->apellido_paterno.' '.$vendedor->persona->apellido_materno;
        $gestion = "vendedores";
        modificarRegistro($vendedor, $descripcion , $gestion);



        Session::flash('success', 'Vendedor modificado.');
        return redirect()->route('mantenimiento.vendedor.index')->with('modificar', 'success');
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
        $this->authorize('haveaccess','vendedor.index');
        DB::transaction(function () use ($id) {

            $vendedor = Vendedor::findOrFail($id);
            $persona = $vendedor->persona;
            $persona->estado = 'ANULADO';
            $persona->update();

            //Registro de actividad
            $descripcion = "SE ELIMINÓ EL VENDEDOR CON EL NOMBRE: ". $vendedor->persona->nombres.' '.$vendedor->persona->apellido_paterno.' '.$vendedor->persona->apellido_materno;
            $gestion = "vendedores";
            eliminarRegistro($vendedor, $descripcion , $gestion);
        });




        Session::flash('success', 'Vendedor eliminado.');
        return redirect()->route('mantenimiento.vendedor.index')->with('eliminar', 'success');
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
