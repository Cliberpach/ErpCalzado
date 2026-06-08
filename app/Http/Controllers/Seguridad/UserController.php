<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seguridad\Users\UserStoreRequest;
use App\Http\Requests\Seguridad\Users\UserUpdateRequest;
use App\Http\Services\Seguridad\User\UserManager;
use App\Mantenimiento\Persona\Persona;
use App\Permission\Model\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    private UserManager $s_manager;

    public function __construct()
    {
        $this->s_manager = new UserManager();
    }

    public function index()
    {
        $this->authorize('haveaccess', 'seguridad.user.index');
        return view('seguridad.users.index');
    }

    public function getUsuarios()
    {
        $users = User::with(['sede', 'colaborador', 'roles'])
            ->where('estado', 'ACTIVO')
            ->select('users.*');

        return DataTables::of($users)
            ->addColumn('sede_nombre', function ($u) {
                return $u->sede ? $u->sede->nombre : '-';
            })
            ->addColumn('colaborador_nombre', function ($u) {
                return $u->colaborador ? $u->colaborador->nombre : 'NO ASIGNADO';
            })
            ->addColumn('roles_html', function ($u) {
                return $u->roles->map(function ($r) {
                    return '<span class="badge badge-info">' . $r->name . '</span>';
                })->implode(' ');
            })
            ->rawColumns(['roles_html'])
            ->make(true);
    }

    public function create()
    {
        $this->authorize('haveaccess', 'seguridad.user.index');

        $sede_id = Auth::user()->sede_id;

        $colaboradores = DB::table('colaboradores as c')
            ->join('empresa_sedes as es', 'es.id', 'c.sede_id')
            ->select('c.*', 'es.nombre as sede_nombre')
            ->where('c.estado', 'ACTIVO')
            ->whereNotIn('c.id', function ($query) {
                $query->select('u.colaborador_id')
                    ->from('users as u')
                    ->where('u.estado', 'ACTIVO');
            })
            ->get();

        $roles = Role::all();

        return view('seguridad.users.create', compact('roles', 'colaboradores', 'sede_id'));
    }

    public function store(UserStoreRequest $request)
    {
        $this->authorize('haveaccess', 'seguridad.user.index');
        DB::beginTransaction();
        try {
            $user = $this->s_manager->store($request->all());

            $descripcion = 'SE AGREGÓ EL USUARIO CON EL NOMBRE: ' . $user->usuario;
            $gestion     = 'USUARIOS';
            crearRegistro($user, $descripcion, $gestion);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'USUARIO REGISTRADO CON ÉXITO']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function show(int $id)
    {
        $this->authorize('haveaccess', 'seguridad.user.index');

        $user = User::find($id);

        $auxs = Persona::where('estado', 'ACTIVO')->get();

        $colaboradores = array();
        foreach ($auxs as $aux) {
            if (!$aux->user_persona) {
                $colaborador = new stdClass();
                $colaborador->id = $aux->id;
                $colaborador->colaborador = $aux->getApellidosYNombres();
                $colaborador->area = 'SIN ÁREA';

                array_push($colaboradores, $colaborador);
            } else {
                if (!empty($user->colaborador['persona_id'])) {
                    if ($aux->id == $user->colaborador['persona_id']) {
                        $colaborador = new stdClass();
                        $colaborador->id = $aux->id;
                        $colaborador->colaborador = $aux->getApellidosYNombres();
                        $colaborador->area = 'SIN ÁREA';

                        array_push($colaboradores, $colaborador);
                    }
                }
            }
        }

        $role_user = [];
        $roles = Role::all();

        foreach ($user->roles as $role) {
            $role_user[] = $role->id;
        }

        return view('seguridad.users.show', compact('roles', 'role_user', 'user', 'colaboradores'));
    }

    public function edit(int $id)
    {
        $this->authorize('haveaccess', 'seguridad.user.index');

        $user    = User::find($id);
        $sede_id = Auth::user()->sede_id;

        $colaboradores = DB::table('colaboradores as c')
            ->join('empresa_sedes as es', 'es.id', '=', 'c.sede_id')
            ->select('c.*', 'es.nombre as sede_nombre')
            ->where('c.estado', 'ACTIVO')
            ->whereNotIn('c.id', function ($query) use ($id) {
                $query->select('u.colaborador_id')
                    ->from('users as u')
                    ->where('u.id', '!=', $id)
                    ->where('u.estado', 'ACTIVO');
            })
            ->get();

        $role_user = [];
        $roles = Role::all();

        foreach ($user->roles as $role) {
            $role_user[] = $role->id;
        }

        return view('seguridad.users.edit', compact('roles', 'role_user', 'user', 'colaboradores', 'sede_id'));
    }

    public function update(UserUpdateRequest $request, int $id)
    {
        $this->authorize('haveaccess', 'seguridad.user.index');
        DB::beginTransaction();
        try {
            $user = $this->s_manager->update($id, $request->all());

            $descripcion = 'SE MODIFICÓ EL USUARIO CON EL NOMBRE: ' . $user->usuario;
            $gestion     = 'USUARIOS';
            modificarRegistro($user, $descripcion, $gestion);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'USUARIO MODIFICADO CON ÉXITO']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function destroy(int $id)
    {
        $this->authorize('haveaccess', 'seguridad.user.index');
        DB::beginTransaction();
        try {
            $user = $this->s_manager->destroy((int) $id);

            $descripcion = 'SE ELIMINÓ EL USUARIO CON EL NOMBRE: ' . $user->usuario;
            $gestion     = 'USUARIOS';
            eliminarRegistro($user, $descripcion, $gestion);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'USUARIO ELIMINADO CON ÉXITO']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function getUsers(Request $request)
    {
        try {
            $users = User::where('estado', 'ACTIVO')->where('sede_id', $request->get('sede_id'))->get();
            return response()->json(['success' => true, 'message' => 'Usuarios obtenidos', 'data' => $users]);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }
}
