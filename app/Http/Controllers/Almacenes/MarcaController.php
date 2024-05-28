<?php

namespace App\Http\Controllers\Almacenes;

use App\Almacenes\Marca;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;

class MarcaController extends Controller
{
    public function index()
    {
        $this->authorize('haveaccess','marca.index');
        return view('almacenes.marcas.index');
    }

    public function getmarca(){
            $marcas = Marca::where('estado','ACTIVO')->orderBy('id','DESC')->get();
            $coleccion = collect([]);
            foreach($marcas as $marca){
                $coleccion->push([
                    'id' => $marca->id,
                    'marca' => $marca->marca,
                    'procedencia' => $marca->procedencia,
                    'fecha_creacion' =>  Carbon::parse($marca->created_at)->format( 'd/m/Y'),
                    'fecha_actualizacion' =>  Carbon::parse($marca->updated_at)->format( 'd/m/Y'),
                    'estado' => $marca->estado,
                ]);
            }
            return DataTables::of($coleccion)->toJson();
    }

    public function store(Request $request){
        $this->authorize('haveaccess','marca.index');
        $data = $request->all();
       
        $rules = [
            'marca_guardar' => [
                'required',
                Rule::unique('marcas', 'marca')->where(function ($query) {
                    return $query->where('estado', 'ACTIVO');
                }),
            ]       
        ];

        $messages = [
            'marca_guardar.required'    =>  'El campo Marca es obligatorio.',
            'marca_guardar.unique'      =>  'La marca ya existe',
        ];

        $validator = Validator::make($data, $rules ,$messages);

        if($request->get('fetch') && $request->get('fetch')=='SI'){
            if ($validator->fails()) {
                return response()->json(['message' => 'error','data'=>$validator->errors()->toArray()]);
            }
        }else{
            $validator->validate();
        }

        $marca = new Marca();
        $marca->marca = $request->get('marca_guardar');
        $marca->procedencia = $request->get('procedencia_guardar');
        $marca->save();

        //Registro de actividad
        $descripcion = "SE AGREGÓ LA MARCA CON EL NOMBRE: ". $marca->marca;
        $gestion = "MARCA PT";
        crearRegistro($marca, $descripcion , $gestion);

        if($request->has('fetch') && $request->input('fetch') == 'SI'){
            $update_marcas  =   Marca::all();
            return response()->json(['message' => 'success',    'data'=>$update_marcas]);        
        }

        Session::flash('success','Marca creada.');
        return redirect()->route('almacenes.marcas.index')->with('guardar', 'success');
    }

    public function update(Request $request){
        $this->authorize('haveaccess','marca.index');
        $data = $request->all();

        $rules = [
            'tabla_id' => 'required',
            'marca' => [
                'required',
                Rule::unique('marcas', 'marca')->where(function ($query) {
                    return $query->where('estado', 'ACTIVO');
                }),
            ]   

        ];

        $message = [
            'marca.required'    => 'El campo Marca es obligatorio.',
            'marca.unique'      => 'La marca ya existe.',
        ];

        Validator::make($data, $rules, $message)->validate();

        $marca = Marca::findOrFail($request->get('tabla_id'));
        $marca->marca = $request->get('marca');
        $marca->procedencia = $request->get('procedencia');
        $marca->update();

        //Registro de actividad
        $descripcion = "SE MODIFICÓ LA MARCA CON EL NOMBRE: ". $marca->marca;
        $gestion = "MARCA PT";
        modificarRegistro($marca, $descripcion , $gestion);

        Session::flash('success','Marca modificada.');
        return redirect()->route('almacenes.marcas.index')->with('modificar', 'success');
    }

    public function destroy($id)
    {
        $this->authorize('haveaccess','marca.index');
        $marca = Marca::findOrFail($id);
        $marca->estado = 'ANULADO';
        $marca->update();

        //Registro de actividad
        $descripcion = "SE ELIMINÓ LA MARCA CON EL NOMBRE: ". $marca->marca;
        $gestion = "MARCA PT";
        eliminarRegistro($marca, $descripcion , $gestion);

        Session::flash('success','Marca eliminada.');
        return redirect()->route('almacenes.marcas.index')->with('eliminar', 'success');

    }

    public function exist(Request $request)
    {

        $data           = $request->all();
        
        $marca          = $data['marca'];
        $id             = $data['id'];
        $marca_existe   = null;

        if ($marca && $id) { // edit
            $marca_existe = Marca::where([
                                    ['marca', $data['marca']],
                                    ['id', '<>', $data['id']]
                                ])->where('estado','!=','ANULADO')->first();
        } else if ($marca && !$id) { // create
            $marca_existe = Marca::where('marca', $data['marca'])->where('estado','!=','ANULADO')->first();
        }

        $result = ['existe' => ($marca_existe) ? true : false];

        return response()->json($result);

    }
}
