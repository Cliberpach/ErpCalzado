<?php

namespace App\Http\Controllers\Almacenes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Almacenes\Modelo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class ModeloController extends Controller
{
    public function index()
    {
        //$this->authorize('haveaccess','marca.index');
        return view('almacenes.modelos.index');
    }

    public function getModelo(){
        $modelos = Modelo::where('estado','ACTIVO')->get();
        $coleccion = collect([]);
        foreach($modelos as $modelo){
            $coleccion->push([
                'id' => $modelo->id,
                'descripcion' => $modelo->descripcion,
                'fecha_creacion' =>  Carbon::parse($modelo->created_at)->format( 'd/m/Y'),
                'fecha_actualizacion' =>  Carbon::parse($modelo->updated_at)->format( 'd/m/Y'),
                'estado' => $modelo->estado,
            ]);
        }
        return DataTables::of($coleccion)->toJson();
    }

    public function store(Request $request){
        //$this->authorize('haveaccess','categoria.index');
        $data = $request->all();

        $rules = [
            'descripcion_guardar' => 'required',
        ];
        
        $message = [
            'descripcion_guardar.required' => 'El campo Descripción es obligatorio.',
        ];

        Validator::make($data, $rules, $message)->validate();

        $modelo = new Modelo();
        $modelo->descripcion = $request->get('descripcion_guardar');
        $modelo->save();

        //Registro de actividad
        $descripcion = "SE AGREGÓ EL MODELO CON LA DESCRIPCION: ". $modelo->descripcion;
        $gestion = "MODELO";
        crearRegistro($modelo, $descripcion , $gestion);

        Session::flash('success','Modelo creado.');
        return redirect()->route('almacenes.modelos.index')->with('guardar', 'success');
    }

    public function update(Request $request){
        //$this->authorize('haveaccess','categoria.index');
        $data = $request->all();

        $rules = [
            'tabla_id' => 'required',
            'descripcion' => 'required',
        ];
        
        $message = [
            'descripcion.required' => 'El campo Descripción es obligatorio.',
        ];

        Validator::make($data, $rules, $message)->validate();
        
        $modelo = Modelo::findOrFail($request->get('tabla_id'));
        $modelo->descripcion = $request->get('descripcion');
        $modelo->update();

        //Registro de actividad
        $descripcion = "SE MODIFICÓ EL MODELO CON LA DESCRIPCION: ". $modelo->descripcion;
        $gestion = "MODELO";
        modificarRegistro($modelo, $descripcion , $gestion);

        Session::flash('success','Modelo modificado.');
        return redirect()->route('almacenes.modelos.index')->with('modificar', 'success');
    }

    
    public function destroy($id)
    {
        //$this->authorize('haveaccess','categoria.index');
        $modelo = Modelo::findOrFail($id);
        $modelo->estado = 'ANULADO';
        $modelo->update();

        //Registro de actividad
        $descripcion = "SE ELIMINÓ EL MODELO CON LA DESCRIPCION: ". $modelo->descripcion;
        $gestion = "MODELO";
        eliminarRegistro($modelo, $descripcion , $gestion);

        Session::flash('success','Modelo eliminado.');
        return redirect()->route('almacenes.modelos.index')->with('eliminar', 'success');

    }
}
