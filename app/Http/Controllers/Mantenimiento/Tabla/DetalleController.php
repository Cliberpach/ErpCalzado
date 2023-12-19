<?php

namespace App\Http\Controllers\Mantenimiento\Tabla;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Mantenimiento\Tabla\Detalle;
use App\Mantenimiento\Tabla\General;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class DetalleController extends Controller
{
    public function index($id)
    {
        $tabla = General::findOrFail($id);
        return view('mantenimiento.tablas.detalle.index', ['tabla' => $tabla]);
    }

    public function getTable($id){
        $tablas = Detalle::where('tabla_id', $id)->where('estado','!=','ANULADO')->get();
        $coleccion = collect([]);
        foreach($tablas as $tabla){
            $coleccion->push([
                'id' => $tabla->id,
                'descripcion' => $tabla->descripcion,
                'simbolo' => $tabla->simbolo,
                'fecha_creacion' =>  Carbon::parse($tabla->created_at)->format( 'd/m/Y - H:i:s'),
                'fecha_actualizacion' =>   Carbon::parse($tabla->updated_at)->format( 'd/m/Y - H:i:s'),
                'estado' => $tabla->estado,
                'editable' => $tabla->editable,
            ]);
        }
        return DataTables::of($coleccion)->toJson();
    }

    public function destroy($id)
    {

        $detalle = Detalle::findOrFail($id);
        $detalle->estado = 'ANULADO';
        $detalle->update();

        //Registro de actividad
        $descripcion = "SE ELIMINÓ EL DETALLE CON LA DESCRIPCION: ". $detalle->descripcion;
        $gestion = "TABLA DETALLE";
        eliminarRegistro($detalle, $descripcion , $gestion);


        Session::flash('success','Detalle eliminado.');
        return redirect()->route('mantenimiento.tabla.detalle.index',$detalle->tabla_id)->with('eliminar', 'success');

    }

    public function store(Request $request){

        $data = $request->all();

        $rules = [
            'tabla_id' => 'required',
            'descripcion_guardar' => 'required',
            'simbolo_guardar' => 'required'
        ];

        $message = [
            'descripcion_guardar.required' => 'El campo Descripción es obligatorio.',
            'simbolo_guardar.required' => 'El campo Simbolo es obligatorio.',
        ];

        Validator::make($data, $rules, $message)->validate();

        $detalle = new Detalle();
        $detalle->tabla_id = $request->get('tabla_id');
        $detalle->descripcion = $request->get('descripcion_guardar');
        $detalle->simbolo = $request->get('simbolo_guardar');
        $detalle->save();
        
        //Registro de actividad
        $descripcion = "SE AGREGÓ EL DETALLE CON LA DESCRIPCION: ". $detalle->descripcion;
        $gestion = "TABLA DETALLE";
        crearRegistro($detalle, $descripcion , $gestion);

        Session::flash('success','Detalle creado.');
        return redirect()->route('mantenimiento.tabla.detalle.index',$detalle->tabla_id)->with('guardar', 'success');
    }

    public function update(Request $request){

        $data = $request->all();

        $rules = [
            'tabla_id' => 'required',
            'descripcion' => 'required',
            'simbolo' => 'required'
        ];

        $message = [
            'descripcion.required' => 'El campo Descripción es obligatorio.',
            'simbolo.required' => 'El campo Simbolo es obligatorio.',
        ];

        Validator::make($data, $rules, $message)->validate();

        $detalle = Detalle::findOrFail($request->get('tabla_id'));
        $detalle->descripcion = $request->get('descripcion');
        $detalle->simbolo = $request->get('simbolo');
        $detalle->update();

        //Registro de actividad
        $descripcion = "SE MODIFICÓ EL DETALLE CON LA DESCRIPCION: ". $detalle->descripcion;
        $gestion = "TABLA DETALLE";
        modificarRegistro($detalle, $descripcion , $gestion);

        Session::flash('success','Detalle modificado.');
        return redirect()->route('mantenimiento.tabla.detalle.index',$detalle->tabla_id)->with('modificar', 'success');
    }

    public function getDetail($descripcion)
    {
        $descripcion = Detalle::where('descripcion',$descripcion)->first();
        return $descripcion;
    }

    public function exist(Request $request)
    {
        $data = $request->all();
        $descripcion = $data['descripcion'];
        $id = $data['id'];
        $id_general = $data['id_general'];
        $detalle = null;

        if ($descripcion && $id) { // edit
            $detalle = Detalle::where([
                                    [ 'descripcion', $data['descripcion']],
                                    [ 'tabla_id', $data['id_general']],
                                    [ 'id', '<>', $data['id']]
                                ])->where('estado','!=','ANULADO')->first();
        
        } else if ($descripcion && !$id) { // create
            $detalle = Detalle::where([
                                        [ 'descripcion', $data['descripcion']],
                                        [ 'tabla_id', $data['id_general']]
                                    ])->where('estado','!=','ANULADO')->first();
        }

        $result = ['existe' => ($detalle) ? true : false];
        return response()->json($result);
    }
}
