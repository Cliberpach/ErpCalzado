<?php

namespace App\Http\Controllers\Mantenimiento\Sede;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mantenimiento\Sedes\NumeracionStoreRequest;
use App\Http\Requests\Mantenimiento\Sedes\SedeStoreRequest;
use App\Http\Requests\Mantenimiento\Sedes\SedeUpdateRequest;
use App\Http\Services\Mantenimiento\Sede\SedeManager;
use App\Mantenimiento\Sedes\Sede;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class SedeController extends Controller
{
    private SedeManager $s_manager;

    public function __construct()
    {
        $this->s_manager = new SedeManager();
    }

    public function index()
    {
        return view('mantenimiento.sedes.index');
    }

    public function create()
    {

        $departamentos  =   DB::select('select * from departamentos');
        $provincias     =   DB::select('select * from provincias');
        $distritos      =   DB::select('select * from distritos');

        return view('mantenimiento.sedes.create', compact('departamentos', 'provincias', 'distritos'));
    }

    public function getSedes()
    {

        $sedes  =   DB::select('select
                    es.id,
                    es.nombre,
                    es.direccion,
                    CONCAT(es.departamento_nombre, " - ", es.provincia_nombre, " - ", es.distrito_nombre) AS ubigeo,
                    es.codigo_local,
                    e.razon_social,
                    es.tipo_sede
                    from empresa_sedes as es
                    inner join empresas as e on e.id = es.empresa_id');

        return DataTables::of($sedes)->toJson();
    }



    /*
array:14 [
  "_token"          => "NtBVgvSzRbuHIPf9aPqpHtk2YDKsJzlErNrAkxCs"
  "nombre"          =  "SEDE NOMBRE"
  "ruc"             => "20370146994"
  "razon_social"    => "CORPORACION ACEROS AREQUIPA S.A."
  "direccion"       => "AV CHAVIMOCHIC 1234"
  "telefono"        => null
  "correo"          => null
  "departmento"     => "01"
  "provincia"       => "0101"
  "distrito"        => "010101"
  "codigo_local"    => "0001"
  "serie"           =>  "0002"
  "urbanizacion"    =>  "urbanizacion"
    "img_empresa" => Illuminate\Http\UploadedFile {#2039
        -test: false
        -originalName: "certificate_test.pem"
        -mimeType: "application/octet-stream"
        -error: 0
        #hashName: null
        path: "D:\xampp\tmp"
        filename: "php49A5.tmp"
        basename: "php49A5.tmp"
        pathname: "D:\xampp\tmp\php49A5.tmp"
        extension: "tmp"
        realPath: "D:\xampp\tmp\php49A5.tmp"
        aTime: 2025-01-04 11:40:25
        mTime: 2025-01-04 11:40:25
        cTime: 2025-01-04 11:40:25
        inode: 38843546786703131
        size: 5332
        perms: 0100666
        owner: 0
        group: 0
        type: "file"
        writable: true
        readable: true
        executable: false
        file: true
        dir: false
        link: false
        linkTarget: "D:\xampp\tmp\php49A5.tmp"
    }
]
*/
    public function store(SedeStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->s_manager->store($request->all());
            DB::commit();
            return response()->json(['success' => true, 'message' => 'SEDE REGISTRADA']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function getNumeracion(Request $request)
    {

        $sede_id    =   $request->get('sede_id');


        $numeracion =   DB::select('select
                        td.descripcion as comprobante,
                        enf.serie,
                        enf.numero_iniciar as nro_inicio,
                        IF(enf.emision_iniciada = "1", "INICIADO", "NO INICIADO") AS iniciado
                        from empresa_numeracion_facturaciones as enf
                        inner join tabladetalles as td on td.id = enf.tipo_comprobante
                        where
                        enf.sede_id = ?
                        and enf.estado = "ACTIVO"
                        and td.tabla_id = 21', [$sede_id]);

        return DataTables::of($numeracion)->toJson();
    }

    public function numeracionCreate($sede_id)
    {

        $sede               =   DB::select('select
                                es.*
                                from empresa_sedes as es
                                where es.id = ?', [$sede_id])[0];

        //====== OMITIR NOTA DÉBITO Y DEVOLUCIÓN ====
        $tipos_comprobantes = DB::select('
                                SELECT
                                    td.id,
                                    td.descripcion,
                                    td.nombre,
                                    td.parametro
                                FROM
                                    tabladetalles AS td
                                WHERE
                                    td.tabla_id = 21
                                    AND td.estado = "ACTIVO"
                                    AND td.simbolo <> "08"
                                    AND td.simbolo <> "NOTADEVOLUCION"
                                    AND td.id NOT IN (
                                        SELECT enf.tipo_comprobante
                                        FROM empresa_numeracion_facturaciones AS enf
                                        WHERE enf.sede_id = ? and enf.empresa_id = 1
                                    )
                            ', [$sede_id]);

        return view('mantenimiento.sedes.numeracion', compact('sede_id', 'tipos_comprobantes', 'sede'));
    }

    /*
array:4 [
  "comprobante_id"  => "128"
  "parametro"       => "B"
  "serie"           => "002"
  "nro_inicio"      => "1"
  "sede_id"         => "13"
]
*/
    public function numeracionStore(NumeracionStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->s_manager->storeNumeracion($request->all());
            DB::commit();
            return response()->json([
                'success'        => true,
                'message'        => 'NUMERACIÓN AGREGADA A LA SEDE',
                'comprobante_id' => $request->get('comprobante_id')
            ]);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function edit($id)
    {
        $sede           =   Sede::find($id);
        $departamentos  =   DB::select('select * from departamentos');
        $provincias     =   DB::select('select * from provincias');
        $distritos      =   DB::select('select * from distritos');

        $rutaRelativa   = 'storage/' . ltrim($sede->logo_ruta, '/');
        $ruta           = public_path(str_replace('/', DIRECTORY_SEPARATOR, $rutaRelativa));

        return view('mantenimiento.sedes.edit', compact('sede', 'departamentos', 'provincias', 'distritos'));
    }


    /*
array:10 [
  "_token" => "JOyNM7PhCTjKURJp3IlRSZAkxqLZl6lX1xKseSbB"
  "nombre" => "SEDE CHICLAYO"
  "direccion" => "AV U123"
  "telefono" => null
  "correo" => null
  "departamento" => "14"
  "provincia" => "1401"
  "distrito" => "140108"
  "urbanizacion" => null
  "codigo_local" => "0002"
]
*/
    public function update($id, SedeUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->s_manager->update($request->all(), (int) $id);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'SEDE ACTUALIZADA CON ÉXITO']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }
}
