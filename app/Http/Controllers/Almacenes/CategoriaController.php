<?php

namespace App\Http\Controllers\Almacenes;

use App\Almacenes\Categoria;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;

class CategoriaController extends Controller
{
    public function index()
    {
        $this->authorize('haveaccess', 'categoria.index');
        return view('almacenes.categorias.index');
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

    public function store(Request $request)
    {
        $this->authorize('haveaccess', 'categoria.index');
        $data = $request->all();


        $rules = [
            'descripcion_guardar' => 'required|unique:categorias,descripcion,NULL,id,estado,ACTIVO',
            'imagen' => 'nullable|mimes:jpg,jpeg,webp,avif|max:1024', // 1MB

        ];

        $messages = [
            'descripcion_guardar.required' => 'El campo Descripción es obligatorio.',
            'descripcion_guardar.unique'      =>  'La categoría ya existe',

            'imagen.mimes' => 'La imagen debe ser formato JPG, JPEG, WEBP o AVIF.',
            'imagen.max' => 'La imagen no debe superar 1MB.',
        ];

        $validator = Validator::make($data, $rules, $messages);

        if ($request->get('fetch') && $request->get('fetch') == 'SI') {
            if ($validator->fails()) {
                return response()->json(['message' => 'error', 'data' => $validator->errors()]);
            }
        } else {
            $validator->validate();
        }

        $categoria = new Categoria();
        $categoria->descripcion = $request->get('descripcion_guardar');

        if ($request->hasFile('imagen')) {

            // Ruta dentro de storage/app/public
            $rutaBase = storage_path('app/public/categorias/img');

            // Crear carpetas si no existen
            if (!File::exists($rutaBase)) {
                File::makeDirectory($rutaBase, 0755, true);
            }

            // Generar nombre único
            $extension = $request->file('imagen')->getClientOriginalExtension();
            $nombreImagen = time() . '_' . uniqid() . '.' . $extension;

            // Guardar imagen
            $request->file('imagen')->move($rutaBase, $nombreImagen);

            // Guardar ruta relativa en BD
            $categoria->img_ruta = 'categorias/img/' . $nombreImagen;
            $categoria->img_nombre  =   $nombreImagen;
        }


        $categoria->save();

        //Registro de actividad
        $descripcion = "SE AGREGÓ LA CATEGORIA CON LA DESCRIPCION: " . $categoria->descripcion;
        $gestion = "CATEGORIA";
        crearRegistro($categoria, $descripcion, $gestion);


        if ($request->has('fetch') && $request->input('fetch') == 'SI') {
            $update_categorias  =   Categoria::all();
            return response()->json(['message' => 'success',    'data' => $update_categorias]);
        }

        Session::flash('success', 'Categoria creada.');
        return redirect()->route('almacenes.categorias.index')->with('guardar', 'success');
    }

    public function update(Request $request)
    {
        $this->authorize('haveaccess', 'categoria.index');
        $data = $request->all();

        $rules = [
            'tabla_id' => 'required',
            'descripcion' => [
                'required',
                Rule::unique('categorias')->where(function ($query) {
                    return $query->where('estado', 'ACTIVO');
                }),
            ],
        ];

        $message = [
            'descripcion.required'  => 'El campo Descripción es obligatorio.',
            'descripcion.unique'    => 'La categoría ya existe.',

        ];

        Validator::make($data, $rules, $message)->validate();

        $categoria = Categoria::findOrFail($request->get('tabla_id'));
        $categoria->descripcion = $request->get('descripcion');

        if ($request->hasFile('imagen')) {

            // Eliminar imagen anterior si existe
            if (
                $categoria->img_ruta &&
                file_exists(storage_path('app/public/' . $categoria->img_ruta))
            ) {

                unlink(storage_path('app/public/' . $categoria->img_ruta));
            }

            // Guardar nueva imagen
            $nombreImagen = time() . '_' . uniqid() . '.' .
                $request->file('imagen')->getClientOriginalExtension();

            $request->file('imagen')
                ->storeAs('categorias/img', $nombreImagen, 'public');

            $categoria->img_ruta = 'categorias/img/' . $nombreImagen;
            $categoria->img_nombre = $nombreImagen;
        }
        
        $categoria->update();

        //Registro de actividad
        $descripcion = "SE MODIFICÓ LA CATEGORIA CON LA DESCRIPCION: " . $categoria->descripcion;
        $gestion = "CATEGORIA";
        modificarRegistro($categoria, $descripcion, $gestion);

        Session::flash('success', 'Categoria modificado.');
        return redirect()->route('almacenes.categorias.index')->with('modificar', 'success');
    }


    public function destroy($id)
    {
        $this->authorize('haveaccess', 'categoria.index');
        $categoria = Categoria::findOrFail($id);
        $categoria->estado = 'ANULADO';
        $categoria->update();

        //Registro de actividad
        $descripcion = "SE ELIMINÓ LA CATEGORIA CON LA DESCRIPCION: " . $categoria->descripcion;
        $gestion = "CATEGORIA";
        eliminarRegistro($categoria, $descripcion, $gestion);

        Session::flash('success', 'Categoria eliminado.');
        return redirect()->route('almacenes.categorias.index')->with('eliminar', 'success');
    }
}
