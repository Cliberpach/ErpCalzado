<?php

namespace App\Http\Controllers\Almacenes;

use App\Almacenes\Talla;
use App\Almacenes\ProductoColorTalla;

use App\Http\Controllers\Controller;
use App\Http\Requests\Almacen\Talla\TallaStoreRequest;
use App\Http\Requests\Almacen\Talla\TallaUpdateRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Throwable;

class TallaController extends Controller
{
    public function index()
    {
        $this->authorize('haveaccess', 'almacen.talla.index');
        return view('almacenes.tallas.index');
    }

    public function getRepository(Request $request)
    {
        $data = Talla::where('estado', 'ACTIVO');

        return DataTables::of($data)
            ->editColumn('fecha_creacion', function ($talla) {
                return Carbon::parse($talla->created_at)->format('d/m/Y');
            })
            ->toJson();
    }

    public function store(TallaStoreRequest $request)
    {
        $this->authorize('haveaccess', 'almacen.talla.index');
        DB::beginTransaction();

        try {

            $data = $request->validated();
            $descripcion = mb_strtoupper(trim($data['descripcion']), 'UTF-8');

            $talla = Talla::where('descripcion', $descripcion)->first();

            if ($talla) {

                if ($talla->estado === 'ANULADO') {
                    $talla->estado = 'ACTIVO';
                    $talla->save();

                    $descripcionLog = "SE REACTIVÓ LA TALLA CON LA DESCRIPCIÓN: " . $talla->descripcion;
                    $gestion = "TALLA";
                    crearRegistro($talla, $descripcionLog, $gestion);

                    DB::commit();

                    return response()->json([
                        'success' => true,
                        'message' => 'La talla ya existía y fue reactivada correctamente.'
                    ]);
                }
            }

            // 🆕 CREAR NUEVA
            $data['descripcion'] = $descripcion;
            $data['estado'] = 'ACTIVO';

            $talla = Talla::create($data);

            // 📝 Registro de actividad
            $descripcionLog = "SE AGREGÓ LA TALLA CON LA DESCRIPCIÓN: " . $talla->descripcion;
            $gestion = "TALLA";
            crearRegistro($talla, $descripcionLog, $gestion);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Talla registrada con éxito.'
            ]);
        } catch (Throwable $th) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function asociarTallaProductos($talla)
    {
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

    public function update(TallaUpdateRequest $request, int $id)
    {
        $this->authorize('haveaccess', 'almacen.talla.index');

        DB::beginTransaction();
        try {

            $data = $request->validated();
            $descripcion = mb_strtoupper(trim($data['descripcion']), 'UTF-8');

            $talla = Talla::findOrFail($id);

            $otraTalla = Talla::where('descripcion', $descripcion)
                ->where('id', '!=', $id)
                ->first();

            if ($otraTalla) {

                if ($otraTalla->estado === 'ANULADO') {

                    $otraTalla->estado = 'ACTIVO';
                    $otraTalla->save();

                    $talla->estado = 'ANULADO';
                    $talla->save();

                    $descripcionLog = "SE REACTIVÓ LA TALLA EXISTENTE: " . $otraTalla->descripcion;
                    $gestion = "TALLA";
                    modificarRegistro($otraTalla, $descripcionLog, $gestion);

                    DB::commit();

                    return response()->json([
                        'success' => true,
                        'message' => 'La talla ya existía y fue reactivada correctamente.'
                    ]);
                }
            }

            $talla->descripcion = $descripcion;
            $talla->save();

            $descripcionLog = "SE MODIFICÓ LA TALLA CON LA DESCRIPCIÓN: " . $talla->descripcion;
            $gestion = "TALLA";
            modificarRegistro($talla, $descripcionLog, $gestion);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Talla modificada con éxito.'
            ]);
        } catch (Throwable $th) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function destroy(int $id)
    {
        $this->authorize('haveaccess', 'almacen.talla.index');
        DB::beginTransaction();

        try {

            $talla = Talla::findOrFail($id);
            $talla->estado = 'ANULADO';
            $talla->update();

            // Registro de actividad
            $descripcion = "SE ELIMINÓ LA TALLA CON LA DESCRIPCION: " . $talla->descripcion;
            $gestion = "TALLA";
            eliminarRegistro($talla, $descripcion, $gestion);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Talla eliminada con éxito'
            ]);
        } catch (Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }
}
