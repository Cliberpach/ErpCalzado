<?php

namespace App\Http\Controllers\Mantenimiento\Promocion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mantenimiento\Promocion\PromocionStoreRequest;
use App\Http\Requests\Mantenimiento\Promocion\PromocionUpdateRequest;
use App\Models\Mantenimiento\Promocion\Promocion;
use App\Models\Mantenimiento\Promocion\PromocionProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class PromocionController extends Controller
{
    public function index()
    {
        $this->authorize('haveaccess', 'mantenimiento.promociones.index');

        return view('mantenimiento.promociones.index');
    }

    public function getAll()
    {
        $promociones = DB::table('promociones')
            ->select(
                'id',
                'nombre',
                'descripcion',
                'tipo_promocion',
                'valor',
                'cantidad_minima',
                'fecha_inicio',
                'fecha_fin',
                'estado',
                'created_at',
            )
            ->where('estado', 'ACTIVO')
            ->orderBy('id', 'DESC');


        return DataTables::of($promociones)->make(true);
    }

    /*
    array:8 [
  "_token" => "H06vDH6x8djRtzJmWnoIB0qh2IqXXaO5RGNGYbtV"
  "nombre" => "SUPER PROMO"
  "descripcion" => "DESC"
  "tipo_promocion" => "precio_total"
  "valor" => "100"
  "cantidad_minima" => "3"
  "fecha_inicio" => "2026-05-19"
  "fecha_fin" => "2026-05-21"
]
    */

    public function store(PromocionStoreRequest $request)
    {
        $this->authorize('haveaccess', 'mantenimiento.promociones.index');
        DB::beginTransaction();

        try {

            $data = $request->all();

            $promocion = Promocion::create([

                'nombre' => mb_strtoupper(
                    trim($data['nombre']),
                    'UTF-8'
                ),

                'descripcion' => isset($data['descripcion'])
                    ? mb_strtoupper(
                        trim($data['descripcion']),
                        'UTF-8'
                    )
                    : null,

                'tipo_promocion' => $data['tipo_promocion'],

                'valor' => $data['valor'],

                'cantidad_minima' => $data['cantidad_minima'],

                'fecha_inicio' => $data['fecha_inicio'] ?? null,

                'fecha_fin' => $data['fecha_fin'] ?? null,

                'estado' => 'ACTIVO',
            ]);

            // Registro actividad
            $descripcion = "SE REGISTRÓ LA PROMOCIÓN: " . $promocion->nombre;
            $gestion = "PROMOCION";
            crearRegistro($promocion, $descripcion, $gestion);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Promoción registrada con éxito'
            ]);
        } catch (Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function update(PromocionUpdateRequest $request, int $id)
    {
        $this->authorize('haveaccess', 'mantenimiento.promociones.index');

        DB::beginTransaction();

        try {

            $promocion = Promocion::findOrFail($id);

            $promocion->update([

                'nombre' => mb_strtoupper(
                    trim($request->get('nombre')),
                    'UTF-8'
                ),

                'descripcion' => $request->get('descripcion')
                    ? mb_strtoupper(
                        trim($request->get('descripcion')),
                        'UTF-8'
                    )
                    : null,

                'tipo_promocion' => $request->get('tipo_promocion'),

                'valor' => $request->get('valor'),

                'cantidad_minima' => $request->get('cantidad_minima'),

                'fecha_inicio' => $request->get('fecha_inicio'),

                'fecha_fin' => $request->get('fecha_fin'),
            ]);

            // Registro actividad
            $descripcion = "SE MODIFICÓ LA PROMOCIÓN: " . $promocion->nombre;
            $gestion = "PROMOCION";
            modificarRegistro($promocion, $descripcion, $gestion);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Promoción modificada con éxito'
            ]);
        } catch (Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        $this->authorize('haveaccess', 'mantenimiento.promociones.index');

        DB::beginTransaction();

        try {

            $promocion = Promocion::findOrFail($id);
            $promocion->estado = 'ANULADO';
            $promocion->save();

            // Registro actividad
            $descripcion = "SE ELIMINÓ LA PROMOCIÓN: " . $promocion->nombre;
            $gestion = "PROMOCION";
            eliminarRegistro($promocion, $descripcion, $gestion);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Promoción eliminada con éxito'
            ]);
        } catch (Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    /*
array:2 [
  "productos" => "[16]"
  "_method" => "PUT"
]
*/
    public function addProducts(Request $request, $id)
    {
        DB::beginTransaction();

        try {

            $productos = json_decode($request->get('productos'), true);

            if (!is_array($productos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos inválidos'
                ]);
            }

            // NORMALIZAR IDS
            $productos = array_map('intval', $productos);

            /*
        |--------------------------------------------------------------------------
        | VALIDAR PRODUCTOS EN OTRAS PROMOCIONES ACTIVAS
        |--------------------------------------------------------------------------
        */
            $productosEnPromocion = PromocionProducto::query()
                ->join(
                    'promociones',
                    'promociones.id',
                    '=',
                    'promociones_productos.promocion_id'
                )
                ->join(
                    'productos',
                    'productos.id',
                    '=',
                    'promociones_productos.producto_id'
                )
                ->whereIn('promociones_productos.producto_id', $productos)
                ->where('promociones_productos.estado', 1)
                ->where('promociones.estado', 'ACTIVO')
                ->where('promociones.id', '!=', $id)
                ->select(
                    'productos.id',
                    'productos.nombre'
                )
                ->distinct()
                ->get();

            if ($productosEnPromocion->isNotEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Algunos productos ya pertenecen a otra promoción activa.',
                    'productos' => $productosEnPromocion
                ]);
            }

            /*
        |--------------------------------------------------------------------------
        | DESACTIVAR LOS QUE YA NO VIENEN
        |--------------------------------------------------------------------------
        */

            PromocionProducto::where('promocion_id', $id)
                ->whereNotIn('producto_id', $productos)
                ->update([
                    'estado' => 0
                ]);

            /*
        |--------------------------------------------------------------------------
        | CREAR / REACTIVAR
        |--------------------------------------------------------------------------
        */

            foreach ($productos as $producto_id) {

                $registro = PromocionProducto::where('promocion_id', $id)
                    ->where('producto_id', $producto_id)
                    ->first();

                if ($registro) {

                    $registro->update([
                        'estado' => 1
                    ]);
                } else {

                    PromocionProducto::create([
                        'promocion_id' => $id,
                        'producto_id' => $producto_id,
                        'estado' => 1
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Productos sincronizados correctamente'
            ]);
        } catch (Throwable $th) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function getProductsPromocion($id)
    {
        try {

            $productos = DB::table('promociones_productos')
                ->where('promocion_id', $id)
                ->where('estado', 1)
                ->pluck('producto_id');

            return response()->json([
                'success' => true,
                'productos' => $productos
            ]);
        } catch (Throwable $th) {

            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }
}
