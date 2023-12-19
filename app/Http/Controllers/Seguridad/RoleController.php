<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use App\Permission\Model\Permission;
use App\Permission\Model\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function index()
    {

        $this->authorize('haveaccess','role.index');
        return view('seguridad.roles.index');
    }

    public function create()
    {
        $this->authorize('haveaccess','role.index');
        $permission_role = [];
        $action = route('role.store');
        $role = new Role();
        $permissions = Permission::all();
        $ubicacion = 'Registrar';
        $title = 'REGISTRAR NUEVO ROL';
        return view('seguridad.roles.create',compact('permissions','action','role','ubicacion','permission_role','title'));
    }

    public function store(Request $request)
    {
        $this->authorize('haveaccess','role.index');
        $data = $request->all();

        $rules = [
            'name' => 'required|max:50|unique:roles,name',
            'slug' => 'required|max:50|unique:roles,slug',
            'full-access' => 'required|in:SI,NO',
            'punto-venta' => 'required|in:SI,NO'
        ];

        $message = [
            'name.required' => 'El campo nombre es obligatorio.',
            'name.unique' => 'El campo nombre debe ser único.',
            'slug.required' => 'El campo slug es obligatorio.',
            'slug.unique' => 'El campo slug debe ser único.',
            'full-access' => 'El campo full-access acepta SI/NO.',
            'punto-venta' => 'El campo punto-venta acepta SI/NO.',
        ];

        Validator::make($data, $rules, $message)->validate();
        $role = Role::create($request->all());
        $role->name = strtoupper($request->get('name'));
        $role->description = strtoupper($request->get('description'));
        $role->slug = strtoupper($request->get('slug'));
        $role->update();
        if($request->get('permission'))
        {
            $role->permissions()->sync($request->get('permission'));
        }

        //Registro de actividad
        $descripcion = "SE AGREGÓ EL ROL CON EL NOMBRE: ". $role->name;
        $gestion = "ROLES";
        crearRegistro($role, $descripcion , $gestion);

        Session::flash('success','Rol creado.');
        return redirect()->route('role.index')->with('guardar', 'success');
    }

    public function show($id)
    {
        $this->authorize('haveaccess','role.index');
        $role = Role::find($id);
        $permission_role = [];
        foreach($role->permissions as $permission)
        {
            $permission_role[] = $permission->id;
        }
        $permissions = Permission::all();
        return view('seguridad.roles.show',compact('permissions','role','permission_role'));
    }

    public function edit($id)
    {
        $this->authorize('haveaccess','role.index');
        $role = Role::find($id);
        $permission_role = [];
        foreach($role->permissions as $permission)
        {
            $permission_role[] = $permission->id;
        }
        $title = 'MODIFICAR ROL';
        $action = route('role.update',$id);
        $permissions = Permission::all();
        $ubicacion = 'Modificar';
        $put = true;
        return view('seguridad.roles.create',compact('permissions','action','role','ubicacion','permission_role','put','title'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('haveaccess','role.edit');
        $data = $request->all();
        $role = Role::find($id);
        $rules = [
            'name' => 'required|max:50|unique:roles,name,'.$role->id,
            'slug' => 'required|max:50|unique:roles,slug,'.$role->id,
            'full-access' => 'required|in:SI,NO',
            'punto-venta' => 'required|in:SI,NO',
        ];

        $message = [
            'name.required' => 'El campo nombre es obligatorio.',
            'name.unique' => 'El campo nombre debe ser único.',
            'slug.required' => 'El campo slug es obligatorio.',
            'slug.unique' => 'El campo slug debe ser único.',
            'full-access' => 'El campo full-access acepta SI/NO.',
            'punto-venta' => 'El campo punto-venta acepta SI/NO.',
        ];

        Validator::make($data, $rules, $message)->validate();
        $role->update($request->all());
        $role->name = strtoupper($request->get('name'));
        $role->description = strtoupper($request->get('description'));
        $role->slug = strtoupper($request->get('slug'));
        $role->update();
        if($request->get('permission'))
        {
            $role->permissions()->sync($request->get('permission'));
        }
        else
        {
            $role->permissions()->sync([]);
        }

        //Registro de actividad
        $descripcion = "SE MODIFICÓ EL ROL CON EL NOMBRE: ". $role->name;
        $gestion = "ROLES";
        modificarRegistro($role, $descripcion , $gestion);
        Session::flash('success','Rol Modificado.');
        return redirect()->route('role.index')->with('modificar', 'success');
    }

    public function destroy($id)
    {
        $role = Role::find($id);

        //Registro de actividad
        $descripcion = "SE ELIMINÓ EL ROL CON EL NOMBRE: ". $role->name;
        $gestion = "ROLES";
        eliminarRegistro($role, $descripcion , $gestion);

        $role->delete();
        Session::flash('success','Rol eliminado.');
        return redirect()->route('role.index')->with('eliminar', 'success');
    }

    public function getTable()
    {
        $roles = Role::all();
        return DataTables::of($roles)->make(true);
    }
}
