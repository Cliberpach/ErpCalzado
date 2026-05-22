<?php

namespace App\Http\Controllers\Mantenimiento\Empresa;

use App\Compras\Banco;
use App\Events\FacturacionEmpresa;
use App\Facturacion\Helpers\Certificate\GenerateCertificate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Mantenimiento\Empresa\EmpresaUpdateRequest;
use App\Http\Requests\Mantenimiento\Sedes\NumeracionStoreRequest;
use App\Http\Services\Mantenimiento\Empresa\EmpresaManager;
use App\Http\Services\Mantenimiento\Sede\SedeManager;
use App\Mantenimiento\Empresa\Banco as EmpresaBanco;
use App\Mantenimiento\Empresa\Empresa;
use App\Mantenimiento\Empresa\Facturacion;
use App\Mantenimiento\Empresa\Numeracion;
use App\Mantenimiento\Ubigeo\Departamento;
use App\Mantenimiento\Ubigeo\Distrito;
use App\Mantenimiento\Ubigeo\Provincia;
use Exception;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
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

    public function store(Request $request)
    {
        $data = $request->all();

        return $data;
        $rules = [
            'ruc' => ['required', 'numeric', 'min:11', Rule::unique('empresas', 'ruc')->where(function ($query) {
                $query->whereIn('estado', ["ACTIVO"]);
            })],
            'estado' => 'required',
            'razon_social' => 'required',
            'direccion_fiscal' => 'required',
            'direccion_llegada' => 'required',
            'dni_representante' => 'required|min:8|numeric',
            'nombre_representante' => 'required',
            'num_asiento' => 'required',
            'num_partida' => 'required',
            'telefono' => 'nullable|numeric',
            'celular' => 'nullable|numeric',
            'correo' => 'nullable',
            'web' => 'nullable',
            'facebook' => 'nullable',
            'instagram' => 'nullable',
            'estado_fe' => 'nullable',
            'logo' => 'image|mimetypes:image/jpeg,image/png,image/jpg|max:40000|required_if:estado_fe,==,on',
            'certificado_base' => 'required_if:estado_fe,==,on',
            'soap_usuario' => 'required_if:estado_fe,==,on',
            'soap_password' => 'required_if:estado_fe,==,on',
        ];

        $message = [
            'ruc.required' => 'El campo Ruc es obligatorio.',
            'ruc.unique' => 'El campo Ruc debe de ser campo único.',
            'ruc.numeric' => 'El campo Ruc debe se numérico.',
            'ruc.min' => 'El campo Ruc debe tener 11 dígitos.',
            'razon_social.required' => 'El campo Razón Social es obligatorio.',
            'direccion_fiscal.required' => 'El campo Direccion Fiscal es obligatorio.',
            'direccion_llegada.required' => 'El campo Direccion Planta es obligatorio.',
            'logo.image' => 'El campo Logo no contiene el formato imagen.',
            'logo.max' => 'El tamaño máximo del Logo para cargar es de 40 MB.',
            'dni_representante.required' => 'El campo Dni es obligatorio.',
            'dni_representante.min' => 'El campo Dni debe tener 8 dígitos.',
            'dni_representante.numeric' => 'El campo Dni debe ser numérico.',
            'nombre_representante.required' => 'El campo Nombre es obligatorio.',
            'num_asiento.required' => 'El campo N° Asiento es obligatorio.',
            'num_partida.required' => 'El campo N° Partida es obligatorio.',
            'telefono.numeric' => 'El campo Teléfono es obligatorio.',
            'estado.required' => 'El campo Estado es obligatorio.',
            'soap_usuario.required_if' => 'El campo Soap Usuario es obligatorio.',
            'soap_password.required_if' => 'El campo Soap Contraseña es obligatorio.',
        ];

        Validator::make($data, $rules, $message)->validate();

        $empresa = new Empresa();
        $empresa->ruc = $request->get('ruc');
        $empresa->razon_social = $request->get('razon_social');
        $empresa->razon_social_abreviada = $request->get('razon_social_abreviada');
        $empresa->direccion_fiscal = $request->get('direccion_fiscal');
        $empresa->direccion_llegada = $request->get('direccion_llegada');
        $empresa->telefono = $request->get('telefono');
        $empresa->celular = $request->get('celular');
        $empresa->ubigeo = $request->get('ubigeo_empresa');

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $name = $file->getClientOriginalName();
            $empresa->nombre_logo = $name;
            $empresa->ruta_logo = $request->file('logo')->store('public/empresas/logos');
            $empresa->base64_logo = '-'; //base64_encode( file_get_contents($request->file('logo')));
        }

        $empresa->dni_representante = $request->get('dni_representante');
        $empresa->nombre_representante = $request->get('nombre_representante');
        $empresa->estado_dni_representante = $request->get('estado_dni_representante');

        $empresa->num_partida = $request->get('num_partida');
        $empresa->num_asiento = $request->get('num_asiento');
        $empresa->estado_ruc = $request->get('estado');
        $empresa->web = $request->get('web');
        $empresa->facebook = $request->get('facebook');
        $empresa->instagram = $request->get('instagram');

        if ($request->get('estado_fe') == 'on') {
            $empresa->estado_fe = '1';
        } else {
            $empresa->estado_fe = '0';
        }
        $empresa->save();


        if ($request->get('estado_fe') == 'on') {

            $contenidoImagen = file_get_contents($request->file('logo'));

            $empresa_facturacion = array(
                "plan" => "free",
                "environment" => "beta",
                "sol_user" => $request->get('soap_usuario'),
                "sol_pass" => $request->get('soap_password'),
                "ruc" =>  $empresa->ruc,
                "razon_social" => $empresa->razon_social,
                "direccion" => $empresa->direccion_fiscal,
                "certificado" => $request->get('certificado_base'),
                "logo" => $empresa->base64_logo,
            );

            $json_empresa = json_encode($empresa_facturacion);
            //AGREGAR EMPRESA "FACTURACION ELECTRONICA"
            //$facturado = json_decode((agregarEmpresaapi($json_empresa)));
            //Facturacion Electronica (GUARDAR DATOS INGRESADO POR API)
            $facturacion = new Facturacion();
            $facturacion->empresa_id = $empresa->id; //RELACION CON LA EMPRESA
            $facturacion->fe_id = 1048; //ID EMPRESA API
            $facturacion->sol_user = $request->get('soap_usuario');
            $facturacion->sol_pass = $request->get('soap_password');
            $facturacion->plan = 'free';
            $facturacion->ambiente = 'beta';
            $facturacion->certificado =  $request->get('certificado_base');
            $facturacion->save();

            //REGISTRAR NUMERACION DE FACTURACION DE LA EMPRESA
            event(
                new FacturacionEmpresa(
                    $empresa,
                    $data['numeracion_tabla']
                )
            );
        }

        //Llenado de Bancos
        $entidadesJSON = $request->get('entidades_tabla');
        $entidadtabla = json_decode($entidadesJSON[0]);

        if ($entidadtabla) {
            foreach ($entidadtabla as $entidad) {
                Banco::create([
                    'empresa_id' => $empresa->id,
                    'descripcion' => $entidad->entidad,
                    'tipo_moneda' => $entidad->moneda,
                    'num_cuenta' => $entidad->cuenta,
                    'cci' => $entidad->cci,
                    'itf' => $entidad->itf,
                ]);
            }
        }

        //Registro de actividad
        $descripcion = "SE AGREGÓ LA EMPRESA: " . $empresa->razon_social . ' con el RUC ' . $empresa->ruc;
        $gestion = "EMPRESAS";
        crearRegistro($empresa, $descripcion, $gestion);

        Session::flash('success', 'Empresa creada.');
        return redirect()->route('mantenimiento.empresas.index')->with('guardar', 'success');
    }

    public function destroy($id)
    {

        $empresa = Empresa::findOrFail($id);
        $empresa->estado = 'ANULADO';
        $empresa->update();

        //Registro de actividad
        $descripcion = "SE ELIMINÓ LA EMPRESA: " . $empresa->razon_social . ' con el RUC ' . $empresa->ruc;
        $gestion = "EMPRESAS";
        eliminarRegistro($empresa, $descripcion, $gestion);

        $facturacion = Facturacion::where('empresa_id', $empresa->id)->where('estado', 'ACTIVO')->first();

        if ($facturacion) {

            $estado = borrarEmpresaapi($facturacion->fe_id);

            $facturacion->estado = 'ANULADO';
            $facturacion->update();

            if ($estado != '200') {
                // Session::flash('success','Empresa eliminado (Error en eliminacion del certificado)');
                // return redirect()->route('mantenimiento.empresas.index')->with('eliminar', 'success');

                return [
                    'success' => true,
                    'eliminar' => 'success',
                    'mensaje' => 'Empresa eliminada (Error en eliminacion del certificado)'
                ];
            } else {
                // Session::flash('success','Empresa eliminado.');
                // return redirect()->route('mantenimiento.empresas.index')->with('eliminar', 'success');

                return [
                    'success' => true,
                    'eliminar' => 'success',
                    'mensaje' => 'Empresa eliminada.'
                ];
            }
        } else {

            // Session::flash('success','Empresa eliminado.');
            // return redirect()->route('mantenimiento.empresas.index')->with('eliminar', 'success');
            return [
                'success' => true,
                'eliminar' => 'success',
                'mensaje' => 'Empresa eliminada'
            ];
        }
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
        $empresa = Empresa::findOrFail($id);
        return view('mantenimiento.empresas.facturacion', [
            'empresa' => $empresa
        ]);
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
