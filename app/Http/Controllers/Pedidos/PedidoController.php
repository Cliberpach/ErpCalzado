<?php

namespace App\Http\Controllers\Pedidos;

use App\Almacenes\Almacen;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use App\Ventas\Pedido;
use App\Ventas\PedidoDetalle;
use App\Mantenimiento\Condicion;
use App\Mantenimiento\Empresa\Empresa;
use App\Ventas\Cliente;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Almacenes\Modelo;
use App\Almacenes\Talla;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade as PDF;
use App\Ventas\Documento\Documento;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Pedido\PedidosExport;
use App\Http\Controllers\UtilidadesController;
use App\Http\Requests\Pedidos\Pedido\PedidoDocVentaRequest;
use App\Http\Requests\Pedidos\Pedido\PedidoStoreRequest;
use App\Http\Requests\Pedidos\Pedido\PedidoUpdateRequest;
use App\Http\Services\Pedidos\Pedidos\PedidoManager;
use App\Mantenimiento\Colaborador\Colaborador;
use App\Mantenimiento\Sedes\Sede;
use App\User;
use Throwable;

class PedidoController extends Controller
{
    private PedidoManager $s_pedido;

    public function __construct()
    {
        $this->s_pedido =   new PedidoManager();
    }

    public function index()
    {
        $roles = DB::table('role_user as rl')
            ->join('roles as r', 'r.id', '=', 'rl.role_id')
            ->where('rl.user_id', Auth::user()->id)
            ->pluck('r.name')
            ->toArray();

        $isAdmin = in_array('ADMIN', $roles);
        return view('pedidos.pedido.index', compact('isAdmin'));
    }

    public function getTable(Request $request)
    {
        $fecha_inicio   =   $request->get('fecha_inicio');
        $fecha_fin      =   $request->get('fecha_fin');
        $pedido_estado  =   $request->get('pedido_estado');
        $cliente_id     =   $request->get('cliente_id');

        $pedidos    =   Pedido::select(
            'pedidos.*',
            DB::raw('CONCAT(pedidos.documento_venta_facturacion_serie, "-", pedidos.documento_venta_facturacion_correlativo) as documento_venta'),
            DB::raw('if(pedidos.cotizacion_id is null,"-",concat("CO-",pedidos.cotizacion_id)) as cotizacion_nro')
        )
            ->where('pedidos.estado', '!=', 'ANULADO');

        if ($fecha_inicio) {
            $pedidos    =   $pedidos->where('fecha_registro', '>=', $fecha_inicio);
        }

        if ($fecha_fin) {
            $pedidos    =   $pedidos->where('fecha_registro', '<=', $fecha_fin);
        }

        if ($pedido_estado) {
            $pedidos    =   $pedidos->where('pedidos.estado', '=', $pedido_estado);
        }

        if ($cliente_id) {
            $pedidos    =   $pedidos->where('pedidos.cliente_id', $cliente_id);
        }

        $roles = DB::table('role_user as rl')
            ->join('roles as r', 'r.id', '=', 'rl.role_id')
            ->where('rl.user_id', Auth::user()->id)
            ->pluck('r.name')
            ->toArray();

        //======== ADMIN PUEDE VER TODOS LOS PEDIDOS DE SU SEDE =====
        if (in_array('ADMIN', $roles)) {
            $pedidos->where('pedidos.sede_id', Auth::user()->sede_id);
        } else {

            //====== USUARIOS PUEDEN VER SOLO SUS PROPIOS PEDIDOS ======
            $pedidos->where('pedidos.sede_id', Auth::user()->sede_id)
                ->where('pedidos.user_id', Auth::user()->id);
        }

        $dataTable  =   DataTables::of($pedidos)
            ->filterColumn('cliente_nombre', function ($query, $keyword) {
                $query->whereRaw('LOWER(pedidos.cliente_nombre) like ?', ["%" . strtolower($keyword) . "%"]);
            })
            ->filterColumn('documento_venta', function ($query, $keyword) {
                $query->whereRaw('LOWER(CONCAT(pedidos.documento_venta_facturacion_serie, "-", pedidos.documento_venta_facturacion_correlativo)) like ?', ["%" . strtolower($keyword) . "%"]);
            })
            ->filterColumn('cotizacion_nro', function ($query, $keyword) {
                $query->whereRaw('LOWER(IF(pedidos.cotizacion_id IS NULL,"-",concat("CO-",pedidos.cotizacion_id))) like ?', ["%" . strtolower($keyword) . "%"]);
            });

        if (in_array('ADMIN', $roles)) {
            $dataTable->filterColumn('user_nombre', function ($query, $keyword) {
                $query->whereRaw('LOWER(pedidos.user_nombre) like ?', ["%" . strtolower($keyword) . "%"]);
            });
        }

        return $dataTable->make(true);
    }


    public function create()
    {

        $empresas           =   Empresa::where('estado', 'ACTIVO')->get();
        $condiciones        =   Condicion::where('estado', 'ACTIVO')->get();

        $sede_id            =   Auth::user()->sede_id;
        $sede               =   Sede::find($sede_id);

        $almacenes          =   Almacen::where('estado', 'ACTIVO')->where('tipo_almacen', 'PRINCIPAL')->get();

        $registrador        =   DB::select(
            'select
                                u.*
                                from users as u where u.id = ?',
            [Auth::user()->id]
        )[0];

        $modelos            =   Modelo::where('estado', 'ACTIVO')->get();
        $tallas             =   UtilidadesController::getTallas();

        $tipos_documento    =   tipos_documento();
        $departamentos      =   departamentos();
        $tipo_clientes      =   tipo_clientes();


        return view(
            'pedidos.pedido.create',
            compact(
                'almacenes',
                'sede_id',
                'empresas',
                'condiciones',
                'modelos',
                'tallas',
                'tipos_documento',
                'departamentos',
                'tipo_clientes',
                'registrador',
                'sede'
            )
        );
    }


    /*
array:18 [
  "_token" => "2RAhjzIPnEzhcBJhovQTN9Q4lUBUNB6LuZtdwuoq"
  "registrador" => "ADMINISTRADOR"
  "fecha_registro" => "2025-02-25"
  "almacen" => "1"
  "condicion_id" => "1"
  "fecha_propuesta" => null
  "cliente" => "1"
  "monto_sub_total" => null
  "monto_embalaje" => null
  "monto_envio" => null
  "monto_total_igv" => null
  "monto_descuento" => null
  "monto_total" => null
  "monto_total_pagar" => null
  "lstPedido" => "[{"producto_id":"1","color_id":"1","modelo_nombre":"","producto_nombre":"PRODUCTO TEST","producto_codigo":"1001","color_nombre":"BLANCO","precio_venta":"1.00","monto_descuento":0,"porcentaje_descuento":0,"precio_venta_nuevo":0,"subtotal_nuevo":0,"tallas":[{"talla_id":"1","talla_nombre":"34","cantidad":"1"}],"subtotal":1},{"producto_id":"1","color_id":"2","modelo_nombre":"","producto_nombre":"PRODUCTO TEST","producto_codigo":"1001","color_nombre":"AZUL","precio_venta":"1.00","monto_descuento":0,"porcentaje_descuento":0,"precio_venta_nuevo":0,"subtotal_nuevo":0,"tallas":[{"talla_id":"1","talla_nombre":"34","cantidad":"3"}],"subtotal":3}]"
  "sede_id" => "1"
  "registrador_id" => "1"
  "amountsPedido" => "{"subtotal":"4.00","embalaje":"0.00","envio":"0.00","total":"3.39","igv":"0.61","totalPagar":"4.00","monto_descuento":"0.00"}"
]
*/
    public function store(PedidoStoreRequest $request)
    {

        DB::beginTransaction();
        try {

            $lstPedido          =   json_decode($request->get('lstPedido'));
            $amountsPedido      =   json_decode($request->get('amountsPedido'));


            //======= MANEJANDO MONTOS ========
            $montos =   PedidoController::calcularMontos($lstPedido, $amountsPedido);

            //======== REGISTRANDO PEDIDO =========
            $pedido                     = new Pedido();
            $pedido->cliente_id         = $request->get('cliente');

            //======== BUSCANDO NOMBRE DEL CLIENTE =====//
            $cliente    =   DB::select('select c.id,c.nombre,c.telefono_movil from clientes as c
                            where c.id=?', [$request->get('cliente')]);

            $pedido->cliente_nombre     =   $cliente[0]->nombre;
            $pedido->cliente_telefono   =   $cliente[0]->telefono_movil;
            //==========================================//

            $pedido->empresa_id         =  1;

            //======== BUSCANDO NOMBRE DE LA EMPRESA =====//
            $empresa    =   DB::select('select e.id,e.razon_social from empresas as e
                            where e.id=?', [1]);

            $pedido->empresa_nombre     =   $empresa[0]->razon_social;
            //==========================================//

            $pedido->condicion_id       = $request->get('condicion_id');
            $pedido->user_id            = $request->get('registrador_id');

            //======== OBTENIENDO EL NOMBRE COMPLETO DEL USUARIO ===========
            $pedido->user_nombre        = User::find($request->get('registrador_id'))->usuario;

            //=============================================================
            $pedido->moneda                 = 1;
            $pedido->fecha_registro         = now()->toDateString();
            $pedido->fecha_propuesta        = $request->get('fecha_propuesta');

            $pedido->monto_embalaje         =   $montos->monto_embalaje;
            $pedido->monto_envio            =   $montos->monto_envio;
            $pedido->sub_total              =   $montos->monto_subtotal;
            $pedido->total_igv              =   $montos->monto_igv;
            $pedido->total                  =   $montos->monto_total;
            $pedido->total_pagar            =   $montos->monto_total_pagar;
            $pedido->monto_descuento        =   $montos->monto_descuento;
            $pedido->porcentaje_descuento   =   $montos->porcentaje_descuento;

            //======= CONTANDO PEDIDOS ======
            $pedido->sede_id    =   $request->get('sede_id');
            $pedido->almacen_id =   $request->get('almacen');
            $pedido->save();


            //========== GRABAR DETALLE DEL PEDIDO ========
            foreach ($lstPedido as $producto) {
                foreach ($producto->tallas as  $talla) {
                    //===== CALCULANDO MONTOS PARA EL DETALLE =====
                    $importe        =   floatval($talla->cantidad) * floatval($producto->precio_venta);
                    $precio_venta   =   $producto->porcentaje_descuento == 0 ? $producto->precio_venta : $producto->precio_venta_nuevo;

                    PedidoDetalle::create([
                        'almacen_id'                => $request->get('almacen'),
                        'pedido_id'                 => $pedido->id,
                        'producto_id'               => $producto->producto_id,
                        'color_id'                  => $producto->color_id,
                        'talla_id'                  => $talla->talla_id,
                        'producto_codigo'           => $producto->producto_codigo,
                        'producto_nombre'           => $producto->producto_nombre,
                        'color_nombre'              => $producto->color_nombre,
                        'talla_nombre'              => $talla->talla_nombre,
                        'modelo_nombre'             => $producto->modelo_nombre,
                        'cantidad'                  => $talla->cantidad,
                        'cantidad_atendida'         => 0,
                        'cantidad_pendiente'        => $talla->cantidad,
                        'precio_unitario'           => $producto->precio_venta,
                        'importe'                   => $importe,
                        'porcentaje_descuento'      =>  floatval($producto->porcentaje_descuento),
                        'precio_unitario_nuevo'     =>  floatval($precio_venta),
                        'importe_nuevo'             =>  floatval($precio_venta) * floatval($talla->cantidad),
                        'monto_descuento'           =>  floatval($importe) * floatval($producto->porcentaje_descuento) / 100,
                    ]);
                }
            }

            //====== REGISTRO DE ACTIVIDAD ========
            $descripcion = "SE AGREGÓ EL PEDIDO CON LA FECHA: " . Carbon::parse($pedido->fecha_registro)->format('d/m/y');
            $gestion = "PEDIDO";
            crearRegistro($pedido, $descripcion, $gestion);

            DB::commit();

            Session::flash('success', 'Pedido creado.');
            return response()->json(['success' => true, 'message' => 'PEDIDO REGISTRADO CON ÉXITO']);
        } catch (\Throwable  $th) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => $th->getMessage(), 'line' => $th->getLine()]);
        }
    }

    public static function calcularMontos($lstPedido, $amountsPedido)
    {
        if (count($lstPedido) === 0) {
            throw new Exception("EL DETALLE DEL PEDIDO ESTÁ VACÍO");
        }

        $monto_subtotal     =   0.0;
        $monto_embalaje     =   $amountsPedido->embalaje ?? 0;
        $monto_envio        =   $amountsPedido->envio ?? 0;
        $monto_total        =   0.0;
        $monto_igv          =   0.0;
        $monto_total_pagar  =   0.0;
        $monto_descuento    =   $amountsPedido->monto_descuento ?? 0;

        foreach ($lstPedido as $producto) {
            foreach ($producto->tallas as $talla) {
                if (floatval($producto->porcentaje_descuento) == 0) {
                    $monto_subtotal +=  ($talla->cantidad * $producto->precio_venta);
                } else {
                    $monto_subtotal +=  ($talla->cantidad * $producto->precio_venta_nuevo);
                }
            }
        }

        $monto_total_pagar      =   $monto_subtotal + $monto_embalaje + $monto_envio;
        $monto_total            =   $monto_total_pagar / 1.18;
        $monto_igv              =   $monto_total_pagar - $monto_total;
        $porcentaje_descuento   =   ($monto_descuento * 100) / ($monto_total_pagar);

        return (object)[
            'monto_embalaje'        =>  $monto_embalaje,
            'monto_envio'           =>  $monto_envio,
            'monto_subtotal'        =>  $monto_subtotal,
            'monto_igv'             =>  $monto_igv,
            'monto_total'           =>  $monto_total,
            'monto_total_pagar'     =>  $monto_total_pagar,
            'monto_descuento'       =>  $monto_descuento,
            'porcentaje_descuento'  =>  $porcentaje_descuento
        ];
    }

    public function edit($id)
    {

        $empresas           =   Empresa::where('estado', 'ACTIVO')->get();
        $condiciones        =   Condicion::where('estado', 'ACTIVO')->get();

        //======= OBTENIENDO DATOS DEL PEDIDO ========
        $pedido             =   Pedido::find($id);
        $pedido_detalles    =   PedidoDetalle::where('pedido_id', $id)->get();

        //========= VALIDAR QUE EL PEDIDO NO ESTÉ FACTURADO =======
        if ($pedido->facturado === 'SI') {
            Session::flash('pedido_error', 'NO SE PUEDEN EDITAR LOS PEDIDOS FACTURADOS');
            return back();
        }

        //======= VALIDAR QUE EL PEDIDO NO ESTÉ ANULADO NI FINALIZADO ======
        if ($pedido->estado === 'ANULADO' || $pedido->estado === "FINALIZADO") {
            Session::flash('pedido_error', 'NO SE PUEDEN EDITAR LOS PEDIDOS ANULADOS O FINALIZADOS');
            return back();
        }

        //========= LOS PEDIDOS NO FACTURADOS PUEDEN EDITARSE ========
        //========= PUEDEN AGREGARSE NUEVOS PRODUCTOS AL PEDIDO =========
        //========= DE LOS PRODUCTOS YA EXISTENTES, PUEDEN EDITARSE LAS CANTIDADES PENDIENTE =======
        //========= DE LOS PRODUCTOS YA EXISTENTES, NO SE PUEDEN TOCAR LAS CANTIDADES ATENDIDAS =====
        //========= LAS CANTIDADES ATENDIDAS PUEDEN MODIFICARSE MEDIANTE NOTAS DE DEVOLUCIÓN/CRÉDITO O CAMBIOS DE TALLA SOBRE EL DOCUMENTO VENTA DE ATENCIÓN =======


        $sede_id            =   Auth::user()->sede_id;
        $sede               =   Sede::find($sede_id);

        $almacenes          =   Almacen::where('estado', 'ACTIVO')->where('tipo_almacen', 'PRINCIPAL')->get();

        $registrador        =   DB::select(
            'select
                                u.*
                                from users as u where u.id = ?',
            [Auth::user()->id]
        )[0];

        $modelos            =   Modelo::where('estado', 'ACTIVO')->get();
        $tallas             =   UtilidadesController::getTallas();
        $pedido_id          =   $id;

        $tipos_documento    =   tipos_documento();
        $departamentos      =   departamentos();
        $tipo_clientes      =   tipo_clientes();
        $cliente            =   Cliente::findOrFail($pedido->cliente_id);

        return view(
            'pedidos.pedido.edit',
            compact(
                'empresas',
                'cliente',
                'almacenes',
                'condiciones',
                'modelos',
                'tallas',
                'pedido',
                'pedido_detalles',
                'tipos_documento',
                'departamentos',
                'tipo_clientes',
                'registrador',
                'sede_id',
                'sede'
            )
        );
    }

    /*
array:11 [
  "_token"          => "2RAhjzIPnEzhcBJhovQTN9Q4lUBUNB6LuZtdwuoq"
  "registrador"     => "CARLOS CUBAS RODRIGUEZ"
  "fecha_registro"  => "2025-02-25"
  "almacen"         => "5"
  "condicion_id"    => "1"
  "fecha_propuesta" => "2025-02-26"
  "cliente" => "1"
  "lstPedido" => "[{"producto_id":1,"producto_nombre":"PRODUCTO TEST","producto_codigo":"1001","modelo_nombre":"","color_id":2,"color_nombre":"AZUL","precio_venta":"1.00","subtotal":10,"subtotal_nuevo":0,"porcentaje_descuento":0,"monto_descuento":0,"precio_venta_nuevo":0,"tallas":[{"talla_id":1,"talla_nombre":"34","cantidad":10}]},{"producto_id":1,"producto_nombre":"PRODUCTO TEST","producto_codigo":"1001","modelo_nombre":"","color_id":3,"color_nombre":"CELESTE","precio_venta":"1.00","subtotal":4,"subtotal_nuevo":0,"porcentaje_descuento":0,"monto_descuento":0,"precio_venta_nuevo":0,"tallas":[{"talla_id":1,"talla_nombre":"34","cantidad":4}]}]"
  "sede_id" => "1"
  "registrador_id" => "1"
  "amountsPedido" => "{"subtotal":"14.00","embalaje":"0.00","envio":"0.00","total":"11.86","igv":"2.14","totalPagar":"14.00","monto_descuento":"0.00"}"
]
*/
    public function update(PedidoUpdateRequest $request, $id)
    {
        DB::beginTransaction();

        try {

            $pedido         =   Pedido::find($id);

            if ($pedido->estado === 'ATENDIENDO' && ($request->has('almacen'))) {
                throw new Exception("LOS PEDIDOS CON ESTADO ATENDIENDO NO PUEDEN CAMBIARSE DE ALMACÉN");
            }

            $productos      =   json_decode($request->get('lstPedido'));
            $amountsPedido  =   json_decode($request->get('amountsPedido'));


            $lstProductos   =   [];

            //======== REFORMATEANDO LST PRODUCTOS =======
            foreach ($productos as $producto) {
                foreach ($producto->tallas as $talla) {
                    $producto   =   (object)[
                        'producto_id' => $producto->producto_id,
                        'color_id' => $producto->color_id,
                        'talla_id' => $talla->talla_id,
                        'producto_nombre' => $producto->producto_nombre,
                        'color_nombre' => $producto->color_nombre,
                        'talla_nombre' => $talla->talla_nombre,
                        'cantidad' => $talla->cantidad
                    ];
                    $lstProductos[] =   $producto;
                }
            }

            //======== VALIDAR LISTADO DE PRODUCTOS ========
            $requestValidacion = new Request([
                'lstProductos'  => json_encode($lstProductos),
                'pedido_id'     => $id
            ]);
            $resValidacion      =   $this->validarCantidadAtendida($requestValidacion);
            $resValidacionData  =   $resValidacion->getData();

            if (!$resValidacionData->success) {
                return $resValidacion;
            }


            //======= MANEJANDO MONTOS ========
            $montos =   PedidoController::calcularMontos($productos, $amountsPedido);

            //======== ACTUALIZAR PEDIDO =========
            $pedido                 = Pedido::find($id);
            $pedido->cliente_id     = $request->get('cliente');


            //======== BUSCANDO NOMBRE DEL CLIENTE =====//
            $cliente    =   DB::select('select
                            c.id,c.nombre,c.telefono_movil
                            from clientes as c
                            where c.id = ?', [$request->get('cliente')]);

            $pedido->cliente_nombre     =   $cliente[0]->nombre;
            $pedido->cliente_telefono   =   $cliente[0]->telefono_movil;

            $pedido->condicion_id       =   $request->get('condicion_id');

            $pedido->moneda                 =   1;

            $pedido->fecha_propuesta        =   $request->get('fecha_propuesta');

            $pedido->monto_embalaje         =   $montos->monto_embalaje;
            $pedido->monto_envio            =   $montos->monto_envio;
            $pedido->sub_total              =   $montos->monto_subtotal;
            $pedido->total_igv              =   $montos->monto_igv;
            $pedido->total                  =   $montos->monto_total;
            $pedido->total_pagar            =   $montos->monto_total_pagar;
            $pedido->monto_descuento        =   $montos->monto_descuento;
            $pedido->porcentaje_descuento   =   $montos->porcentaje_descuento;

            $pedido->update();


            //======= ELIMINANDO DETALLE ANTERIOR, SIEMPRE Y CUANDO NO SE HAYA ATENDIDO AÚN ========
            if (count($productos) > 0) {
                PedidoDetalle::where('pedido_id', $id)
                    ->where('cantidad_atendida', 0)
                    ->delete();
            }

            //========== GRABAR DETALLE DEL PEDIDO ========
            foreach ($productos as $producto) {
                foreach ($producto->tallas as  $talla) {

                    //===== CALCULANDO MONTOS PARA EL DETALLE =====
                    $importe        =   floatval($talla->cantidad) * floatval($producto->precio_venta);
                    $precio_venta   =   $producto->porcentaje_descuento == 0 ? $producto->precio_venta : $producto->precio_venta_nuevo;

                    //======= BUSCANDO SI EXISTE EL PRODUCTO EN EL DETALLE DEL PEDIDO =====
                    $producto_existe                        =   DB::select(
                        'select pd.producto_id,pd.color_id,pd.talla_id,pd.cantidad_atendida
                                                                from pedidos_detalles as pd
                                                                where pd.pedido_id = ? and
                                                                pd.producto_id = ? and
                                                                pd.color_id = ? and
                                                                pd.talla_id = ?',
                        [
                            $id,
                            $producto->producto_id,
                            $producto->color_id,
                            $talla->talla_id
                        ]
                    );


                    //========== EN CASO EL PRODUCTO YA EXISTA EN EL DETALLE =======
                    if (count($producto_existe) === 1) {

                        //====== PREGUNTANDO SI TIENE CANTIDAD ATENDIDA ======
                        if ($producto_existe[0]->cantidad_atendida > 0) {

                            //======= LA NUEVA CANTIDAD DEBE SER MAYOR O IGUAL A LA CANTIDAD ATENDIDA =======
                            if ($talla->cantidad >= $producto_existe[0]->cantidad_atendida) {

                                //============ ACTUALIZAR PRODUCTO EN LA BD ========
                                DB::table('pedidos_detalles')
                                    ->where('pedido_id', $id)
                                    ->where('producto_id', $producto->producto_id)
                                    ->where('color_id', $producto->color_id)
                                    ->where('talla_id', $talla->talla_id)
                                    ->update([
                                        'cantidad'                  => $talla->cantidad,
                                        'cantidad_pendiente'        => $talla->cantidad - $producto_existe[0]->cantidad_atendida,
                                        'precio_unitario'           => $producto->precio_venta,
                                        'importe'                   => $importe,
                                        'porcentaje_descuento'      => floatval($producto->porcentaje_descuento),
                                        'precio_unitario_nuevo'     => floatval($precio_venta),
                                        'importe_nuevo'             => floatval($precio_venta) * floatval($talla->cantidad),
                                        'monto_descuento'           => floatval($importe) * floatval($producto->porcentaje_descuento) / 100,
                                    ]);
                            } else {
                                throw new Exception($producto->producto_nombre . '-' . $producto->color_nombre . '-' . $talla->talla_nombre .
                                    ', LA CANTIDAD NUEVA (' . $talla->cantidad . ') DEBE SER MAYOR O IGUAL A LA CANTIDAD ATENDIDA' . '(' . $producto_existe[0]->cantidad_atendida . ')');
                            }
                        }
                    }

                    //========== EN CASO EL PRODUCTO SEA NUEVO EN EL DETALLE ========
                    if (count($producto_existe) === 0) {
                        $pedido_detalle                         =   new PedidoDetalle();
                        $pedido_detalle->almacen_id             =   $pedido->almacen_id;
                        $pedido_detalle->pedido_id              =   $pedido->id;
                        $pedido_detalle->producto_id            =   $producto->producto_id;
                        $pedido_detalle->color_id               =   $producto->color_id;
                        $pedido_detalle->talla_id               =   $talla->talla_id;
                        $pedido_detalle->producto_codigo        =   $producto->producto_codigo;
                        $pedido_detalle->producto_nombre        =   $producto->producto_nombre;
                        $pedido_detalle->color_nombre           =   $producto->color_nombre;
                        $pedido_detalle->talla_nombre           =   $talla->talla_nombre;
                        $pedido_detalle->modelo_nombre          =   $producto->modelo_nombre;
                        $pedido_detalle->cantidad               =   $talla->cantidad;
                        $pedido_detalle->cantidad_atendida      =   0;
                        $pedido_detalle->cantidad_pendiente     =   $talla->cantidad;
                        $pedido_detalle->precio_unitario        =   $producto->precio_venta;
                        $pedido_detalle->importe                =   $importe;
                        $pedido_detalle->porcentaje_descuento   =   floatval($producto->porcentaje_descuento);
                        $pedido_detalle->precio_unitario_nuevo  =   floatval($precio_venta);
                        $pedido_detalle->importe_nuevo          =   floatval($precio_venta) * floatval($talla->cantidad);
                        $pedido_detalle->monto_descuento        =   floatval($importe) * floatval($producto->porcentaje_descuento) / 100;
                        $pedido_detalle->save();
                    }
                }
            }

            //====== REGISTRO DE ACTIVIDAD ========
            $descripcion = "SE MODIFICÓ EL PEDIDO CON LA FECHA: " . Carbon::parse($pedido->fecha_registro)->format('d/m/y');
            $gestion = "PEDIDO";
            crearRegistro($pedido, $descripcion, $gestion);


            DB::commit();
            Session::flash('success', 'PEDIDO N°' . $id . 'MODIFICADO CON ÉXITO');

            return response()->json(['success' => true, 'message' => 'PEDIDO N°' . $id . 'MODIFICADO CON ÉXITO']);
        } catch (\Throwable  $th) {
            DB::rollback();

            return response()->json(['success' => false, 'message' => $th->getMessage(), 'line' => $th->getLine()]);
        }
    }

    public function destroy($id)
    {
        try {
            //==== ANULANDO PEDIDO ======
            $pedido         =   Pedido::find($id);
            $pedido->estado =   'FINALIZADO';
            $pedido->update();

            return response()->json(['type' => 'success', 'pedido_id' => $id]);
        } catch (\Throwable $th) {
            return response()->json(['type' => 'error', 'message' => $th->getMessage()]);
        }
    }

    public function report($id)
    {
        $pedido             = Pedido::findOrFail($id);
        $tallas             = Talla::all();
        $detalles           = PedidoDetalle::where('pedido_id', $id)->get();
        $empresa            = Empresa::where('id', $pedido->empresa_id)->get()[0];
        $detalles           = $this->formatearArrayDetalle($detalles);

        $vendedor_nombre    = $pedido->user_nombre;


        $pdf = PDF::loadview('pedidos.pedido.reportes.detalle', [
            'pedido'            => $pedido,
            'detalles'          => $detalles,
            'empresa'           => $empresa,
            'tallas'            => $tallas,
            'vendedor_nombre'   => $vendedor_nombre
        ])->setPaper('a4')->setWarnings(false);
        return $pdf->stream('CO-' . $pedido->pedido_nro . '.pdf');
    }

    public function atender(Request $request)
    {
        DB::beginTransaction();
        try {

            //======== OBTENIENDO ID DEL PEDIDO =========
            $pedido_id      =   $request->get('pedido_id');

            //========= OBTENIENDO EL DETALLE DEL PEDIDO =====
            $pedido         =   Pedido::findOrFail($pedido_id);
            $pedido_detalle =   PedidoDetalle::where('pedido_id', $pedido_id)->get();
            $cliente        =   Cliente::findOrFail($pedido->cliente_id);

            //========== EL DOCUMENTO IDENTIDAD DEL CLIENTE DEBE SER IGUAL AL DEL DOC VENTA ANTICIPO ========
            if ($pedido->facturado === 'SI') {
                $doc_anticipo   =   Documento::findOrFail($pedido->documento_venta_facturacion_id);
                if ($doc_anticipo->documento_cliente != $cliente->documento) {
                    throw new Exception("EL DOCUMENTO IDENTIDAD DEL CLIENTE DEBE SER IGUAL AL QUE FIGURA EN EL DOC VENTA ANTICIPO");
                }
            }

            $atencion_detalle = [];
            foreach ($pedido_detalle as  $pedido_item) {

                //======== OBTENIENDO EL STOCK DEL PRODUCTO =======
                $producto_stock =   DB::select(
                    'select
                                    pct.stock_logico
                                    from producto_color_tallas as pct
                                    where
                                    pct.almacen_id = ?
                                    AND pct.producto_id = ?
                                    AND pct.color_id = ?
                                    AND pct.talla_id = ?',
                    [
                        $pedido_item->almacen_id,
                        $pedido_item->producto_id,
                        $pedido_item->color_id,
                        $pedido_item->talla_id
                    ]
                );

                //======= EN CASO EL PRODUCTO COLOR TENGA ESA TALLA EN LA BD ========
                if (count($producto_stock) > 0) {

                    $stock_logico                       =   $producto_stock[0]->stock_logico;
                    $cantidad_pendiente                 =   $pedido_item->cantidad_pendiente;
                    $cantidad_atendida                  =   $pedido_item->cantidad_atendida;
                    $cantidad_solicitada                =   $pedido_item->cantidad;
                    $stock_logico_actualizado           =   0;
                    $cantidad_atender                   =   0;


                    //===== SEPARANDO STOCK LOGICO ======
                    if ($cantidad_pendiente > 0 && $stock_logico > 0) {

                        $cantidad_atender   =   ($stock_logico >= $cantidad_pendiente) ? $cantidad_pendiente : $stock_logico;

                        DB::table('producto_color_tallas')
                            ->where('almacen_id', $pedido_item->almacen_id)
                            ->where('producto_id', $pedido_item->producto_id)
                            ->where('color_id', $pedido_item->color_id)
                            ->where('talla_id', $pedido_item->talla_id)
                            ->update([
                                'stock_logico' => DB::raw("stock_logico - $cantidad_atender")
                            ]);

                        //====== OBTENIENDO NUEVO STOCK_LOGICO ======
                        $producto_stock_actualizado =   DB::select(
                            'select
                                                        pct.stock_logico
                                                        from producto_color_tallas as pct
                                                        where
                                                        pct.almacen_id = ?
                                                        AND pct.producto_id = ?
                                                        AND pct.color_id = ?
                                                        AND pct.talla_id=?',
                            [
                                $pedido_item->almacen_id,
                                $pedido_item->producto_id,
                                $pedido_item->color_id,
                                $pedido_item->talla_id
                            ]
                        );

                        $stock_logico_actualizado   =   $producto_stock_actualizado[0]->stock_logico;
                    }

                    if ($cantidad_pendiente == 0 || $stock_logico == 0) {
                        $stock_logico_actualizado   =   $stock_logico;
                        $cantidad_atender           =   0;
                    }

                    //====== EXISTE EL PRODUCTO COLOR TALLA ======
                    $existe                     =   true;
                }

                //========= EN CASO EL PRODUCTO COLOR NO TENGA ESA TALLA EN BD ==========
                if (count($producto_stock) == 0) {
                    $stock_logico                       =   0;
                    $cantidad_solicitada                =   $pedido_item->cantidad;
                    $cantidad_pendiente                 =   $pedido_item->cantidad_pendiente;
                    $cantidad_atendida                  =   $pedido_item->cantidad_atendida;
                    $stock_logico_actualizado           =   0;
                    $cantidad_atender                   =   0;

                    //====== EXISTE EL PRODUCTO COLOR TALLA ======
                    $existe                     =   false;
                }

                $atencion_item = (object)[
                    'modelo_nombre'             => $pedido_item->modelo_nombre,
                    'almacen_id'                => $pedido_item->almacen_id,
                    'producto_id'               => $pedido_item->producto_id,
                    'producto_codigo'           => $pedido_item->producto_codigo,
                    'producto_nombre'           => $pedido_item->producto_nombre,
                    'color_id'                  => $pedido_item->color_id,
                    'color_nombre'              => $pedido_item->color_nombre,
                    'talla_id'                  => $pedido_item->talla_id,
                    'talla_nombre'              => $pedido_item->talla_nombre,
                    'precio_unitario'           => $pedido_item->precio_unitario,
                    'porcentaje_descuento'      => $pedido_item->porcentaje_descuento,
                    'precio_unitario_nuevo'     => $pedido_item->precio_unitario_nuevo,
                    'stock_logico_actualizado'  => $stock_logico_actualizado,
                    'stock_logico'              => $stock_logico,
                    'cantidad_solicitada'       => $cantidad_solicitada,
                    'cantidad_atendida'         => $cantidad_atendida,
                    'cantidad_pendiente'        => $cantidad_pendiente,
                    'cantidad'                  => $cantidad_atender,
                    'existe'                    => $existe,
                ];

                $atencion_detalle[]     =   $atencion_item;
            }

            $empresas           =   Empresa::where('estado', 'ACTIVO')->get();
            $condiciones        =   Condicion::where('estado', 'ACTIVO')->get();

            $modelos            =   Modelo::where('estado', 'ACTIVO')->get();
            $tallas             =   Talla::where('estado', 'ACTIVO')->get();
            $tipoVentas         =   tipos_venta()
                                    ->whereIn('id', [129])
                                    ->where('estado', 'ACTIVO');

            //======= SI EL PEDIDO YA FUE FACTURADO, PERMITIR SOLO ATENDER DE ACUERDO AL DOC DEL CLIENTE ======
            if ($pedido->facturado === 'SI') {

                $doc_venta  =   Documento::where('pedido_id', $pedido->id)->first();
                if (!$doc_venta) {
                    throw new Exception('NO SE ENCUENTRA EL DOC DE VENTA CON EL QUE SE FACTURÓ EL PEDIDO');
                }

                Session::flash(
                    'pedido_facturado_atender',
                    'LOS PEDIDOS SOLO DEBEN ATENDERSE CON <strong>' . 'NOTA DE VENTA' . '</strong>,
                    FACTURADO CON EL DOC: <strong>' . $doc_venta->serie . '-' . $doc_venta->correlativo . '</strong>'
                );
            }

            $departamentos  =   departamentos();

            $almacen        =   Almacen::find($pedido->almacen_id);
            $sede           =   Sede::find($pedido->sede_id);
            $registrador    =   Colaborador::find(Auth::user()->colaborador_id);

            $origenes_ventas    =   UtilidadesController::getOrigenesVentas();
            $tipos_pago_envio   =   UtilidadesController::getTiposPagoEnvio();

            $tipos_envio        =   UtilidadesController::getTiposEnvio();
            $tipos_documento    =   UtilidadesController::getTiposDocumento();
            $cuentas            =   UtilidadesController::getCuentas();

            DB::commit();
            return view(
                'pedidos.pedido.atender',
                compact(
                    'atencion_detalle',
                    'empresas',
                    'cliente',
                    'pedido_detalle',
                    'condiciones',
                    'tallas',
                    'pedido',
                    'tipoVentas',
                    'departamentos',
                    'almacen',
                    'sede',
                    'registrador',
                    'origenes_ventas',
                    'tipos_pago_envio',
                    'tipos_envio',
                    'tipos_documento',
                    'cuentas'
                )
            );
        } catch (Throwable $th) {
            DB::rollback();
            Session::flash('message_error', $th->getMessage());
            return redirect()->route('pedidos.pedido.index');
        }
    }



    public function getAtenciones($pedido_id)
    {
        $pedido_atenciones    =   DB::select('select cd.serie as documento_serie,cd.correlativo as documento_correlativo,
                                cd.created_at as fecha_atencion ,CONCAT(p.nombres, " ", p.apellido_paterno, " ", p.apellido_materno) AS documento_usuario,
                                cd.monto_envio as documento_monto_envio, cd.monto_embalaje as documento_monto_embalaje,
                                cd.total_pagar as documento_total_pagar,cd.pedido_id,cd.id as documento_id
                                from  cotizacion_documento as cd
                                inner join user_persona as up on cd.user_id = up.user_id
                                inner join personas as p  on p.id = up.persona_id
                                where cd.pedido_id=? and cd.tipo_doc_venta_pedido = "ATENCION" ', [$pedido_id]);


        return  response()->json(['type' => 'success', 'pedido_atenciones' => $pedido_atenciones]);
    }

    public function getAtencionDetalles($pedido_id, $documento_id)
    {

        $atencion_detalles    =   DB::select('select cdd.nombre_producto as producto_nombre,
                                cdd.nombre_color as color_nombre, cdd.nombre_talla as talla_nombre,
                                cdd.cantidad
                                from cotizacion_documento as cd
                                inner join cotizacion_documento_detalles  as cdd on cdd.documento_id = cd.id
                                where cd.pedido_id=? and cd.id = ?', [$pedido_id, $documento_id]);


        return  response()->json(['type' => 'success', 'atencion_detalles' => $atencion_detalles]);
    }

    public function formatearArrayDetalleObjetos($detalles)
    {
        $detalleFormateado = [];
        $productosProcesados = [];
        foreach ($detalles as $detalle) {
            $cod   =   $detalle->producto_id . '-' . $detalle->color_id;
            if (!in_array($cod, $productosProcesados)) {
                $producto = [];
                //======== obteniendo todas las detalle talla de ese producto_color =================
                $producto_color_tallas = $detalles->filter(function ($detalleFiltro) use ($detalle) {
                    return $detalleFiltro->producto_id == $detalle->producto_id && $detalleFiltro->color_id == $detalle->color_id;
                });

                $producto['producto_codigo']        =   $detalle->producto_codigo;
                $producto['producto_id']            =   $detalle->producto_id;
                $producto['color_id']               =   $detalle->color_id;
                $producto['producto_nombre']        =   $detalle->producto_nombre;
                $producto['color_nombre']           =   $detalle->color_nombre;
                $producto['modelo_nombre']          =   $detalle->modelo_nombre;
                $producto['precio_unitario']        =   $detalle->precio_unitario;
                $producto['porcentaje_descuento']   =   $detalle->porcentaje_descuento;
                $producto['precio_unitario_nuevo']  =   $detalle->precio_unitario_nuevo;
                $producto['monto_descuento']        =   $detalle->monto_descuento;
                $producto['precio_venta']           =   $detalle->precio_unitario;
                $producto['precio_venta_nuevo']     =   $detalle->precio_unitario_nuevo;

                $tallas             =   [];
                $subtotal           =   0.0;
                $subtotal_with_desc =   0.0;
                $cantidadTotal = 0;
                foreach ($producto_color_tallas as $producto_color_talla) {
                    $talla = [];
                    $talla['talla_id']              =   $producto_color_talla->talla_id;


                    $talla['cantidad']              =   (int)$producto_color_talla->cantidad;
                    $subtotal                       +=  $talla['cantidad'] * $producto['precio_unitario_nuevo'];
                    $cantidadTotal                  +=  $talla['cantidad'];


                    $talla['talla_nombre']          =   $producto_color_talla->talla_nombre;

                    array_push($tallas, (object)$talla);
                }

                $producto['tallas']                 =   $tallas;
                $producto['subtotal']               =   $subtotal;
                $producto['cantidad_total']         =   $cantidadTotal;
                array_push($detalleFormateado, (object)$producto);
                $productosProcesados[] = $detalle->producto_id . '-' . $detalle->color_id;
            }
        }
        return $detalleFormateado;
    }

    public function formatearArrayDetalle($detalles)
    {
        $detalleFormateado = [];
        $productosProcesados = [];
        foreach ($detalles as $detalle) {
            $cod   =   $detalle->producto_id . '-' . $detalle->color_id;
            if (!in_array($cod, $productosProcesados)) {
                $producto = [];
                //======== obteniendo todas las detalle talla de ese producto_color =================
                $producto_color_tallas = $detalles->filter(function ($detalleFiltro) use ($detalle) {
                    return $detalleFiltro->producto_id == $detalle->producto_id && $detalleFiltro->color_id == $detalle->color_id;
                });

                $producto['producto_codigo']        =   $detalle->producto_codigo;
                $producto['producto_id']            =   $detalle->producto_id;
                $producto['color_id']               =   $detalle->color_id;
                $producto['producto_nombre']        =   $detalle->producto_nombre;
                $producto['color_nombre']           =   $detalle->color_nombre;
                $producto['modelo_nombre']          =   $detalle->modelo_nombre;
                $producto['precio_unitario']        =   $detalle->precio_unitario;
                $producto['porcentaje_descuento']   =   $detalle->porcentaje_descuento;
                $producto['precio_unitario_nuevo']  =   $detalle->precio_unitario_nuevo;
                $producto['monto_descuento']        =   $detalle->monto_descuento;
                $producto['precio_venta']           =   $detalle->precio_unitario;
                $producto['precio_venta_nuevo']     =   $detalle->precio_unitario_nuevo;

                $tallas             =   [];
                $subtotal           =   0.0;
                $subtotal_with_desc =   0.0;
                $cantidadTotal = 0;
                foreach ($producto_color_tallas as $producto_color_talla) {
                    $talla = [];
                    $talla['talla_id']              =   $producto_color_talla->talla_id;


                    $talla['cantidad']              =   $producto_color_talla->cantidad;
                    $subtotal                       +=  $talla['cantidad'] * $producto['precio_unitario_nuevo'];
                    $cantidadTotal                  +=  $talla['cantidad'];


                    $talla['talla_nombre']          =   $producto_color_talla->talla_nombre;

                    array_push($tallas, $talla);
                }

                $producto['tallas']                 =   $tallas;
                $producto['subtotal']               =   $subtotal;
                $producto['cantidad_total']         =   $cantidadTotal;
                array_push($detalleFormateado, $producto);
                $productosProcesados[] = $detalle->producto_id . '-' . $detalle->color_id;
            }
        }
        return $detalleFormateado;
    }

    public function getColoresTallas($almacen_id, $producto_id)
    {

        try {

            $precios_venta  =   DB::select(
                'SELECT
                                p.id AS producto_id,
                                p.nombre AS producto_nombre,
                                p.precio_venta_1,
                                p.precio_venta_2,
                                p.precio_venta_3
                                FROM
                                    productos AS p
                                WHERE
                                    p.id = ? AND p.estado = "ACTIVO" ',
                [$producto_id]
            );


            $colores =  DB::select(
                'SELECT
                                    p.id AS producto_id,
                                    p.nombre AS producto_nombre,
                                    c.id AS color_id,
                                    c.descripcion AS color_nombre,
                                    p.codigo as producto_codigo
                                FROM
                                    producto_colores AS pc
                                    inner join productos as p on p.id = pc.producto_id
                                    inner join colores as c on c.id = pc.color_id
                                WHERE
                                    pc.almacen_id = ?
                                    AND pc.producto_id = ?
                                    AND p.estado = "ACTIVO"
                                    AND c.estado = "ACTIVO" ',
                [$almacen_id, $producto_id]
            );

            $stocks =   DB::select(
                'select
                        pct.producto_id,
                        pct.color_id,
                        pct.talla_id,
                        pct.stock,
                        pct.stock_logico,
                        t.descripcion as talla_nombre
                        from producto_color_tallas as pct
                        inner join productos as p on p.id = pct.producto_id
                        inner join colores as c on c.id = pct.color_id
                        inner join tallas as t on t.id = pct.talla_id
                        where
                        p.estado = "ACTIVO"
                        AND c.estado = "ACTIVO"
                        AND t.estado = "ACTIVO"
                        AND pct.almacen_id = ?
                        AND p.id = ?',
                [$almacen_id, $producto_id]
            );

            $tallas =   Talla::where('estado', 'ACTIVO')->orderBy('id')->get();

            $producto_color_tallas  =   null;
            if (count($colores) > 0) {
                $producto_color_tallas  =   $this->formatearColoresTallas($colores, $stocks, $precios_venta, $tallas);
            }

            return response()->json(['success' => true, 'producto_color_tallas' => $producto_color_tallas]);
        } catch (\Throwable $th) {

            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function formatearColoresTallas($colores, $stocks, $precios_venta, $tallas)
    {

        $producto = [];

        // Verifica si $colores no está vacío
        if (count($colores) > 0) {
            $producto['id']     = $colores[0]->producto_id;
            $producto['nombre'] = $colores[0]->producto_nombre;
            $producto['codigo'] = $colores[0]->producto_codigo;
        } else {
            // Maneja el caso cuando $colores está vacío
            $producto['id']     = null;
            $producto['nombre'] = null;
            $producto['codigo'] = null;
        }

        // Verifica si $precios_venta no está vacío
        if (count($precios_venta) > 0) {
            $producto['precio_venta_1'] = $precios_venta[0]->precio_venta_1;
            $producto['precio_venta_2'] = $precios_venta[0]->precio_venta_2;
            $producto['precio_venta_3'] = $precios_venta[0]->precio_venta_3;
        } else {
            // Maneja el caso cuando $precios_venta está vacío
            $producto['precio_venta_1'] = null;
            $producto['precio_venta_2'] = null;
            $producto['precio_venta_3'] = null;
        }

        $lstColores = [];

        //======== RECORRIENDO COLORES =======
        foreach ($colores as $color) {
            $item_color = [];
            $item_color['id']       =   $color->color_id;
            $item_color['nombre']   =   $color->color_nombre;

            //======== OBTENIENDO TALLAS DEL COLOR =======
            $lstTallas = [];

            foreach ($tallas as $talla) {
                $item_talla = [];
                $item_talla['id'] = $talla->id;
                $item_talla['nombre'] = $talla->descripcion;

                // Filtrar stocks para color y talla actuales
                $stock_filtrado = array_filter($stocks, function ($stock) use ($producto, $color, $talla) {
                    return $stock->producto_id == $producto['id'] &&
                        $stock->color_id == $color->color_id &&
                        $stock->talla_id == $talla->id;
                });

                // Asignar stock y stock lógico si existe, o establecer en 0
                if (!empty($stock_filtrado)) {
                    $first_stock = reset($stock_filtrado); // Obtiene el primer elemento del array filtrado
                    $item_talla['stock'] = $first_stock->stock;
                    $item_talla['stock_logico'] = $first_stock->stock_logico;
                } else {
                    $item_talla['stock'] = 0;
                    $item_talla['stock_logico'] = 0;
                }

                $lstTallas[] = $item_talla;
            }

            $item_color['tallas'] = $lstTallas;
            $lstColores[] = $item_color;
        }

        $producto['colores'] = $lstColores;

        return $producto;
    }


    public function getProductosByModelo($modelo_id)
    {
        try {
            $productos  =   DB::select('select p.id,p.nombre
                            from productos as p
                            where p.modelo_id = ? and p.estado = "ACTIVO"', [$modelo_id]);

            return response()->json(['success' => true, 'productos' => $productos]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    // public function getProductosByModelo($modelo_id){
    //     try {
    //         $productos  =   DB::select('select distinct p.id as producto_id,c.id as color_id, t.id as talla_id,
    //         m.id as modelo_id, p.nombre as producto_nombre,c.descripcion as color_nombre,
    //         t.descripcion as talla_nombre,pct.stock_logico,m.descripcion as modelo_nombre,p.codigo as producto_codigo,
    //         p.precio_venta_1,p.precio_venta_2,p.precio_venta_3
    //         from producto_colores as pc
    //         left join producto_color_tallas as pct on (pc.producto_id=pct.producto_id and pc.color_id=pct.color_id)
    //         inner join productos as p on p.id=pc.producto_id
    //         inner join colores as c on c.id=pc.color_id
    //         inner join modelos as m on m.id=p.modelo_id
    //         left join tallas as t on t.id=pct.talla_id
    //         where m.id=?
    //         order by p.nombre,c.descripcion',[$modelo_id]);

    //        $productos_formateado    =   $this->formatearListado($productos);

    //         return response()->json(['type'=>'success','message'=>$productos_formateado]);

    //     } catch (\Throwable $e) {
    //         return response()->json(['type'=>'error','message'=>$e->getMessage()]);
    //     }
    // }

    public function formatearListado($productos)
    {
        $productos_formateado       =   [];
        $producto_color_procesados  =   [];
        $llave                      =   '';

        $productos_procesados       =   [];
        $llave_2                    =   '';


        //====== FORMATEANDO =====
        foreach ($productos as $producto) {
            $llave      =   $producto->producto_id . '-' . $producto->color_id;
            $llave_2    =   $producto->producto_id;
            if (!in_array($llave, $producto_color_procesados)) {
                $producto_color =   [];
                $producto_color['producto_id']          =   $producto->producto_id;
                $producto_color['color_id']             =   $producto->color_id;
                $producto_color['producto_nombre']      =   $producto->producto_nombre;
                $producto_color['producto_codigo']      =   $producto->producto_codigo;
                $producto_color['color_nombre']         =   $producto->color_nombre;
                $producto_color['modelo_nombre']        =   $producto->modelo_nombre;
                $producto_color['porcentaje_descuento'] =   0;
                $producto_color['precio_venta_1']       =   $producto->precio_venta_1;
                $producto_color['precio_venta_2']       =   $producto->precio_venta_2;
                $producto_color['precio_venta_3']       =   $producto->precio_venta_3;


                //==== OBTENIENDO LAS TALLAS ====
                $tallas = array_filter($productos, function ($p) use ($producto) {
                    return $p->producto_id == $producto->producto_id && $p->color_id == $producto->color_id;
                });

                $producto_color_tallas = [];
                foreach ($tallas as $talla) {
                    //====== CONSTRUYENDO TALLA =====
                    $producto_color_talla                       =   [];
                    $producto_color_talla['talla_id']           =   $talla->talla_id;
                    $producto_color_talla['talla_nombre']       =   $talla->talla_nombre;
                    $producto_color_talla['stock_logico']       =   $talla->stock_logico;
                    $producto_color_talla['precio_unitario']    =   0;


                    //====== GUARDANDO TALLA DEL PRODUCTO COLOR ====
                    $producto_color_tallas[]   =   $producto_color_talla;
                }
                $producto_color['tallas']   =   $producto_color_tallas;
                if (!in_array($llave_2, $productos_procesados)) {
                    $producto_color['print_precios']   =   true;
                } else {
                    $producto_color['print_precios']   =   false;
                }

                $productos_formateado[]         =   $producto_color;
                $producto_color_procesados[]    =   $llave;
            }
            $productos_procesados[]    =   $llave_2;
        }

        return $productos_formateado;
    }

/*
array:6 [
  "cantidad_atender_anterior" => 1
  "cantidad_atender_nueva" => 1
  "almacen_id" => 5
  "producto_id" => "1"
  "color_id" => "3"
  "talla_id" => "1"
]
*/
    public function validarCantidadAtender(Request $request)
    {
        DB::beginTransaction();
        try {
            $data   =   $request->all();

            $cantidad_atender_nueva         =   $request->get('cantidad_atender_nueva');
            $cantidad_atender_anterior      =   $request->get('cantidad_atender_anterior');

            $almacen_id         =   $request->get('almacen_id');
            $producto_id        =   $request->get('producto_id');
            $color_id           =   $request->get('color_id');
            $talla_id           =   $request->get('talla_id');

            //======== OBTENER PRODUCTO ======
            $producto   =   DB::select(
                'select
                            pct.stock_logico
                            from producto_color_tallas as pct
                            where
                            pct.almacen_id = ?
                            and pct.producto_id = ?
                            and pct.color_id = ?
                            and pct.talla_id=?',
                [
                    $almacen_id,
                    $producto_id,
                    $color_id,
                    $talla_id
                ]
            );

            if (count($producto) > 0) {
                $stock_logico   =   $producto[0]->stock_logico;

                //======= SI LA NUEVA CANTIDAD ATENDER ES MENOR IGUAL AL STOCK LOGICO REPUESTO ======
                $stock_logico_repuesto  =   $stock_logico + $cantidad_atender_anterior;
                if ($cantidad_atender_nueva <= $stock_logico_repuesto) {
                    //======== ACTUALIZAR STOCK_LOGICO ======
                    DB::table('producto_color_tallas')
                        ->where('almacen_id', $almacen_id)
                        ->where('producto_id', $producto_id)
                        ->where('color_id', $color_id)
                        ->where('talla_id', $talla_id)
                        ->update([
                            'stock_logico' => DB::raw('stock_logico + ' . ($cantidad_atender_anterior - $cantidad_atender_nueva)),
                        ]);

                    DB::commit();
                    return response()->json(['type' => 'success', 'data' => $data, 'message' => 'Stock lógico actualizado']);
                } else {
                    return response()->json(['type' => 'error', 'data' => $data, 'message' => 'Stock lógico (' . $stock_logico_repuesto . ') es menor
                    a la cantidad que se quiere atender (' . $cantidad_atender_nueva . ')']);
                }
            } else {
                return response()->json(['type' => 'error', 'data' => $data, 'message' => 'El producto no existe']);
            }
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['type' => 'error', 'data' => $th->getMessage(), 'message' => 'Error en el servidor']);
        }
    }

/*
array:23 [
  "_token" => "5MGXGnvyPqmaWivHZqfgugbvucN0v7DZj6Q0bkUX"
  "registrador" => "ADMIN"
  "almacen" => "CHACHAPOYAS PRINCIPAL"
  "fecha_atencion_campo" => "2025-02-26"
  "fecha_vencimiento_campo" => "2025-02-26"
  "tipo_venta" => "129"
  "condicion_id" => "1"
  "modo" => "ATENCION"
  "cliente_id" => "1"
  "productos_tabla" => "[{"producto_id":1,"color_id":3,"talla_id":1,"cantidad":1,"precio_unitario":"1.00","porcentaje_descuento":0,"precio_unitario_nuevo":0}]"
  "igv" => "18"
  "igv_check" => "on"
  "efectivo" => "0"
  "importe" => "0"
  "empresa_id" => "1"
  "monto_sub_total" => "1.00"
  "monto_embalaje" => "21.00"
  "monto_envio" => "10.00"
  "monto_total_igv" => "4.88"
  "monto_descuento" => "0.00"
  "monto_total" => "27.12"
  "monto_total_pagar" => "32.00"
  "data_envio" => null
  "pedido_id" => "2"
  "data_envio" => "{opcional}
]
*/
    public function generarDocumentoVenta(PedidoDocVentaRequest $request)
    {
        DB::beginTransaction();
        try {

            $res    =   $this->s_pedido->generarDocumentoVenta($request->toArray());

            DB::commit();

            return response()->json([
                'success'       => true,
                'documento_id'  => $res,
                'mensaje'       => 'DOCUMENTO DE VENTA GENERADO - PEDIDO ACTUALIZADO'
            ]);

        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage(),'line'=>$th->getLine()]);
        }
    }

    public function validarTipoVenta($comprobante_id)
    {
        try {
            $estado = DB::table('empresa_numeracion_facturaciones')
                ->where('tipo_comprobante', $comprobante_id)
                ->where('estado', 'ACTIVO')
                ->exists();

            $message = "";
            if ($estado) {
                $message    =   "TIPO DE COMPROBANTE ACTIVO EN LA EMPRESA";
            } else {
                $message    =   "TIPO DE COMPROBANTE NO ESTÁ ACTIVO EN LA EMPRESA";
            }

            return response()->json(['type' => 'success', 'estado' => $estado, 'message' => $message]);
        } catch (\Throwable $th) {
            return response()->json(['type' => 'error', 'message' => 'ERROR EN EL SERVIDOR', 'exception' => $th->getMessage()]);
        }
    }

    public function devolverStockLogico(Request $request)
    {
        $productos  =  json_decode($request->get('carrito'));

        foreach ($productos as $producto) {
            foreach ($producto->tallas as $talla) {
                if ($talla->existe && $talla->cantidad_atender > 0) {

                    DB::update(
                        'UPDATE producto_color_tallas
                    SET stock_logico = stock_logico + ?
                    WHERE
                    almacen_id = ?
                    and producto_id = ?
                    and color_id = ?
                    and talla_id = ?',
                        [
                            $talla->cantidad_atender,
                            $producto->almacen_id,
                            $producto->producto_id,
                            $producto->color_id,
                            $talla->talla_id
                        ]
                    );
                }
            }
        }
    }

    public function getPedidoDetalles($pedido_id)
    {

        try {
            $pedido_detalles    =   PedidoDetalle::where('pedido_id', $pedido_id)->get();

            return response()->json(['type' => 'success', 'pedido_detalles' => $pedido_detalles]);
        } catch (\Throwable $th) {
            return response()->json(['type' => 'error', 'exception' => $th->getMessage(), 'message' => 'ERROR EN EL SERVIDOR']);
        }
    }


    public function getExcel($fecha_inicio = null, $fecha_fin = null, $estado = null)
    {
        $fecha_inicio   =   $fecha_inicio == "null" ? null : $fecha_inicio;
        $fecha_fin      =   $fecha_fin == "null" ? null : $fecha_fin;
        $estado         =   $estado == "null" ? null : $estado;


        return  Excel::download(new PedidosExport($fecha_inicio, $fecha_fin, $estado), 'REPORTE-PEDIDOS' . '.xlsx');
    }

    /*
array:9 [
  "_token" => "BUwUdTR0MfLCxd0DVA2zKRPDaN5yrlOm51d9wGAj"
  "registrador" => "ADMINISTRADOR"
  "fecha_registro" => "2025-07-21"
  "almacen" => "CENTRAL"
  "condicion_id" => "CONTADO"
  "fecha_propuesta" => "2025-07-21"
  "cliente" => "ZAYRA CECIBEL VILELA JARAMILLO DE QUISPE"
  "comprobante" => "129"
  "pedido_id" => "666"
]
*/
    public function facturarStore(Request $request)
    {
        DB::beginTransaction();
        try {
            $doc_venta   =   $this->s_pedido->facturar($request->toArray());

            DB::commit();
            return response()->json(
                [
                    'success'       =>  true,
                    'message'       =>  'SE HA GENERADO EL DOCUMENTO DE VENTA ' . $doc_venta->serie . '-' . $doc_venta->correlativo,
                    'documento_id'  =>  $doc_venta->id
                ]
            );
        } catch (Throwable $th) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => $th->getMessage(), 'line' => $th->getLine(),'file'=>$th->getFile()]);
        }
    }

    public function getCliente($pedido_id)
    {

        try {
            $pedido     =   Pedido::find($pedido_id);

            $cliente    =   DB::select('select
                            c.*
                            from clientes as c
                            where c.id = ?', [$pedido->cliente_id]);


            if (count($cliente) !== 1) {
                throw new \Exception('CLIENTE NO ENCONTRADO');
            }

            return response()->json(['success' => true, 'cliente' => $cliente[0]]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }


    /*
array:2 [
  "pedido_id"       => 2
  "lstProductos"    => "[{"producto_id":"1","producto_nombre":"PRODUCTO TEST","producto_codigo":null,"modelo_nombre":"","color_id":"2","color_nombre":"AZUL","talla_id":"1","talla_nombre":"34","cantidad":"10","precio_venta":"1.00","subtotal":0,"subtotal_nuevo":0,"porcentaje_descuento":0,"monto_descuento":0,"precio_venta_nuevo":0},{"producto_id":"1","producto_nombre":"PRODUCTO TEST","producto_codigo":null,"modelo_nombre":"","color_id":"3","color_nombre":"CELESTE","talla_id":"1","talla_nombre":"34","cantidad":"4","precio_venta":"1.00","subtotal":0,"subtotal_nuevo":0,"porcentaje_descuento":0,"monto_descuento":0,"precio_venta_nuevo":0}]"
]
*/
    public function validarCantidadAtendida(Request $request)
    {

        try {

            $lstProductos   =   json_decode($request->get('lstProductos'));
            $pedido_id      =   $request->get('pedido_id');

            $lstProductosValidados  =   [];
            $lstErroresValidacion   =   [];

            foreach ($lstProductos as $producto) {

                //========= OBTENIENDO CANTIDAD NUEVA ======
                $cantidad_nueva =   $producto->cantidad;

                //======== OBTENIENDO LA CANTIDAD ATENDIDA DEL PRODUCTO EN TIEMPO REAL =========
                $producto_en_detalle    =   DB::select(
                    'select
                                            pd.cantidad_atendida,
                                            pd.cantidad_pendiente,
                                            pd.producto_id,
                                            pd.color_id,
                                            pd.talla_id
                                            from pedidos_detalles as pd
                                            where
                                            pd.pedido_id = ?
                                            and pd.producto_id = ?
                                            and pd.color_id = ?
                                            and pd.talla_id = ?',
                    [
                        $pedido_id,
                        $producto->producto_id,
                        $producto->color_id,
                        $producto->talla_id
                    ]
                );

                //======== EN CASO EL PRODUCTO EXISTA EN EL DETALLE PREVIAMENTE ======
                if (count($producto_en_detalle) === 1) {

                    //======= VALIDAR CANTIDAD NUEVA CON LA CANTIDAD ATENDIDA =======
                    //======= LA CANTIDAD NUEVA DEBE SER MAYOR O IGUAL A LA CANTIDAD ATENDIDA ======
                    if ($cantidad_nueva < $producto_en_detalle[0]->cantidad_atendida) {

                        $mensaje    =   $producto->producto_nombre . "-" . $producto->color_nombre . "-" . $producto->talla_nombre .
                            ", CANT NUEVA(" . $cantidad_nueva . ") DEBE SER MAYOR O IGUAL A CANT ATEND(" . $producto_en_detalle[0]->cantidad_atendida . ").";

                        throw new Exception($mensaje);

                        $producto->validacion           =   false;
                        //$producto->mensaje_validacion   =   $mensaje;
                        $lstErroresValidacion[] =   (object)[
                            'producto_id' => $producto->producto_id,
                            'color_id' => $producto->color_id,
                            'talla_id' => $producto->talla_id,
                            'mensaje' => $mensaje
                        ];
                    } else {

                        $producto->validacion           =   true;
                        $producto->mensaje_validacion   =   '';
                    }

                    $lstProductosValidados[]    =   $producto;
                }

                //========= EN CASO EL PRODUCTO SEA NUEVO =======
                if (count($producto_en_detalle) === 0) {

                    if ($producto->cantidad > 0) {
                        $producto->validacion           =   true;
                        $lstProductosValidados[]        =   $producto;
                    }
                }
            }

            /*return (object)[
            'success'=>true,
            'lstProductosValidados'=>$lstProductosValidados,
            'lstErroresValidacion'=>$lstErroresValidacion];*/

            return response()->json(['success' => true, 'message' => 'CANTIDADES VALIDADAS']);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }


    public function getProductos(Request $request)
    {

        try {

            $search         = $request->query('search'); // Palabra clave para la búsqueda
            $almacenId      = $request->query('almacen_id'); // ID del almacén
            $page           = $request->query('page', 1);

            if (!$almacenId) {
                throw new Exception("FALTA SELECCIONAR UN ALMACÉN!!!");
            }

            $productos  =   DB::table('productos as p')
                ->join('categorias as c', 'c.id', 'p.categoria_id')
                ->join('marcas as ma', 'ma.id', 'p.marca_id')
                ->join('modelos as mo', 'mo.id', 'p.modelo_id')
                ->leftJoin('producto_color_tallas as pct', 'p.id', 'pct.producto_id')
                ->select(
                    DB::raw("CONCAT(c.descripcion, ' - ', ma.marca, ' - ', mo.descripcion, ' - ', p.nombre) as producto_completo"),
                    'c.descripcion as categoria_nombre',
                    'ma.marca as marca_nombre',
                    'mo.descripcion as modelo_nombre',
                    'p.id as producto_id',
                    'p.nombre as producto_nombre',
                    'pct.almacen_id'
                )
                ->where(DB::raw("CONCAT(c.descripcion, ' - ', ma.marca, ' - ', mo.descripcion, ' - ', p.nombre)"), 'LIKE', "%$search%")
                ->where('pct.almacen_id', $almacenId)
                ->where('p.estado', 'ACTIVO')
                ->groupBy('pct.almacen_id', 'p.id', 'c.descripcion', 'ma.marca', 'mo.descripcion', 'p.nombre')
                ->paginate(10, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'message' => 'PRODUCTOS OBTENIDOS',
                'productos' => $productos->items(),
                'more' => $productos->hasMorePages()
            ]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function facturarCreate(int $id)
    {
        try {


            $pedido                 = Pedido::findOrFail($id);
            $registrador_nombre     = Auth::user()->usuario;

            if ($pedido->facturado === 'SI') {
                throw new Exception("ESTE PEDIDO PE-" . $id . ",YA FUE FACTURADO");
            }

            if ($pedido->user_id !== Auth::user()->id) {
                throw new Exception("PEDIDO PE-" . $id . ",SOLO EL USUARIO QUE LO CREÓ PUEDE FACTURARLO");
            }

            $detalle            =   PedidoDetalle::where('pedido_id', $id)->where('tipo','PRODUCTO')->get();
            $detalle            =   $this->formatearArrayDetalleObjetos($detalle);

            $tallas             =   Talla::where('estado', 'ACTIVO')->get();
            $almacen            =   Almacen::findOrFail($pedido->almacen_id);
            $condicion          =   Condicion::findOrFail($pedido->condicion_id);
            $cliente            =   Cliente::findOrFail($pedido->cliente_id);

            $tipos_ventas   =   tipos_venta()->whereIn('id', [127, 128, 129]);

            //===== RETIRAR BOLETA =======
            if ($cliente->tipo_documento === 'RUC') {
                $tipos_ventas   =   $tipos_ventas->where('id', '<>', 128);
            }
            if ($cliente->tipo_documento === 'DNI') {
                $tipos_ventas   =   $tipos_ventas->where('id', '<>', 127);
            }

            return view(
                'pedidos.pedido.facturar',
                compact('pedido', 'detalle', 'registrador_nombre', 'tallas', 'almacen', 'condicion', 'tipos_ventas')
            );
        } catch (Throwable $th) {
            Session::flash('message_error', $th->getMessage());
            return redirect()->route('pedidos.pedido.index');
        }
    }
}
