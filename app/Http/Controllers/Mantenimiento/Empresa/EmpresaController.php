<?php

namespace App\Http\Controllers\Mantenimiento\Empresa;

use App\Facturacion\Helpers\Certificate\GenerateCertificate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Mantenimiento\Empresa\EmpresaUpdateRequest;
use App\Http\Requests\Mantenimiento\Empresa\FacturacionUpdateRequest;
use App\Http\Requests\Mantenimiento\Sedes\NumeracionStoreRequest;
use App\Http\Services\Mantenimiento\Empresa\EmpresaManager;
use App\Http\Services\Mantenimiento\Sede\SedeManager;
use App\Mantenimiento\Empresa\Banco as EmpresaBanco;
use App\Mantenimiento\Empresa\Empresa;
use App\Mantenimiento\Empresa\Facturacion;
use App\Mantenimiento\Empresa\Numeracion;
use App\Mantenimiento\Greenter\GreenterConfig;
use App\Mantenimiento\Ubigeo\Departamento;
use App\Mantenimiento\Ubigeo\Distrito;
use App\Mantenimiento\Ubigeo\Provincia;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use Yajra\DataTables\DataTables;

class EmpresaController extends Controller
{
    private EmpresaManager $s_manager;
    private SedeManager $sede_manager;

    public function __construct()
    {
        $this->s_manager    = new EmpresaManager();
        $this->sede_manager = new SedeManager();
    }

    public function index()
    {
        return view('mantenimiento.empresas.index');
    }

    public function getBusiness()
    {
        return datatables()->query(
            DB::table('empresas')
                ->select('empresas.*')->where('empresas.estado', 'ACTIVO')->orderBy('empresas.id', 'desc')
        )->toJson();
    }

    public function obtenerNumeracion($id)
    {

        $numeraciones = Numeracion::where('empresa_id', $id)->where('estado', '!=', 'ANULADO')->get();
        $coleccion = collect([]);
        foreach ($numeraciones as $numeracion) {
            $coleccion->push([
                'id' => $numeracion->id,
                'tipo_id' => $numeracion->tipo_comprobante,
                'serie' => $numeracion->serie,
                'tipo_comprobante' => $numeracion->comprobanteDescripcion(),
                'numero_iniciar' => $numeracion->numero_iniciar,
                'emision' => $numeracion->emision_iniciada,
            ]);
        }
        return DataTables::of($coleccion)->toJson();
    }

    public function create()
    {
        $departamentos = Departamento::all();
        $provincias = Provincia::all();
        $distritos = Distrito::all();
        $bancos = bancos();
        $monedas = tipos_moneda();

        return view('mantenimiento.empresas.create', [
            'departamentos' => $departamentos,
            'provincias' => $provincias,
            'distritos' => $distritos,
            'bancos' => $bancos,
            'monedas' => $monedas,
        ]);
    }

    public function show($id)
    {
        $empresa = Empresa::findOrFail($id);
        $banco = EmpresaBanco::where('empresa_id', $id)
            ->where('estado', 'ACTIVO')
            ->get();
        return view('mantenimiento.empresas.show', [
            'empresa' => $empresa,
            'banco' => $banco
        ]);
    }

    public function edit($id)
    {
        $empresa = Empresa::findOrFail($id);

        $numeraciones = Numeracion::where('empresa_id', $id)->where('estado', 'ACTIVO')->get();
        $facturacion = Facturacion::where('empresa_id', $empresa->id)->where('estado', 'ACTIVO')->first();
        $departments    =   Departamento::all();
        $provinces      =   Provincia::all();
        $districts      =   Distrito::all();

        return view('mantenimiento.empresas.edit', [
            'empresa'           => $empresa,
            'facturacion'       => $facturacion,
            'numeraciones'      => $numeraciones,
            "departments"      =>  $departments,
            'provinces'         =>  $provinces,
            'districts'         =>  $districts
        ]);
    }

    public function editFacturacion($id)
    {
        $empresa         = Empresa::findOrFail($id);
        $greenter_config = GreenterConfig::where('empresa_id', 1)->where('modo', 'PRODUCCION')->first();

        return view('mantenimiento.empresas.facturacion', [
            'empresa'         => $empresa,
            'greenter_config' => $greenter_config,
        ]);
    }

    public function facturacionStore(FacturacionUpdateRequest $request)
    {
        try {
            $this->s_manager->facturacionStore(
                $request->except('certificado'),
                $request->file('certificado')
            );

            return response()->json(['success' => true, 'message' => 'CONFIGURACIÓN DE FACTURACIÓN GUARDADA CON ÉXITO']);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    /*
array:30 [
  "ruc" => "10802398307"
  "razon_social" => "SISCOM FAC"
  "razon_social_abreviada" => "SISCOM FAC"
  "ubigeo_empresa" => "130102"
  "direccion_fiscal" => "AV ESPAÑA 1319"
  "direccion_llegada" => "TRUJILLO"
  "departamento" => null
  "provincia" => null
  "distrito" => null
  "correo" => null
  "telefono" => null
  "celular" => null
  "dni_representante" => "70004110"
  "nombre_representante" => "NOMBRE APELLIDOPAT APELLIDOMAT"
  "num_partida" => "11036086"
  "num_asiento" => "A00001"
  "urbanizacion" => "-"
  "cod_local" => "0000"
  "ubigeo" => "130102"
  "facebook" => null
  "instagram" => null
  "web" => null
  "igv" => "18.00"
  "estado" => "ACTIVO"
  "estado_fe" => "1"
  "_method" => "PUT"
  "logo" => Illuminate\Http\UploadedFile {#1228}
]
*/
    public function update(EmpresaUpdateRequest $request, $id)
    {
        try {
            $empresa    =   $this->s_manager->update($request->all(), $id);

            //Registro de actividad
            $descripcion = "SE MODIFICÓ LA EMPRESA: " . $empresa->razon_social . ' con el RUC ' . $empresa->ruc;
            $gestion = "EMPRESAS";
            modificarRegistro($empresa, $descripcion, $gestion);

            return response()->json(['success' => true, 'message' => 'Empresa Actualizada con éxito']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function certificate(Request $request)
    {
        if ($request->hasFile('certificado')) {
            try {

                $file = $request->file('certificado');
                $contra = $request->get('contra_certificado');
                $pfx = file_get_contents($file);
                $pem = GenerateCertificate::typePEM($pfx, $contra);
                $certificado64 = base64_encode($pem);

                return [
                    'success' => true,
                    'certificado' => $certificado64,
                ];
            } catch (Exception $e) {
                return [
                    'success' => false,
                    'message' =>  $e->getMessage()
                ];
            }
        }
        return [
            'success' => false,
            'message' =>  'Error',
        ];
    }

    public function numeracionCreate(int $empresa_id)
    {
        $empresa = Empresa::findOrFail($empresa_id);

        $sede_principal = DB::table('empresa_sedes')
            ->where('empresa_id', $empresa_id)
            ->where('tipo_sede', 'PRINCIPAL')
            ->where('estado', 'ACTIVO')
            ->first();

        if (!$sede_principal) {
            return redirect()->route('mantenimiento.empresas.index')
                ->with('error', 'La empresa no tiene una sede principal registrada.');
        }

        $tipos_comprobantes = DB::select('
            SELECT td.id, td.descripcion, td.nombre, td.parametro
            FROM tabladetalles AS td
            WHERE td.tabla_id = 21
            AND td.estado = "ACTIVO"
            AND td.simbolo <> "08"
            AND td.simbolo <> "NOTADEVOLUCION"
            AND td.id NOT IN (
                SELECT enf.tipo_comprobante
                FROM empresa_numeracion_facturaciones AS enf
                WHERE enf.sede_id = ? AND enf.estado = "ACTIVO"
            )
        ', [$sede_principal->id]);

        return view('mantenimiento.empresas.numeracion', compact('empresa_id', 'empresa', 'sede_principal', 'tipos_comprobantes'));
    }

    public function numeracionStore(NumeracionStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->sede_manager->storeNumeracion($request->all());
            DB::commit();
            return response()->json([
                'success'        => true,
                'message'        => 'NUMERACION AGREGADA A LA EMPRESA',
                'comprobante_id' => $request->get('comprobante_id'),
            ]);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function serie($id)
    {
        $tipos      = tipos_venta();
        $resultado  = $tipos->where('id', $id)->first();

        if (!$resultado) {
            return "NO EXISTE";
        }

        $tipo_comprobante   =   $resultado->descripcion;
        foreach ($tipos as $tipo) {
            //====== 130 N.E FACTURAS FF01 | 131 N.D | 201 N.E BOLETAS BB01 | 202 NOTA DEVOLUCION NN01 ========
            if ($tipo_comprobante === 'NOTA DE CRÉDITO FACTURA' || $tipo_comprobante === 'NOTA DE DEVOLUCIÓN' || $tipo_comprobante === 'NOTA DE CRÉDITO BOLETA' || $tipo_comprobante === "NOTA DE DÉBITO") {
                if ($tipo->id == $id) {
                    $empresas_numeracion = Numeracion::where('tipo_comprobante', $id)->where('estado', 'ACTIVO')->get();
                    $serie = $tipo->parametro . '0' . (count($empresas_numeracion) + 1);
                    return $serie;
                }
            } else {
                if ($tipo->id == $id) {
                    $empresas_numeracion = Numeracion::where('tipo_comprobante', $id)->where('estado', 'ACTIVO')->get();
                    $serie = $tipo->parametro . '00' . (count($empresas_numeracion) + 1);
                    return $serie;
                }
            }
        }
    }
}
