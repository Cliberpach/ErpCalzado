<?php

namespace App\Http\Controllers;

use App\Mantenimiento\Tabla\Detalle;
use App\Mantenimiento\Ubigeo\Departamento;
use App\Mantenimiento\Ubigeo\Distrito;
use App\Mantenimiento\Ubigeo\Provincia;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ConsultasAjaxController extends Controller
{
    public function getTipoDocumentos()
    {
        $tipos_documento = tipos_documento();
        return response()->json($tipos_documento);
    }
    public function tipoClientes()
    {
        $tipo_clientes = tipo_clientes();
        return response()->json($tipo_clientes);
    }

    public function getDepartamentos()
    {
        $datos = departamentos();
        return response()->json($datos);
    }
    public function getCodigoPrecioMenor()
    {
        $datos = codigoPrecioMenor();
        return $datos;
    }

    public function getTipoEnvios()
    {
        $tipos_envio    =   DB::select('select td.id,td.descripcion from tablas as t
        inner join tabladetalles as td  on t.id=td.tabla_id
        where t.id=35 and td.estado="ACTIVO"');

        return response()->json($tipos_envio);
    }

    public function getEmpresasEnvio($tipo_envio)
    {
        try {
            $_tipo_envio    =   Detalle::findOrFail($tipo_envio);

            $empresas_envio =   DB::select(
                                    'SELECT
                                                    ee.*
                                                    FROM empresas_envio AS ee
                                                    WHERE ee.tipo_envio=?
                                                    AND ee.estado="ACTIVO"',
                                    [$_tipo_envio->descripcion]
                                );

            return response()->json(['success' => true, 'empresas_envio' => $empresas_envio]);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => "ERROR EN EL SERVIDOR", 'exception' => $th->getMessage()]);
        }
    }

    public function getSedesEnvio($empresa_envio_id, $ubigeo)
    {
        try {
            $ubigeo             =   json_decode($ubigeo);
            //dd($ubigeo);
            $departamento_id   = str_pad($ubigeo[0], 2, '0', STR_PAD_LEFT);
            $provincia_id      = str_pad($ubigeo[1], 4, '0', STR_PAD_LEFT);
            $distrito_id       = str_pad($ubigeo[2], 6, '0', STR_PAD_LEFT);

            $departamento           =   Departamento::find($departamento_id);
            $provincia              =   Provincia::find($provincia_id);
            $distrito               =   Distrito::find($distrito_id);

            $sedes_envio    =   DB::select(
                'SELECT
                                ees.*
                                FROM empresa_envio_sedes AS ees
                                WHERE ees.empresa_envio_id=?
                                AND ees.departamento=?
                                AND ees.provincia=?
                                AND ees.distrito=?
                                AND ees.estado="ACTIVO"',
                [
                    $empresa_envio_id,
                    $departamento->nombre,
                    $provincia->nombre,
                    $distrito->nombre
                ]
            );

            return response()->json(['success' => true, 'sedes_envio' => $sedes_envio]);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => "ERROR EN EL SERVIDOR", 'exception' => $th->getMessage()]);
        }
    }


    public function getOrigenesVentas()
    {
        try {

            $origenes_ventas    =   DB::select('SELECT
                                    td.id,
                                    td.descripcion
                                    FROM tabladetalles AS td
                                    WHERE
                                    td.tabla_id="36"
                                    AND td.estado="ACTIVO"');

            return response()->json(['success' => true, 'origenes_ventas' => $origenes_ventas]);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => "ERROR EN EL SERVIDOR", 'exception' => $th->getMessage()]);
        }
    }

    public function getTiposPagoEnvio()
    {
        try {
            $tipos_pago_envio   =   DB::select('SELECT
                                    td.id,
                                    td.descripcion
                                    FROM tabladetalles AS td
                                    WHERE td.tabla_id="37" AND td.estado="ACTIVO" ');

            return response()->json(['success' => true, 'tipos_pago_envio' => $tipos_pago_envio]);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => "ERROR EN EL SERVIDOR", 'exception' => $th->getMessage()]);
        }
    }
}
