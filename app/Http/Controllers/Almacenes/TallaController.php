<?php

namespace App\Http\Controllers\Almacenes;

use App\Almacenes\Talla;
use App\Almacenes\ProductoColor;
use App\Almacenes\ProductoColorTalla;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB; 

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

        $this->asociarTallaProductos($talla);


        //Registro de actividad
        $descripcion = "SE AGREGÓ LA TALLA CON LA DESCRIPCION: ". $talla->descripcion;
        $gestion = "TALLA";
        crearRegistro($talla, $descripcion , $gestion);

        Session::flash('success','Talla creada.');
        return redirect()->route('almacenes.tallas.index')->with('guardar', 'success');
    }

    public function asociarTallaProductos($talla){
        // Obtener todos los productos y colores
        $productosColores = DB::table('producto_color_tallas')
        ->select('producto_id', 'color_id')
        ->distinct()
        ->get();

        // Iterar sobre los productos y colores para asociar la nueva talla
        foreach ($productosColores as $productoColor) {
            // Verificar si la talla ya está asociada
            $existeAsociacion = DB::table('producto_color_tallas')
                ->where('producto_id', $productoColor->producto_id)
                ->where('color_id', $productoColor->color_id)
                ->where('talla_id', $talla->id)
                ->exists();

            // Si no existe la asociación, agregarla
            if (!$existeAsociacion) {
                $producto_color_talla = new ProductoColorTalla();
                $producto_color_talla->color_id      = $productoColor->color_id;
                $producto_color_talla->producto_id   = $productoColor->producto_id;
                $producto_color_talla->talla_id      = $talla->id;
                $producto_color_talla->stock         = 0;
                $producto_color_talla->stock_logico  = 0;
                $producto_color_talla->estado        =   '1';
                $producto_color_talla->save();
            }
        }
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
