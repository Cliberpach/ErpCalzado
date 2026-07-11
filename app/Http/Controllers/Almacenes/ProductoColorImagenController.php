<?php

namespace App\Http\Controllers\Almacenes;

use App\Events\ProductoActualizadoEvent;
use App\Http\Controllers\Controller;
use App\Models\Almacenes\Producto\ProductoColorImagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProductoColorImagenController extends Controller
{
    /**
     * GET /almacenes/productos/{productoId}/colores/{colorId}/imagenes
     * Devuelve las imágenes existentes para un producto+color.
     */
    public function index(int $productoId, int $colorId)
    {
        $imagenes = ProductoColorImagen::where('producto_id', $productoId)
            ->where('color_id', $colorId)
            ->orderBy('orden')
            ->get()
            ->map(function ($img) {
                return [
                    'id'       => $img->id,
                    'orden'    => $img->orden,
                    'img_name' => $img->img_name,
                    'url'      => asset(Storage::url($img->img_route)),
                ];
            });

        return response()->json(['success' => true, 'data' => $imagenes]);
    }

    /**
     * POST /almacenes/productos/{productoId}/colores/{colorId}/imagenes
     * FilePond envía un archivo; lo almacenamos y devolvemos el registro.
     * Máximo 5 imágenes por producto+color.
     */
    public function store(Request $request, int $productoId, int $colorId)
    {
        $request->validate([
            'imagen' => 'required|file|mimes:jpg,jpeg,webp|max:2048',
        ], [
            'imagen.mimes' => 'Solo se permiten imágenes JPG, JPEG o WEBP.',
            'imagen.max'   => 'El archivo no debe superar los 2 MB.',
        ]);

        DB::beginTransaction();
        try {
            $total = ProductoColorImagen::where('producto_id', $productoId)
                ->where('color_id', $colorId)
                ->count();

            if ($total >= 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Máximo 5 imágenes por color.',
                ], 422);
            }

            $orden     = $total + 1;
            $extension = $request->file('imagen')->getClientOriginalExtension();
            $imgName   = $request->file('imagen')->getClientOriginalName();
            $fileName  = 'p' . $productoId . '_c' . $colorId . '_o' . $orden . '_' . time() . '.' . $extension;

            $imgRoute  = $request->file('imagen')
                ->storeAs('public/producto_color_img', $fileName);

            $imagen = ProductoColorImagen::create([
                'producto_id' => $productoId,
                'color_id'    => $colorId,
                'img_route'   => $imgRoute,
                'img_name'    => $imgName,
                'orden'       => $orden,
            ]);

            DB::commit();

            ProductoActualizadoEvent::dispatch($productoId, 'actualizado');

            return response()->json([
                'success' => true,
                'data'    => [
                    'id'       => $imagen->id,
                    'orden'    => $imagen->orden,
                    'img_name' => $imagen->img_name,
                    'url'      => asset(Storage::url($imgRoute)),
                ],
            ]);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }

    /**
     * DELETE /almacenes/productos/{productoId}/colores/{colorId}/imagenes/{id}
     * Elimina la imagen del disco y de la base de datos.
     * Reordena las imágenes restantes del mismo producto+color.
     */
    public function destroy(int $productoId, int $colorId, int $id)
    {
        DB::beginTransaction();
        try {
            $imagen = ProductoColorImagen::where('id', $id)
                ->where('producto_id', $productoId)
                ->where('color_id', $colorId)
                ->firstOrFail();

            Storage::disk('public')->delete(
                str_replace('public/', '', $imagen->img_route)
            );

            $imagen->delete();

            // Reordenar las restantes para que no queden huecos
            $restantes = ProductoColorImagen::where('producto_id', $productoId)
                ->where('color_id', $colorId)
                ->orderBy('orden')
                ->get();

            foreach ($restantes as $index => $item) {
                $item->orden = $index + 1;
                $item->save();
            }

            DB::commit();

            ProductoActualizadoEvent::dispatch($productoId, 'actualizado');

            return response()->json(['success' => true, 'message' => 'Imagen eliminada.']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }
}
