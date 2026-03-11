<?php

namespace App\Http\Controllers\Ventas;

use App\Exports\Ventas\Clientes\ClientesExport;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilidadesController;
use App\Http\Requests\Cliente\ClienteStoreFastRequest;
use App\Http\Requests\Cliente\ClienteStoreRequest;
use App\Http\Requests\Cliente\ClienteUpdateRequest;
use App\Http\Services\Ventas\Clientes\ClienteManager;
use App\Mantenimiento\Empresa\Empresa;
use App\Mantenimiento\Tabla\Detalle;
use App\Mantenimiento\Ubigeo\Departamento;
use App\Mantenimiento\Ubigeo\Distrito;
use App\Mantenimiento\Ubigeo\Provincia;
use App\Models\Ventas\TipoCliente\TipoCliente;
use App\Ventas\Cliente;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class ClienteController extends Controller
{
    private ClienteManager  $s_manager;

    public function __construct()
    {
        $this->s_manager    =   new ClienteManager();
    }

    public function index()
    {
        return view('ventas.clientes.index');
    }

    public function getTable(Request $request)
    {
        $clientes   =   $this->queryCustomers($request);

        return DataTables::make($clientes)->toJson();
    }

    public function queryCustomers(Request $request)
    {
        $clientes = DB::table('clientes as c')
            ->join('departamentos as d', 'd.id', 'c.departamento_id')
            ->join('provincias as p', 'p.id', 'c.provincia_id')
            ->join('distritos as dis', 'dis.id', 'c.distrito_id')
            ->select(
                'c.id',
                'c.tipo_documento',
                'c.documento',
                'c.nombre',
                'c.telefono_movil',
                'd.nombre as departamento',
                'p.nombre as provincia',
                'dis.nombre as distrito',
                'c.provincia_id',
                'c.distrito_id',
                'c.zona',
                'c.tipo_cliente_nombre'
            )
            ->where('c.estado', 'ACTIVO')
            ->orderByDesc('c.id');
        return $clientes;
    }

    public function create()
    {
        $tipos_clientes     =   UtilidadesController::getTiposClientes();
        $departments        =   Departamento::all();
        $provinces          =   Provincia::all();
        $districts          =   Distrito::all();
        $tipos_documento    =   tipos_documento();
        return view('ventas.clientes.create')->with(
            compact(
                'tipos_clientes',
                'departments',
                'provinces',
                'districts',
                'tipos_documento',
            )
        );
    }


/*
array:25 [
  "_token" => "d0jHqdxCtV7YSkPGywYyPAtFkF99PcqkYVfR4J17"
  "type_identity_document" => "6"
  "nro_document" => "76477777"
  "type_customer" => "1"
  "name" => "CLIENTAZO"
  "address" => "AV HUSARES 223"
  "phone" => null
  "email" => null
  "department" => "08"
  "province" => "0811"
  "district" => "081103"

  "direccion_negocio" => null
  "fecha_aniversario" => "-"
  "observaciones" => null
  "facebook" => null
  "instagram" => null
  "web" => null
  "hora_inicio" => null
  "hora_termino" => null

  "nombre_propietario" => null
  "direccion_propietario" => null
  "fecha_nacimiento_prop" => "-"
  "celular_propietario" => null
  "correo_propietario" => null
  logo" => Illuminate\Http\UploadedFile {#1985}
]
*/
    public function store(ClienteStoreRequest $request)
    {
        DB::beginTransaction();
        try {

            $cliente    =   $this->s_manager->store($request->toArray());

            //Registro de actividad
            $descripcion    =   "SE AGREGÓ EL CLIENTE CON EL NOMBRE: " . $cliente->nombre;
            $gestion        =   "CLIENTES";
            crearRegistro($cliente, $descripcion, $gestion);

            Session::flash('success', 'Cliente creado.');
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Cliente registrado con éxito']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }

    public function edit($id)
    {
        $cliente           =   Cliente::findOrFail($id);
        $tipos_clientes     =   UtilidadesController::getTiposClientes();
        $departments        =   Departamento::all();
        $provinces          =   Provincia::all();
        $districts          =   Distrito::all();
        $tipos_documento    =   tipos_documento();
        return view('ventas.clientes.edit', [
            'cliente'           => $cliente,
            'tipos_clientes'    =>  $tipos_clientes,
            'departments'       =>  $departments,
            'provinces'         =>  $provinces,
            'districts'         =>  $districts,
            'tipos_documento'   =>  $tipos_documento
        ]);
    }

    /*
array:11 [
  "_token" => "jZJPbBDAGw1aLzQN7TuRbYGPm17cPrWwz5M5D6zh"
  "type_identity_document" => "6"
  "nro_document" => "77664477"
  "type_customer" => "1"
  "name" => "TEST editado"
  "address" => "AV. RIVERA NAVARRETE NRO. 501, LIMA - LIMA - SAN ISIDRO"
  "phone" => null
  "email" => "editado@gmail.com"
  "department" => "09"
  "province" => "0905"
  "district" => "090509"
]
*/
    public function update(ClienteUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        try {

            $type_customer                  =   TipoCliente::findOrFail($request->get('type_customer'));
            $distrito                       =   Distrito::findOrFail($request->get('district'));
            $departamento                   =   Departamento::findOrFail($request->get('department'));
            $tipo_documento                 =   Detalle::findOrfail($request->get('type_identity_document'));

            $cliente                        =   Cliente::findOrFail($id);
            $cliente->tipo_documento_id     =   $tipo_documento->id;
            $cliente->tipo_documento        =   $tipo_documento->simbolo;

            $cliente->documento             =   $request->get('nro_document');
            $cliente->tipo_cliente_id       =   $type_customer->id;
            $cliente->tipo_cliente_nombre   =   $type_customer->nombre;
            $cliente->nombre                =   mb_strtoupper($request->get('name'), 'UTF-8');
            $cliente->codigo                =   $distrito->id;
            $cliente->zona                  =   $departamento->zona;

            $cliente->departamento_id       =   $request->get('department');
            $cliente->provincia_id          =   $request->get('province');
            $cliente->distrito_id           =   $request->get('district');
            $cliente->direccion             =   $request->get('address');
            $cliente->correo_electronico    =   $request->get('email');
            $cliente->telefono_movil        =   $request->get('phone');
            $cliente->update();

            //Registro de actividad
            $descripcion    = "SE MODIFICÓ EL CLIENTE CON EL NOMBRE: " . $cliente->nombre;
            $gestion        = "CLIENTES";
            modificarRegistro($cliente, $descripcion, $gestion);

            Session::flash('success', 'Cliente modificado.');
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Cliente registrado con éxito']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }

    public function show($id)
    {
        $cliente = Cliente::findOrFail($id);
        return view('ventas.clientes.show', [
            'cliente' => $cliente
        ]);
    }

    public function destroy($id)
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->estado = 'ANULADO';
        $cliente->update();

        //Registro de actividad
        $descripcion = "SE ELIMINÓ EL CLIENTE CON EL NOMBRE: " . $cliente->nombre;
        $gestion = "CLIENTES";
        eliminarRegistro($cliente, $descripcion, $gestion);

        Session::flash('success', 'Cliente eliminado.');
        return redirect()->route('ventas.cliente.index')->with('eliminar', 'success');
    }

    public function getDocumento(Request $request)
    {
        $data           = $request->all();
        $existe         = false;
        $igualPersona   = false;
        if (!is_null($data['tipo_documento']) && !is_null($data['documento'])) {
            if (!is_null($data['id'])) {
                $cliente = Cliente::findOrFail($data['id']);
                if ($cliente->tipo_documento_id == $data['tipo_documento'] && $cliente->documento == $data['documento']) {
                    $igualPersona = true;
                } else {
                    $cliente = Cliente::where([
                        ['tipo_documento_id', '=', $data['tipo_documento']],
                        ['documento', $data['documento']],
                        ['estado', 'ACTIVO']
                    ])->first();
                }
            } else {
                $cliente = Cliente::where([
                    ['tipo_documento_id', '=', $data['tipo_documento']],
                    ['documento', $data['documento']],
                    ['estado', 'ACTIVO']
                ])->first();
            }

            if (!is_null($cliente)) {
                $existe = true;
            }
        }

        $result = [
            'existe' => $existe,
            'igual_persona' => $igualPersona
        ];

        return response()->json($result);
    }

    public function getCustomer(Request $request)
    {
        $data = $request->all();
        $cliente_id = $data['cliente_id'];

        $cliente = Cliente::findOrFail($cliente_id);
        return $cliente;
    }

    /*
array:14 [
  "tipo_documento" => "6"
  "tipo_cliente_id" => 121
  "departamento" => 13
  "provincia" => 1301
  "distrito" => 130101
  "zona" => "NORTE"
  "nombre" => "LUIS DANIEL ALVA LUJAN"
  "documento" => "75608753"
  "direccion" => "Nn"
  "telefono_movil" => "999999999"
  "correo_electronico" => null
  "telefono_fijo" => null
  "codigo_verificacion" => 9
  "activo" => "ACTIVO"
]
*/
    public function storeFast(ClienteStoreFastRequest $request)
    {
        try {
            DB::beginTransaction();

            $tipo_cliente                   =   TipoCliente::findOrFail($request->get('tipo_cliente_id'));
            $tipo_documento                 =   Detalle::findOrfail($request->get('tipo_documento'));

            $cliente                        =   new Cliente();
            $cliente->tipo_documento_id     =   $tipo_documento->id;
            $cliente->tipo_documento        =   $tipo_documento->simbolo;

            $cliente->documento             =   $request->get('documento');
            $cliente->tipo_cliente_id       =   $tipo_cliente->id;
            $cliente->tipo_cliente_nombre   =   $tipo_cliente->nombre;
            $cliente->nombre                =   mb_strtoupper($request->get('nombre'), 'UTF-8');
            $cliente->codigo                =   $request->get('codigo');
            $cliente->zona                  =   $request->get('zona');

            $cliente->departamento_id       =   str_pad($request->get('departamento'), 2, "0", STR_PAD_LEFT);
            $cliente->provincia_id          =   str_pad($request->get('provincia'), 4, "0", STR_PAD_LEFT);
            $cliente->distrito_id           =   str_pad($request->get('distrito'), 6, "0", STR_PAD_LEFT);
            $cliente->direccion             =   $request->get('direccion');
            $cliente->correo_electronico    =   $request->get('correo_electronico');
            $cliente->telefono_movil        =   $request->get('telefono_movil');
            $cliente->telefono_fijo         =   $request->get('telefono_fijo');
            $cliente->activo                =   $request->get('activo');

            $cliente->save();

            //======= REGISTRO DE ACTIVIDAD ========
            $descripcion = "SE AGREGÓ EL CLIENTE CON EL NOMBRE: " . $cliente->nombre;
            $gestion = "CLIENTES";
            crearRegistro($cliente, $descripcion, $gestion);

            DB::commit();

            $cliente_return =   [
                'id'                =>  $cliente->id,
                'tabladetalles_id'  =>  $cliente->tabladetalles_id,
                'tipo_documento'    =>  $cliente->tipo_documento,
                'documento'         =>  $cliente->documento,
                'nombre'            =>  $cliente->nombre
            ];


            return response()->json([
                'success'           =>  true,
                'message'           =>  'CLIENTE: ' . $cliente->nombre . ' ,REGISTRADO CON ÉXITO.',
                'cliente'           =>  $cliente_return,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success'   =>  false,
                'message'   =>  $e->getMessage()
            ]);
        }
    }

    public function getCliente($tipo_documento, $nro_documento)
    {
        try {
            //========== OBTENIENDO CLIENTE ==========
            $cliente    =   DB::select(
                'SELECT
                            c.*
                            FROM clientes AS c
                            WHERE c.tipo_documento = ?
                            AND c.documento = ?
                            AND c.estado = "ACTIVO"',
                [$tipo_documento, $nro_documento]
            );

            $message    =   '';

            if (count($cliente) === 1) {
                $message    =   'EL ' . $cliente[0]->tipo_documento . ': ' . $cliente[0]->documento
                    . ' YA SE ENCUENTRA REGISTRADO COMO CLIENTE';
            }

            if (count($cliente) === 0) {
                $message    =   'EL ' . $tipo_documento . ': ' . $nro_documento . ' NO SE ENCUENTRA REGISTRADO';
            }

            if (count($cliente) > 1) {
                throw new Exception('ERROR AL CONSULTAR CLIENTE EN LA EMPRESA');
            }

            return response()->json(['success' => true, 'cliente' => $cliente, 'message' => $message]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function getClientes(Request $request)
    {
        try {

            $search         =   $request->query('search'); // Palabra clave para la búsqueda
            $page           =   $request->query('page', 1);
            $cliente_id     =   $request->query(('cliente_id'));

            $clientes   =   DB::table('clientes as c')
                ->select(
                    'c.id',
                    DB::raw('CONCAT(c.tipo_documento,":",c.documento,"-",c.nombre) as descripcion'),
                    'c.tipo_documento',
                    'c.documento',
                    'c.nombre',
                    'c.telefono_movil',
                    'c.departamento_id',
                    'c.provincia_id',
                    'c.distrito_id'
                )
                ->where(DB::raw('CONCAT(c.tipo_documento,":",c.documento,"-",c.nombre)'), 'LIKE', "%$search%")
                ->where('c.estado', 'ACTIVO');

            if ($cliente_id) {
                $clientes->where('c.id', $cliente_id);
            }

            $clientes   =   $clientes->paginate(10, ['*'], 'page', $page);

            return response()->json([
                'success'   => true,
                'message'   => 'CLIENTES OBTENIDOS',
                'clientes'  => $clientes->items(),
                'more'      => $clientes->hasMorePages()
            ]);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function excel(Request $request)
    {
        ob_end_clean();
        ob_start();
        $empresa            =   Empresa::findOrFail(1);
        $data               =   $this->queryCustomers($request)->get();

        return Excel::download(
            new ClientesExport($data, $request, $empresa),
            'clientes_lista_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx'
        );
    }
}
