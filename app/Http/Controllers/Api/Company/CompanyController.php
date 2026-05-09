<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Controller;
use App\Mantenimiento\Sedes\Sede;
use Throwable;

class CompanyController extends Controller
{
    public function getCompanyLocations()
    {
        try {

            $sedes = Sede::where('estado', 'ACTIVO')
                ->get()
                ->map(function ($sede) {

                    return [
                        'id' => $sede->id,
                        'nombre' => $sede->nombre,
                        'razon_social' => $sede->razon_social,
                        'ruc' => $sede->ruc,
                        'direccion' => $sede->direccion,
                        'urbanizacion' => $sede->urbanizacion,
                        'telefono' => $sede->telefono,
                        'correo' => $sede->correo,

                        'departamento' => $sede->departamento_nombre,
                        'provincia' => $sede->provincia_nombre,
                        'distrito' => $sede->distrito_nombre,

                        'tipo_sede' => $sede->tipo_sede,
                        'codigo_local' => $sede->codigo_local,

                        'logo_nombre' => $sede->logo_nombre,
                        'logo_ruta' => $sede->logo_ruta,

                        'logo_url' => $sede->logo_ruta
                            ? asset('storage/' . $sede->logo_ruta)
                            : null,
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'SEDES OBTENIDAS',
                'data' => $sedes
            ]);
        } catch (Throwable $th) {

            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'code' => $th->getCode()
            ], 500);
        }
    }
}
