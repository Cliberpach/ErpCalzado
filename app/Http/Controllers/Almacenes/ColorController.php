<?php

namespace App\Http\Controllers\Almacenes;

use App\Almacenes\Talla;
use App\Almacenes\ProductoColorTalla;
use App\Http\Controllers\Controller;
use App\Http\Requests\Almacen\Color\ColorStoreRequest;
use App\Http\Requests\Almacen\Color\ColorUpdateRequest;
use App\Http\Services\Almacen\Colores\ColorManager;
use App\Models\Almacenes\Color\Color;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

class ColorController extends Controller
{

    private ColorManager $s_color;

    public function __construct()
    {
        $this->s_color  =   new ColorManager();
    }

    public function index()
    {
        //$this->authorize('haveaccess','categoria.index');
        return view('almacenes.colores.index');
    }

    public function getColores(Request $request)
    {

        $colores = Color::where('estado', 'ACTIVO');

        return DataTables::of($colores)->toJson();
    }

    public function getColor(int $id)
    {
        try {

            $color  =   $this->s_color->getColor($id);

            return response()->json(['success' => true, 'message' => 'COLOR OBTENIDO', 'data' => $color]);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function store(ColorStoreRequest $request)
    {
        DB::beginTransaction();

        try {

            $color  =   $this->s_color->store($request->toArray());

            //Registro de actividad
            $descripcion = "SE AGREGÓ EL COLOR CON LA DESCRIPCION: " . $color->descripcion;
            $gestion = "COLOR";
            crearRegistro($color, $descripcion, $gestion);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'COLOR REGISTRADO CON ÉXITO']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }


        if ($request->has('fetch') && $request->input('fetch') == 'SI') {
            return response()->json(['message' => 'success',    'data' => $color]);
        }

        Session::flash('success', 'Color creado.');
        return redirect()->route('almacenes.colores.index')->with('guardar', 'success');
    }

    public function asociarColorProductos($color)
    {

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

    public function update(ColorUpdateRequest $request, int $id)
    {
        DB::beginTransaction();
        try {

            $color  =   $this->s_color->update($id, $request->toArray());


            //Registro de actividad
            $descripcion = "SE MODIFICÓ EL COLOR CON LA DESCRIPCION: " . $color->descripcion;
            $gestion = "COLOR";
            modificarRegistro($color, $descripcion, $gestion);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'COLOR ACTUALIZADO CON ÉXITO']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }


    public function destroy(int $id)
    {
        $this->authorize('haveaccess', 'categoria.index');
        DB::beginTransaction();
        try {

            $color  =   $this->s_color->destroy($id);
            
            $descripcion = "SE ELIMINÓ EL COLOR CON LA DESCRIPCION: " . $color->descripcion;
            $gestion = "COLOR";
            eliminarRegistro($color, $descripcion, $gestion);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'COLOR ELIMINADO CON ÉXITO']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }
}
