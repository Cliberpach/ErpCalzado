<?php

namespace App\Http\Controllers\Almacenes;

use App\Almacenes\Talla;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class TallaController extends Controller
{
    public function index()
    {
        //$this->authorize('haveaccess','categoria.index');
        return view('almacenes.tallas.index');
    }

    public function getTalla(){
        $tallas = Talla::where('estado','ACTIVO')->get();
        $coleccion = collect([]);
        foreach($tallas as $talla){
            $coleccion->push([
                'id' => $talla->id,
                'descripcion' => $talla->descripcion,
                'fecha_creacion' =>  Carbon::parse($talla->created_at)->format( 'd/m/Y'),
                'fecha_actualizacion' =>  Carbon::parse($talla->updated_at)->format( 'd/m/Y'),
                'estado' => $talla->estado,
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

        $talla = new Talla();
        $talla->descripcion = $request->get('descripcion_guardar');
        $talla->save();

        //Registro de actividad
        $descripcion = "SE AGREGÓ LA TALLA CON LA DESCRIPCION: ". $talla->descripcion;
        $gestion = "TALLA";
        crearRegistro($talla, $descripcion , $gestion);

        Session::flash('success','Talla creada.');
        return redirect()->route('almacenes.tallas.index')->with('guardar', 'success');
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
        
        $talla = Talla::findOrFail($request->get('tabla_id'));
        $talla->descripcion = $request->get('descripcion');
        $talla->update();

        //Registro de actividad
        $descripcion = "SE MODIFICÓ LA TALLA CON LA DESCRIPCION: ". $talla->descripcion;
        $gestion = "TALLA";
        modificarRegistro($talla, $descripcion , $gestion);

        Session::flash('success','Talla modificada.');
        return redirect()->route('almacenes.tallas.index')->with('modificar', 'success');
    }

    
    public function destroy($id)
    {
        //$this->authorize('haveaccess','categoria.index');
        $talla = Talla::findOrFail($id);
        $talla->estado = 'ANULADO';
        $talla->update();

        //Registro de actividad
        $descripcion = "SE ELIMINÓ LA TALLA CON LA DESCRIPCION: ". $talla->descripcion;
        $gestion = "TALLA";
        eliminarRegistro($talla, $descripcion , $gestion);

        Session::flash('success','Talla eliminada.');
        return redirect()->route('almacenes.tallas.index')->with('eliminar', 'success');

    }
}
