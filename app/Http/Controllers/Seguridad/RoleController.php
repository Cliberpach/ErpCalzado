<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seguridad\Roles\RoleUpdateRequest;
use App\Http\Requests\Seguridad\Roles\RolStoreRequest;
use App\Permission\Model\Permission;
use App\Permission\Model\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function index()
    {
        $this->authorize('haveaccess', 'seguridad.role.index');
        return view('seguridad.roles.index');
    }

    public function create()
    {
        $this->authorize('haveaccess', 'seguridad.role.index');
        $permission_role = [];
        $role = new Role();
        $permissions = Permission::all();
        return view('seguridad.roles.create', compact('permissions', 'role', 'permission_role'));
    }

    /*
array:7 [
  "_token" => "EFBcg0A34InqzxLtDbBDbfDCZy33Bn28PdFtJ1Fq"
  "name" => "test"
  "slug" => "test"
  "description" => "test"
  "full-access" => "NO"
  "punto-venta" => "NO"
  "permissions" => "["2","8"]"
]
*/
    public function store(RolStoreRequest $request)
    {
        $this->authorize('haveaccess', 'seguridad.role.index');
        DB::beginTransaction();
        try {

            $role               = new Role();
            $role->name         = mb_strtoupper(trim($request->get('name')), 'UTF-8');
            $role->description  = mb_strtoupper(trim($request->get('description')), 'UTF-8');
            $role->slug         = mb_strtoupper(trim($request->get('slug')), 'UTF-8');
            $role->{'full-access'} = $request->get('full-access');
            $role->{'punto-venta'} = $request->get('punto-venta');
            $role->save();

            //========================
            // PERMISOS
            //========================
            if ($request->get('full-access') === 'NO') {

                $permissions = json_decode(
                    $request->permissions,
                    true
                );

                $role->permissions()->sync($permissions ?? []);
            } else {
                $role->permissions()->sync([]);
            }

            DB::commit();
            //Registro de actividad
            $descripcion = "SE AGREGÓ EL ROL CON EL NOMBRE: " . $role->name;
            $gestion = "ROLES";
            crearRegistro($role, $descripcion, $gestion);

            return response()->json(['success' => true, 'message' => 'ROL REGISTRADO CON ÉXITO']);
        } catch (Throwable $th) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }

    public function show($id)
    {
        $this->authorize('haveaccess', 'seguridad.role.index');

        try {

            $role = Role::with('permissions')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Datos del rol obtenidos',
                'data' => [
                    'id'            => $role->id,
                    'name'          => $role->name,
                    'slug'          => $role->slug,
                    'description'   => $role->description,
                    'full_access'   => $role->{'full-access'},
                    'punto_venta'   => $role->{'punto-venta'},
                    'permissions'   => $role->permissions->map(function ($permission) {

                        $parts = explode('.', $permission->slug);

                        return [
                            'id'         => $permission->id,
                            'name'       => $permission->name,
                            'slug'       => $permission->slug,
                            'modulo'     => mb_strtoupper($parts[0] ?? '-', 'UTF-8'),
                            'submodulo'  => mb_strtoupper($parts[1] ?? '-', 'UTF-8'),
                        ];
                    })
                ]
            ]);
        } catch (Throwable $th) {

            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'line'    => $th->getLine(),
                'file'    => $th->getFile()
            ]);
        }
    }

    public function edit($id)
    {
        $this->authorize('haveaccess', 'seguridad.role.index');
        $role = Role::findOrFail($id);
        $permission_role = [];
        foreach ($role->permissions as $permission) {
            $permission_role[] = $permission->id;
        }
        $permissions = Permission::all();
        return view('seguridad.roles.edit', compact(
            'permissions',
            'role',
            'permission_role',
        ));
    }

    /*
array:8 [
  "_token" => "EFBcg0A34InqzxLtDbBDbfDCZy33Bn28PdFtJ1Fq"
  "name" => "USER"
  "slug" => "USER"
  "description" => "USER"
  "full-access" => "NO"
  "punto-venta" => "NO"
  "permissions" => "["3","5"]"
  "_method" => "PUT"
]
*/
    public function update(RoleUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        $this->authorize('haveaccess', 'seguridad.role.edit');
        try {

            $role               = Role::findOrFail($id);
            $role->name         = mb_strtoupper(trim($request->get('name')), 'UTF-8');
            $role->description  = mb_strtoupper(trim($request->get('description')), 'UTF-8');
            $role->slug         = mb_strtoupper(trim($request->get('slug')), 'UTF-8');
            $role->{'full-access'} = $request->get('full-access');
            $role->{'punto-venta'} = $request->get('punto-venta');
            $role->save();

            //========================================
            // PERMISOS
            //========================================
            if ($request->get('full-access') === 'NO') {
                $permissions = json_decode(
                    $request->get('permissions'),
                    true
                );
                $role->permissions()->sync($permissions ?? []);
            } else {
                $role->permissions()->sync([]);
            }

            DB::commit();

            //Registro de actividad
            $descripcion = "SE MODIFICÓ EL ROL CON EL NOMBRE: " . $role->name;
            $gestion = "ROLES";
            modificarRegistro($role, $descripcion, $gestion);

            return response()->json(['success' => true, 'message' => 'ROL ACTUALIZADO CON ÉXITO']);
        } catch (Throwable $th) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }

    public function destroy($id)
    {

        $role = Role::findOrFail($id);

        //Registro de actividad
        $descripcion = "SE ELIMINÓ EL ROL CON EL NOMBRE: " . $role->name;
        $gestion = "ROLES";
        eliminarRegistro($role, $descripcion, $gestion);

        $role->delete();
        Session::flash('success', 'Rol eliminado.');
        return redirect()->route('seguridad.role.index')->with('eliminar', 'success');
    }

    public function getTable()
    {
        $roles = Role::all();
        return DataTables::of($roles)->make(true);
    }
}
