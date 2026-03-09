<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilidadesController;
use App\Http\Requests\Cliente\ClienteStoreFastRequest;
use App\Http\Requests\Cliente\ClienteStoreRequest;
use App\Http\Requests\Cliente\ClienteUpdateRequest;
use App\Models\Ventas\TipoCliente\TipoCliente;
use App\Ventas\Cliente;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class ClienteController extends Controller
{
    public function index()
    {
        return view('ventas.clientes.index');
    }

    public function getTable()
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

        return DataTables::make($clientes)->toJson();
    }

    public function create()
    {
        $action         =   route('ventas.cliente.store');
        $tipos_clientes =   UtilidadesController::getTiposClientes();
        $cliente        =   new Cliente();
        return view('ventas.clientes.create')->with(
            compact(
                'action',
                'cliente',
                'tipos_clientes'
            )
        );
    }

    public function store(ClienteStoreRequest $request)
    {
        DB::beginTransaction();
        try {

            $arrayDatos = $request->all();
            if ($arrayDatos['fecha_aniversario'] == "-") {
                unset($arrayDatos['fecha_aniversario']);
            } else {
                $arrayDatos['fecha_aniversario'] = Carbon::createFromFormat('d/m/Y', $arrayDatos['fecha_aniversario'])->format('Y-m-d');
            }

            $tipo_cliente                   =   TipoCliente::findOrFail($request->get('tipo_cliente'));

            $cliente                        =   new Cliente($arrayDatos);
            $cliente->tipo_documento        =   $request->get('tipo_documento');

            $cliente->documento             =   $request->get('documento');
            $cliente->tipo_cliente_id       =   $tipo_cliente->id;
            $cliente->tipo_cliente_nombre   =   $tipo_cliente->nombre;
            $cliente->nombre                =   mb_strtoupper($request->get('nombre'), 'UTF-8');
            $cliente->codigo                =   $request->get('codigo');
            $cliente->zona                  =   $request->get('zona');
            $cliente->nombre_comercial      =   $request->get('nombre_comercial');

            $cliente->departamento_id       =   str_pad($request->get('departamento'), 2, "0", STR_PAD_LEFT);
            $cliente->provincia_id          =   str_pad($request->get('provincia'), 4, "0", STR_PAD_LEFT);
            $cliente->distrito_id           =   str_pad($request->get('distrito'), 6, "0", STR_PAD_LEFT);
            $cliente->direccion             =   $request->get('direccion');
            $cliente->correo_electronico    =   $request->get('correo_electronico');
            $cliente->telefono_movil        =   $request->get('telefono_movil');
            $cliente->telefono_fijo         =   $request->get('telefono_fijo');
            $cliente->activo                =   $request->get('activo');

            $cliente->facebook              =   $request->get('facebook');
            $cliente->instagram             =   $request->get('instagram');
            $cliente->web                   =   $request->get('web');

            $cliente->hora_inicio           =   $request->get('hora_inicio');
            $cliente->hora_termino          =   $request->get('hora_termino');


            $cliente->nombre_propietario    =   $request->get('nombre_propietario');
            $cliente->direccion_propietario =   $request->get('direccion_propietario');

            if ($request->get('fecha_nacimiento_prop') != "-") {
                $cliente->fecha_nacimiento_prop  = Carbon::createFromFormat('d/m/Y', $request->get('fecha_nacimiento_prop'))->format('Y-m-d');
            } else {
                $cliente->fecha_nacimiento_prop  = NULL;
            }

            $cliente->celular_propietario   =   $request->get('celular_propietario');
            $cliente->correo_propietario    =   $request->get('correo_propietario');

            //Latitud y longitud
            $cliente->lat = $request->get('lat');
            $cliente->lng = $request->get('lng');

            $cliente->agente_retencion  =   $request->get('retencion');
            $cliente->tasa_retencion    =   $request->get('tasa_retencion');
            $cliente->monto_mayor       =   $request->get('monto_mayor');

            //Img Gps
            if ($request->hasFile('logo')) {
                $file                   =   $request->file('logo');
                $name                   =   $file->getClientOriginalName();
                $cliente->nombre_logo   =   $name;
                $cliente->ruta_logo     =   $request->file('logo')->store('public/clientes/img');
            }
            $cliente->save();

            //Registro de actividad
            $descripcion    =   "SE AGREGÓ EL CLIENTE CON EL NOMBRE: " . $cliente->nombre;
            $gestion        =   "CLIENTES";
            crearRegistro($cliente, $descripcion, $gestion);

            Session::flash('success', 'Cliente creado.');
            DB::commit();
            return redirect()->route('ventas.cliente.index')->with('guardar', 'success');
        } catch (Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
        }
    }

    public function edit($id)
    {
        $cliente        =   Cliente::findOrFail($id);
        $tipos_clientes =   UtilidadesController::getTiposClientes();

        $put = True;
        $action = route('ventas.cliente.update', $id);
        return view('ventas.clientes.edit', [
            'cliente' => $cliente,
            'action' => $action,
            'put' => $put,
            'tipos_clientes'    =>  $tipos_clientes
        ]);
    }

    public function update(ClienteUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        try {

            $tipo_cliente                   = TipoCliente::findOrFail($request->get('tipo_cliente'));

            $cliente                        = Cliente::findOrFail($id);
            $cliente->tipo_documento        = $request->get('tipo_documento');
            $cliente->documento             = $request->get('documento');
            $cliente->nombre                = mb_strtoupper($request->get('nombre'), 'UTF-8');

            $cliente->codigo                = $request->get('codigo');
            $cliente->zona                  = $request->get('zona');
            $cliente->nombre_comercial      = $request->get('nombre_comercial');

            $cliente->tipo_cliente_id       = $tipo_cliente->id;
            $cliente->tipo_cliente_nombre   = $tipo_cliente->nombre;
            $cliente->departamento_id       = str_pad($request->get('departamento'), 2, "0", STR_PAD_LEFT);
            $cliente->provincia_id          = str_pad($request->get('provincia'), 4, "0", STR_PAD_LEFT);
            $cliente->distrito_id           = str_pad($request->get('distrito'), 6, "0", STR_PAD_LEFT);
            $cliente->direccion             = $request->get('direccion');
            $cliente->correo_electronico    = $request->get('correo_electronico');
            $cliente->telefono_movil        = $request->get('telefono_movil');
            $cliente->telefono_fijo         = $request->get('telefono_fijo');

            $cliente->direccion_negocio     = $request->get('direccion_negocio');
            if ($request->get('fecha_aniversario') != "-") {
                $cliente->fecha_aniversario = Carbon::createFromFormat('d/m/Y', $request->get('fecha_aniversario'))->format('Y-m-d');
            }
            $cliente->activo                = $request->get('activo');
            $cliente->observaciones         = $request->get('observaciones');
            $cliente->facebook              = $request->get('facebook');
            $cliente->instagram             = $request->get('instagram');
            $cliente->web                   = $request->get('web');

            $cliente->hora_inicio           = $request->get('hora_inicio');
            $cliente->hora_termino          = $request->get('hora_termino');


            $cliente->nombre_propietario    = $request->get('nombre_propietario');
            $cliente->direccion_propietario = $request->get('direccion_propietario');


            if ($request->get('fecha_nacimiento_prop') != "-") {
                $cliente->fecha_nacimiento_prop  = Carbon::createFromFormat('d/m/Y', $request->get('fecha_nacimiento_prop'))->format('Y-m-d');
            } else {
                $cliente->fecha_nacimiento_prop  = NULL;
            }


            $cliente->celular_propietario   = $request->get('celular_propietario');
            $cliente->correo_propietario    = $request->get('correo_propietario');

            //Latitud y longitud
            $cliente->lat                   = $request->get('lat');
            $cliente->lng                   = $request->get('lng');

            $cliente->agente_retencion      = $request->get('retencion');
            $cliente->tasa_retencion        = $request->get('tasa_retencion');
            $cliente->monto_mayor           = $request->get('monto_mayor');

            //Imagen cliente gps
            if ($request->hasFile('logo')) {
                Storage::delete($cliente->ruta_logo);
                $file                       = $request->file('logo');
                $name                       = $file->getClientOriginalName();
                $cliente->nombre_logo       = $name;
                $cliente->ruta_logo         = $request->file('logo')->store('public/clientes/img');
            }
            $cliente->update();

            //Registro de actividad
            $descripcion    = "SE MODIFICÓ EL CLIENTE CON EL NOMBRE: " . $cliente->nombre;
            $gestion        = "CLIENTES";
            modificarRegistro($cliente, $descripcion, $gestion);

            Session::flash('success', 'Cliente modificado.');
            DB::commit();
            return redirect()->route('ventas.cliente.index')->with('guardar', 'success');
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
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
                if ($cliente->tipo_documento == $data['tipo_documento'] && $cliente->documento == $data['documento']) {
                    $igualPersona = true;
                } else {
                    $cliente = Cliente::where([
                        ['tipo_documento', '=', $data['tipo_documento']],
                        ['documento', $data['documento']],
                        ['estado', 'ACTIVO']
                    ])->first();
                }
            } else {
                $cliente = Cliente::where([
                    ['tipo_documento', '=', $data['tipo_documento']],
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
  "tipo_documento" => "DNI"
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

            $tipo_cliente   =   TipoCliente::findOrFail($request->get('tipo_cliente_id'));

            $cliente                        =   new Cliente();
            $cliente->tipo_documento        =   $request->get('tipo_documento');

            $cliente->documento             =   $request->get('documento');
            $cliente->tipo_cliente_id       =   $tipo_cliente->id;
            $cliente->tipo_cliente_nombre   =   $tipo_cliente->nombre;
            $cliente->nombre                =   $request->get('nombre');
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
}
