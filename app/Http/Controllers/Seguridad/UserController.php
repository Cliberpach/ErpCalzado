<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use App\Mantenimiento\Colaborador\Colaborador;
use App\Mantenimiento\Persona\Persona;
use App\Mantenimiento\Sedes\Sede;
use App\Permission\Model\Role;
use App\User;
use App\UserPersona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use stdClass;

class UserController extends Controller
{
    public function index()
    {
        $this->authorize('haveaccess','user.index');

        $users = User::with('sede')
                ->where('estado', 'ACTIVO')
                ->get();


        return view('seguridad.users.index',compact('users'));
    }

    public function create()
    {
        $this->authorize('haveaccess','user.create');

        $auxs   =   Persona::where('estado','ACTIVO')->get();

        $sede_id            =   Auth::user()->sede_id;
        $colaboradores      =   DB::table('colaboradores as c')
                                ->join('empresa_sedes as es','es.id','c.sede_id')
                                ->select('c.*','es.nombre as sede_nombre')
                                ->where('c.estado','ACTIVO')
                                ->get();

        $role_user = [];

        $roles = Role::all();

        return view('seguridad.users.create',compact('roles','colaboradores','sede_id'));
    }


/*
array:8 [▼
  "_token" => "uA8DojLYx1bnlHUStJrLNeX1e0TYsd5OkhYgNM2o"
  "usuario" => "luisdaniel"
  "email" => "ld@gmail.com"
  "colaborador_id" => "6"
  "password" => "123456789"
  "confirm_password" => "123456789"
  "sede" => "2"
  "role" => array:2 [▼
    0 => "1"
    1 => "3"
  ]
]
*/ 
    public function store(Request $request)
    {
        $data = $request->all();
      
        $rules = [
            'usuario' => 'required',
            'colaborador_id' => 'required',
            'email' => ['required', Rule::unique('users','email')->where(function ($query) {
                $query->whereIn('estado',["ACTIVO","ANULADO"]);
            })],
            'password' => 'required'
        ];
        $message = [
            'usuario.required' => 'El campo usuario es obligatorio.',
            'colaborador_id.required' => 'El campo colaborador es obligatorio.',
            'email.required' => 'El campo email es obligatorio',
            'email.unique' => 'El campo email debe ser único',
            'password.required' => 'El campo contraseña  es obligatorio'

        ];

        Validator::make($data, $rules, $message)->validate();
        $arrayDatos = $request->all();


        if($request->password !== $request->confirm_password)
        {
            //Session::flash('success','Usuario creado.');
            return back()->with([
                'password' => $request->password ,
                'confirm_password' => $request->confirm_password,
                'mpassword' => 'Contraseñas distintas',
                'usuario' => $request->get('usuario'),
                'colaborador_id' => $request->get('colaborador_id'),
                'email' => $request->get('email'),
                'role' => $request->get('role'),
            ]);
        }

        $colaborador            =   Colaborador::find($request->get('colaborador_id'));

        $user                   =   new User();
        $password               =   strtoupper($request->password);

        $user->usuario          =   strtoupper($request->get('usuario'));
        $user->email            =   strtoupper($request->get('email'));
        $user->password         =   bcrypt($password);
        $user->contra           =   $password;
        $user->sede_id          =   $colaborador->sede_id;
        $user->colaborador_id   =   $request->get('colaborador_id');
        $user->save();

        /*
            $user_persona               = new UserPersona();
            $user_persona->user_id      = $user->id;
            $user_persona->persona_id   = $request->get('colaborador_id');
            $user_persona->save();
        */

        if($request->get('role'))
        {
            $user->roles()->sync($request->get('role'));
        }
        else
        {
            $user->roles()->sync([]);
        }

        //Registro de actividad
        $descripcion = "SE AGREGÓ EL USUARIO CON EL NOMBRE: ". $user->usuario;
        $gestion = "CLIENTES";
        crearRegistro($user, $descripcion , $gestion);

        Session::flash('success','Usuario creado.');
        return redirect()->route('user.index')->with('guardar', 'success');
    }

    public function show($id)
    {
        $user = User::find($id);

        $this->authorize('view',[$user,['user.show','userown.show']]);

        $auxs = Persona::where('estado','ACTIVO')->get();

        $colaboradores = array();
        foreach($auxs as $aux)
        {
            if(!$aux->user_persona)
            {
                $colaborador = new stdClass();
                $colaborador->id = $aux->id;
                $colaborador->colaborador = $aux->getApellidosYNombres();
                $colaborador->area = 'SIN ÁREA';

                array_push($colaboradores,$colaborador);
            }
            else
            {
                if(!empty($user->colaborador['persona_id']))
                {
                    if($aux->id == $user->colaborador['persona_id'])
                    {
                        $colaborador = new stdClass();
                        $colaborador->id = $aux->id;
                        $colaborador->colaborador = $aux->getApellidosYNombres();
                        $colaborador->area = 'SIN ÁREA';

                        array_push($colaboradores,$colaborador);
                    }
                }
            }
        }

        $role_user = [];

        $roles = Role::all();

        foreach($user->roles as $role)
        {
            $role_user[] = $role->id;
        }

        return view('seguridad.users.show',compact('roles','role_user','user','colaboradores'));
    }

    public function edit($id)
    {
        $user   =   User::find($id);

        $this->authorize('view',[$user,['user.edit','userown.edit']]);

        $auxs = Persona::where('estado','ACTIVO')->get();

        $sede_id            =   Auth::user()->sede_id;
        $colaboradores      =   DB::table('colaboradores as c')
                                ->join('empresa_sedes as es','es.id','c.sede_id')
                                ->select('c.*','es.nombre as sede_nombre')
                                ->where('c.estado','ACTIVO')
                                ->get();

        $role_user = [];

        $roles = Role::all();

        foreach($user->roles as $role)
        {
            $role_user[] = $role->id;
        }

        return view('seguridad.users.edit',
        compact('roles','role_user','user','colaboradores','sede_id'));
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        $this->authorize('view',[$user,['user.edit','userown.edit']]);

        $data = $request->all();

        $rules = [
            'usuario' => ['required', Rule::unique('users','usuario')->where(function ($query) {
                $query->whereIn('estado',["ACTIVO","ANULADO"]);
            })->ignore($id)],
            'colaborador_id' => 'required',
            'email' => ['required', Rule::unique('users','email')->where(function ($query) {
                $query->whereIn('estado',["ACTIVO","ANULADO"]);
            })->ignore($id)],
            'password' => 'required'
        ];
        $message = [
            'usuario.required' => 'El campo usuario es obligatorio.',
            'colaborador_id.required' => 'El campo colaborador es obligatorio.',
            'email.required' => 'El campo email es obligatorio',
            'email.unique' => 'El campo email debe ser único',
            'password.required' => 'El campo contraseña  es obligatorio'

        ];

        Validator::make($data, $rules, $message)->validate();

        if($request->password !== $request->confirm_password)
        {
            //Session::flash('success','Usuario creado.');
            return back()->with([
                'password' => $request->password ,
                'confirm_password' => $request->confirm_password,
                'mpassword' => 'Contraseñas distintas',
                'usuario' => $request->get('usuario'),
                'colaborador_id' => $request->get('colaborador_id'),
                'email' => $request->get('email'),
                'role' => $request->get('role'),
            ]);
        }

        $colaborador            =   Colaborador::find($request->get('colaborador_id'));

        $password = strtoupper($request->password);

        $user->usuario          =   strtoupper($request->usuario);
        $user->email            =   strtoupper($request->email);
        $user->password         =   bcrypt($password);
        $user->contra           =   $password;
        $user->sede_id          =   $colaborador->sede_id;
        $user->colaborador_id   =   $request->get('colaborador_id');
        $user->update();

        /*
            $user_persona               = UserPersona::find($user->user->id);
            $user_persona->user_id      = $user->id;
            $user_persona->persona_id   = $request->get('colaborador_id');
            $user_persona->update();
        */

        if($request->get('role'))
        {
            $user->roles()->sync($request->get('role'));
        }
        else
        {
            $user->roles()->sync([]);
        }

        //Registro de actividad
        $descripcion = "SE MODIFICÓ EL USUARIO CON EL NOMBRE: ". $user->usuario;
        $gestion = "USUARIOS";
        modificarRegistro($user, $descripcion , $gestion);
        Session::flash('success','Usuario Modificado.');
        return redirect()->route('user.index')->with('modificar', 'success');
    }

    public function destroy($id)
    {
        $this->authorize('haveaccess','user.delete');
        $user = User::find($id);
        //Registro de actividad
        $descripcion = "SE ELIMINÓ EL USUARIO CON EL NOMBRE: ". $user->usuario;
        $gestion = "USUARIOS";
        eliminarRegistro($user, $descripcion , $gestion);

        $user->estado = 'ANULADO';
        $user->update();
        Session::flash('success','Usuario eliminado');
        return redirect()->route('user.index')->with('eliminar', 'success');
    }

}
