<?php

namespace App\Http\Controllers\Api\Shipping;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Throwable;

class ShippingController extends Controller
{
    public function costByProvince($provinceId)
    {
        try {
            $provincia = DB::table('provincias')
                ->where('id', $provinceId)
                ->first(['id', 'nombre', 'costo']);

            if (!$provincia) {
                return response()->json([
                    'success' => false,
                    'message' => 'Provincia no encontrada',
                ], 404);
            }

            return response()->json([
                'success'  => true,
                'province' => $provincia->nombre,
                'costo'    => $provincia->costo,
            ]);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
