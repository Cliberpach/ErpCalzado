<?php

namespace App\Http\Controllers\Almacenes;

use App\Almacenes\Categoria;
use App\Http\Controllers\Controller;
use App\Http\Requests\Almacen\Categoria\CategoriaUpdateRequest;
use App\Http\Services\Almacen\Categorias\CategoriaManager;
use App\Models\Almacenes\Categoria\Categoria as AlmacenesCategoria;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;
use Throwable;

class CategoriaController extends Controller
{
    private CategoriaManager $s_manager;

    public function __construct()
    {
        $this->s_manager  =   new CategoriaManager();
    }

    public function index()
    {
        $this->authorize('haveaccess', 'categoria.index');
        return view('almacenes.categorias.index');
    }

    public function getAll(Request $request)
    {
        $items =   AlmacenesCategoria::select(
            'id',
            'descripcion',
            'img_ruta',
            'img_nombre'
        )
            ->where('estado', 'ACTIVO');

        return DataTables::of($items)->make(true);
    }

    public function getCategory()
    {
        $categorias = Categoria::where('estado', 'ACTIVO')->get();
        $coleccion = collect([]);
        foreach ($categorias as $categoria) {
            $coleccion->push([
                'id' => $categoria->id,
                'descripcion' => $categoria->descripcion,
                'fecha_creacion' =>  Carbon::parse($categoria->created_at)->format('d/m/Y'),
                'fecha_actualizacion' =>  Carbon::parse($categoria->updated_at)->format('d/m/Y'),
                'estado' => $categoria->estado,
                'img_ruta'  =>  $categoria->img_ruta,
                'img_nombre'    =>  $categoria->img_nombre
            ]);
        }
        return DataTables::of($coleccion)->toJson();
    }


    /*
array:3 [
  "_token" => "RG1HST0u8rFP9P4C45b1jIJZ4NEadP1ACzaldZ4L"
  "nombre" => "BOTAS"
  "imagen" => Illuminate\Http\UploadedFile {#1149}
]
*/
    public function store(Request $request)
    {
        $this->authorize('haveaccess', 'categoria.index');
        DB::beginTransaction();
        try {
            $instance   =   $this->s_manager->store($request->toArray());

            //Registro de actividad
            $descripcion = "SE AGREGÓ LA CATEGORIA CON LA DESCRIPCION: " . $instance->descripcion;
            $gestion = "CATEGORIA";
            crearRegistro($instance, $descripcion, $gestion);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Categoría registrada con éxito']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }


    /*
array:3 [
  "_token" => "RG1HST0u8rFP9P4C45b1jIJZ4NEadP1ACzaldZ4L"
  "nombre" => "SANDALIAS"
  "imagen" => Illuminate\Http\UploadedFile {#1983}
]
*/
    public function update(CategoriaUpdateRequest $request, int $id)
    {
        $this->authorize('haveaccess', 'categoria.index');
        DB::beginTransaction();
        try {
            $instance   =   $this->s_manager->update($request->toArray(), $id);

            //Registro de actividad
            $descripcion = "SE MODIFICÓ LA CATEGORIA CON LA DESCRIPCION: " . $instance->descripcion;
            $gestion = "CATEGORIA";
            modificarRegistro($instance, $descripcion, $gestion);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Categoría actualizada con éxito']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }



        Session::flash('success', 'Categoria modificado.');
        return redirect()->route('almacenes.categorias.index')->with('modificar', 'success');
    }


    public function destroy($id)
    {
        $this->authorize('haveaccess', 'categoria.index');
        DB::beginTransaction();
        try {

            $instance           =   Categoria::findOrFail($id);
            $instance->estado   =   'ANULADO';
            $instance->update();

            //Registro de actividad
            $descripcion = "SE ELIMINÓ LA CATEGORIA CON LA DESCRIPCION: " . $instance->descripcion;
            $gestion = "CATEGORIA";
            modificarRegistro($instance, $descripcion, $gestion);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Categoría eliminada con éxito']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }
}
