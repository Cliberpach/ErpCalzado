<?php

namespace App\Http\Controllers\Almacenes;

use App\Almacenes\Color;
use App\Almacenes\Talla;
use App\Almacenes\ProductoColorTalla;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB; 

class ColorController extends Controller
{
    public function index()
    {
        //$this->authorize('haveaccess','categoria.index');
        return view('almacenes.colores.index');
    }

    public function getColor(){
        $colores = Color::where('estado','ACTIVO')->get();
        $coleccion = collect([]);
        foreach($colores as $color){
            $coleccion->push([
                'id' => $color->id,
                'descripcion' => $color->descripcion,
                'fecha_creacion' =>  Carbon::parse($color->created_at)->format( 'd/m/Y'),
                'fecha_actualizacion' =>  Carbon::parse($color->updated_at)->format( 'd/m/Y'),
                'estado' => $color->estado,
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

        $color = new Color();
        $color->descripcion = $request->get('descripcion_guardar');
        $color->save();

        
        //$this->asociarColorProductos($color);

        //Registro de actividad
        $descripcion = "SE AGREGÓ EL COLOR CON LA DESCRIPCION: ". $color->descripcion;
        $gestion = "COLOR";
        crearRegistro($color, $descripcion , $gestion);

        Session::flash('success','Color creado.');
        return redirect()->route('almacenes.colores.index')->with('guardar', 'success');
    }

    public function asociarColorProductos($color){

        // Obtener todas las tallas
        $tallas = Talla::all();

        // Iterar sobre las tallas y asociar el nuevo color
        foreach ($tallas as $talla) {
            // Verificar si el color ya está asociado
            $existeAsociacion = DB::table('producto_color_tallas')
                ->where('color_id', $color->id)
                ->where('talla_id', $talla->id)
                ->exists();

            // Si no existe la asociación, agregarla
            if (!$existeAsociacion) {
                // Obtener todos los productos
                $productos = DB::table('producto_color_tallas')
                    ->select('producto_id')
                    ->distinct()
                    ->get();

                // Iterar sobre los productos y agregar la asociación con el nuevo color y talla
                foreach ($productos as $producto) {
                    $producto_color_talla = new ProductoColorTalla();
                    $producto_color_talla->color_id      = $color->id;
                    $producto_color_talla->producto_id   = $producto->producto_id;
                    $producto_color_talla->talla_id      = $talla->id;
                    $producto_color_talla->stock         = 0;
                    $producto_color_talla->stock_logico  = 0;
                    $producto_color_talla->estado        =   '1';
                    $producto_color_talla->save();
                }
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
        
        $color = Color::findOrFail($request->get('tabla_id'));
        $color->descripcion = $request->get('descripcion');
        $color->update();

        //Registro de actividad
        $descripcion = "SE MODIFICÓ EL COLOR CON LA DESCRIPCION: ". $color->descripcion;
        $gestion = "COLOR";
        modificarRegistro($color, $descripcion , $gestion);

        Session::flash('success','Color modificado.');
        return redirect()->route('almacenes.colores.index')->with('modificar', 'success');
    }

    
    public function destroy($id)
    {
        //$this->authorize('haveaccess','categoria.index');
        $color = Color::findOrFail($id);
        $color->estado = 'ANULADO';
        $color->update();

        //Registro de actividad
        $descripcion = "SE ELIMINÓ EL COLOR CON LA DESCRIPCION: ". $color->descripcion;
        $gestion = "COLOR";
        eliminarRegistro($color, $descripcion , $gestion);

        Session::flash('success','Color eliminado.');
        return redirect()->route('almacenes.colores.index')->with('eliminar', 'success');

    }
}
