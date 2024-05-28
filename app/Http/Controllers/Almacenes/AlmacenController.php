<?php

namespace App\Http\Controllers\Almacenes;

use App\Almacenes\Almacen;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AlmacenController extends Controller
{
    public function index()
    {
        $this->authorize('haveaccess','almacen.index');
        return view('almacenes.almacen.index');
    }
    public function getRepository(){
        
        return datatables()->query(
            DB::table('almacenes')
            ->select('almacenes.*', 
            DB::raw('DATE_FORMAT(created_at, "%d/%m/%Y") as creado'),
            DB::raw('DATE_FORMAT(updated_at, "%d/%m/%Y") as actualizado')
            )->where('almacenes.estado','ACTIVO')->orderBy('id','DESC')
        )->toJson();
    }

    public function store(Request $request){
        
        $this->authorize('haveaccess','almacen.index'); 
        $data = $request->all();

        $rules = [
            'descripcion_guardar' => [
                'required',
                Rule::unique('almacenes','descripcion')->where(function ($query) {
                    return $query->where('estado', 'ACTIVO');
                }),
            ],
            'ubicacion_guardar' => 'required',
        ];
        
        $message = [
            'descripcion_guardar.required'  => 'El campo Descripción es obligatorio.',
            'descripcion_guardar.unique'    => 'El nombre de almacén ya existe.',
            'ubicacion_guardar.required'    => 'El campo Ubicación es obligatorio.',
        ];

        Validator::make($data, $rules, $message)->validate();

        $almacen = new Almacen();
        $almacen->descripcion = $request->get('descripcion_guardar');
        $almacen->ubicacion = $request->get('ubicacion_guardar');
        $almacen->save();

        
        //Registro de actividad
        $descripcion = "SE AGREGÓ EL ALMACEN CON EL NOMBRE: ". $almacen->descripcion;
        $gestion = "ALMACEN";
        crearRegistro($almacen, $descripcion , $gestion);

        Session::flash('success','Almacen creado.');
        return redirect()->route('almacenes.almacen.index')->with('guardar', 'success');
    }

    public function update(Request $request){
        
        $this->authorize('haveaccess','almacen.index');
        $data = $request->all();

        $rules = [
            'tabla_id' => 'required',
            'descripcion' => 'required',
            'ubicacion' => 'required',
        ];
        
        $message = [
            'descripcion.required' => 'El campo Descripción es obligatorio.',
            'ubicacion.required' => 'El campo Ubicación es obligatorio.',
        ];

        Validator::make($data, $rules, $message)->validate();
        
        $almacen = Almacen::findOrFail($request->get('tabla_id'));
        $almacen->descripcion = $request->get('descripcion');
        $almacen->ubicacion = $request->get('ubicacion');
        $almacen->update();

        //Registro de actividad
        $descripcion = "SE MODIFICÓ EL ALMACEN CON EL NOMBRE: ". $almacen->descripcion;
        $gestion = "ALMACEN";
        modificarRegistro($almacen, $descripcion , $gestion);

        Session::flash('success','Almacen modificado.');
        return redirect()->route('almacenes.almacen.index')->with('modificar', 'success');
    }

    
    public function destroy($id)
    {
        
        $this->authorize('haveaccess','almacen.index');
        $almacen = Almacen::findOrFail($id);
        $almacen->estado = 'ANULADO';
        $almacen->update();

        //Registro de actividad
        $descripcion = "SE ELIMINÓ EL ALMACEN CON EL NOMBRE: ". $almacen->descripcion;
        $gestion = "ALMACEN";
        eliminarRegistro($almacen, $descripcion , $gestion);


        Session::flash('success','Almacen eliminado.');
        return redirect()->route('almacenes.almacen.index')->with('eliminar', 'success');

    }

    public function exist(Request $request)
    {
        
        $data = $request->all();
        $descripcion = $data['descripcion'];
        $ubicacion = $data['ubicacion'];
        $id = $data['id'];
        $almacen = null;

        if ($descripcion && $id && $ubicacion ) { // edit
            $almacen = Almacen::where([
                                    ['descripcion', $data['descripcion']],
                                    ['ubicacion', $data['ubicacion']],
                                    ['id', '<>', $data['id']]
                                ])->where('estado','!=','ANULADO')->first();
        } else if ($ubicacion && $descripcion && !$id) { // create
            $almacen = Almacen::where('descripcion', $data['descripcion'])->where('ubicacion',$data['ubicacion'])->where('estado','!=','ANULADO')->first();
        }

        $result = ['existe' => ($almacen) ? true : false];

        return response()->json($result);

    }

 

}
