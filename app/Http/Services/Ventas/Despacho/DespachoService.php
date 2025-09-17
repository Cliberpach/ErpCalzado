<?php

namespace App\Http\Services\Ventas\Despacho;

use App\Almacenes\Almacen;
use App\Mantenimiento\MetodoEntrega\EmpresaEnvioSede;
use App\Mantenimiento\MetodoEntrega\MetodoEntrega;
use App\Mantenimiento\Tabla\Detalle;
use App\Mantenimiento\Ubigeo\Departamento;
use App\Mantenimiento\Ubigeo\Distrito;
use App\Mantenimiento\Ubigeo\Provincia;
use App\Ventas\Documento\Documento;
use App\Ventas\EnvioVenta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DespachoService
{

    public function __construct() {}

    public function generarDespachoDefecto(int $venta_id, string $modo = 'VENTA')
    {
        $documento  =   Documento::findOrFail($venta_id);
        $almacen    =   Almacen::findOrFail($documento->almacen_id);

        //======== OBTENER EMPRESA ENVÍO =======
        $empresa_envio                      =   DB::select('SELECT
                                                    ee.id,
                                                    ee.empresa,
                                                    ee.tipo_envio
                                                    FROM empresas_envio AS ee
                                                    WHERE predeterminado = 1')[0];

        $sede_envio                         =   DB::select(
            'SELECT
                                                    ees.id,
                                                    ees.direccion,
                                                    ees.departamento,
                                                    ees.provincia,
                                                    ees.distrito
                                                    FROM empresa_envio_sedes AS ees
                                                    WHERE ees.empresa_envio_id=?',
            [$empresa_envio->id]
        )[0];

        $departamento       =   Departamento::where('nombre', $sede_envio->departamento)->first();
        $provincia          =   Provincia::where('nombre', $sede_envio->provincia)->first();
        $distrito           =   Distrito::where('nombre', $sede_envio->distrito)->first();
        $tipo_envio         =   Detalle::where('descripcion', $empresa_envio->tipo_envio)->first();
        $tipo_pago_envio    =   Detalle::where('descripcion', 'ENVÍO GRATIS')->first();
        $origen_venta       =   Detalle::where('descripcion', 'WHATSAPP')->first();

        $envio_venta                        =   new EnvioVenta();
        $envio_venta->documento_id          =   $documento->id;

        $envio_venta->departamento_id       =   $departamento->id;
        $envio_venta->departamento          =   $sede_envio->departamento;

        $envio_venta->provincia             =   $sede_envio->provincia;
        $envio_venta->provincia_id          =   $provincia->id;

        $envio_venta->distrito              =   $sede_envio->distrito;
        $envio_venta->distrito_id           =   $distrito->id;

        $envio_venta->empresa_envio_id      =   $empresa_envio->id;
        $envio_venta->empresa_envio_nombre  =   $empresa_envio->empresa;

        $envio_venta->sede_envio_id         =   $sede_envio->id;
        $envio_venta->sede_envio_nombre     =   $sede_envio->direccion;

        $envio_venta->tipo_envio_id         =   $tipo_envio->id;
        $envio_venta->tipo_envio            =   $empresa_envio->tipo_envio;

        $envio_venta->tipo_pago_envio_id    =   $tipo_pago_envio->id;
        $envio_venta->tipo_pago_envio       =   $tipo_pago_envio->descripcion;

        $envio_venta->origen_venta          =   "WHATSAPP";
        $envio_venta->origen_venta_id       =   $origen_venta->id;

        $envio_venta->destinatario_tipo_doc =   $documento->tipo_documento_cliente;
        $envio_venta->destinatario_nro_doc  =   $documento->documento_cliente;
        $envio_venta->destinatario_nombre   =   $documento->cliente;
        $envio_venta->cliente_id            =   $documento->cliente_id;
        $envio_venta->cliente_nombre        =   $documento->cliente;
        $envio_venta->monto_envio           =   $documento->monto_envio;
        $envio_venta->entrega_domicilio     =   "NO";
        $envio_venta->direccion_entrega     =   null;
        $envio_venta->documento_nro         =   $documento->serie . '-' . $documento->correlativo;
        $envio_venta->fecha_envio_propuesta =   null;
        $envio_venta->obs_despacho          =   null;
        $envio_venta->obs_rotulo            =   null;
        $envio_venta->estado                =   'PENDIENTE';
        $envio_venta->cliente_celular       =   $documento->clienteEntidad->telefono_movil;
        $envio_venta->user_vendedor_id      =   $documento->user_id;
        $envio_venta->user_vendedor_nombre  =   $documento->user->usuario;
        $envio_venta->user_despachador_id       =   $documento->user_id;
        $envio_venta->user_despachador_nombre   =   $documento->user->usuario;
        $envio_venta->almacen_id            =   $documento->almacen_id;
        $envio_venta->almacen_nombre        =   $documento->almacen_nombre;
        $envio_venta->sede_id               =   $documento->sede_id;
        $envio_venta->sede_despachadora_id  =   $almacen->sede_id;
        $envio_venta->modo                  =   $modo;
        $envio_venta->save();
    }

    public function store(array $datos)
    {
        $departamento           =   Departamento::findOrFail($datos['departamento']);
        $provincia              =   Provincia::findOrFail($datos['provincia']);
        $distrito               =   Distrito::findOrFail($datos['distrito']);
        $empresa_envio          =   MetodoEntrega::findOrFail($datos['empresa_envio']);
        $sede_envio             =   EmpresaEnvioSede::findOrFail($datos['sede_envio']);
        $tipo_envio             =   Detalle::findOrFail($datos['tipo_envio']);
        $tipo_pago_envio        =   Detalle::findOrFail($datos['tipo_pago_envio']);
        $origen_venta           =   Detalle::findOrFail($datos['origen_venta']);
        $venta                  =   Documento::findOrFail($datos['documento_id']);
        $almacen                =   Almacen::findOrFail($venta->almacen_id);

        $envio                          =   new EnvioVenta();
        $envio->documento_id            =   $datos['documento_id'];
        $envio->departamento            =   $departamento->nombre;
        $envio->provincia               =   $provincia->nombre;
        $envio->distrito                =   $distrito->nombre;
        $envio->departamento_id         =   $departamento->id;
        $envio->provincia_id            =   $provincia->id;
        $envio->distrito_id             =   $distrito->id;
        $envio->empresa_envio_id        =   $empresa_envio->id;
        $envio->empresa_envio_nombre    =   $empresa_envio->empresa;
        $envio->sede_envio_id           =   $sede_envio->id;
        $envio->sede_envio_nombre       =   $sede_envio->direccion;
        $envio->tipo_envio_id           =   $tipo_envio->id;
        $envio->tipo_envio              =   $tipo_envio->descripcion;

        $envio->destinatario_tipo_doc   =   $datos['destinatario']['tipo_documento'];
        $envio->destinatario_nro_doc    =   $datos['destinatario']['nro_documento'];
        $envio->destinatario_nombre     =   $datos['destinatario']['nombres'];

        $envio->cliente_id              =   $venta->cliente_id;
        $envio->cliente_nombre          =   $venta->cliente;
        $envio->cliente_celular         =   $venta->clienteEntidad->telefono_movil;

        $envio->tipo_pago_envio         =   $tipo_pago_envio->descripcion;
        $envio->tipo_pago_envio_id      =   $tipo_pago_envio->id;

        $envio->monto_envio             =   $venta->monto_envio;
        $envio->entrega_domicilio       =   $datos['entrega_domicilio'] ? 'SI' : 'NO';
        $envio->direccion_entrega       =   $datos['direccion_entrega'];
        $envio->documento_nro           =   $venta->serie . '-' . $venta->correlativo;
        $envio->fecha_envio_propuesta   =   $datos['fecha_envio_propuesta'];

        $envio->origen_venta            =   $origen_venta->descripcion;
        $envio->origen_venta_id         =   $origen_venta->id;

        $envio->obs_rotulo   = mb_strtoupper($datos['obs_rotulo'], 'UTF-8');
        $envio->obs_despacho = mb_strtoupper($datos['obs_despacho'], 'UTF-8');

        $envio->usuario_nombre          =   Auth::user()->usuario;
        $envio->user_vendedor_id        =   $venta->user_id;
        $envio->user_vendedor_nombre    =   $venta->registrador_nombre;
        $envio->user_despachador_id     =   null;
        $envio->user_despachador_nombre =   null;

        $envio->almacen_id            =   $venta->almacen_id;
        $envio->almacen_nombre        =   $venta->almacen_nombre;
        $envio->sede_id               =   $venta->sede_id;
        $envio->sede_despachadora_id  =   $almacen->sede_id;
        $envio->modo                  =   'VENTA';
        $envio->save();

        $venta->despacho_id     =   $envio->id;
        $venta->estado_despacho       = 'PENDIENTE';
        $venta->update();
    }

    public function update(array $datos)
    {
        $documento_id                           =   $datos['documento_id'];

        //====== ACTUALIZAR DESPACHO ========
        $departamentoId = str_pad($datos['departamento'], 2, '0', STR_PAD_LEFT);
        $provinciaId    = str_pad($datos['provincia'], 4, '0', STR_PAD_LEFT);
        $distritoId     = str_pad($datos['distrito'], 6, '0', STR_PAD_LEFT);

        $departamento = Departamento::findOrFail($departamentoId);
        $provincia    = Provincia::findOrFail($provinciaId);
        $distrito     = Distrito::findOrFail($distritoId);
        $empresa_envio          =   MetodoEntrega::findOrFail($datos['empresa_envio']);
        $sede_envio             =   EmpresaEnvioSede::findOrFail($datos['sede_envio']);
        $tipo_envio             =   Detalle::findOrFail($datos['tipo_envio']);
        $tipo_pago_envio        =   Detalle::findOrFail($datos['tipo_pago_envio']);
        $origen_venta           =   Detalle::findOrFail($datos['origen_venta']);
        $venta                  =   Documento::findOrFail($datos['documento_id']);

        $envio                          =   EnvioVenta::where('documento_id', $documento_id)->first();
        $envio->documento_id            =   $datos['documento_id'];
        $envio->departamento            =   $departamento->nombre;
        $envio->provincia               =   $provincia->nombre;
        $envio->distrito                =   $distrito->nombre;
        $envio->departamento_id         =   $departamento->id;
        $envio->provincia_id            =   $provincia->id;
        $envio->distrito_id             =   $distrito->id;
        $envio->empresa_envio_id        =   $empresa_envio->id;
        $envio->empresa_envio_nombre    =   $empresa_envio->empresa;
        $envio->sede_envio_id           =   $sede_envio->id;
        $envio->sede_envio_nombre       =   $sede_envio->direccion;
        $envio->tipo_envio_id           =   $tipo_envio->id;
        $envio->tipo_envio              =   $tipo_envio->descripcion;

        $envio->destinatario_tipo_doc   =   $datos['destinatario']['tipo_documento'];
        $envio->destinatario_nro_doc    =   $datos['destinatario']['nro_documento'];
        $envio->destinatario_nombre     =   $datos['destinatario']['nombres'];

        $envio->cliente_id              =   $venta->cliente_id;
        $envio->cliente_nombre          =   $venta->cliente;
        $envio->cliente_celular         =   $venta->clienteEntidad->telefono_movil;

        $envio->tipo_pago_envio         =   $tipo_pago_envio->descripcion;
        $envio->tipo_pago_envio_id      =   $tipo_pago_envio->id;

        $envio->monto_envio             =   $venta->monto_envio;
        $envio->entrega_domicilio       =   $datos['entrega_domicilio'] ? 'SI' : 'NO';
        $envio->direccion_entrega       =   $datos['direccion_entrega'];
        $envio->documento_nro           =   $venta->serie . '-' . $venta->correlativo;
        $envio->fecha_envio_propuesta   =   $datos['fecha_envio_propuesta'];

        $envio->origen_venta            =   $origen_venta->descripcion;
        $envio->origen_venta_id         =   $origen_venta->id;

        $envio->obs_rotulo              =   mb_strtoupper($datos['obs_rotulo'], 'UTF-8');
        $envio->obs_despacho            =   mb_strtoupper($datos['obs_despacho'], 'UTF-8');

        $envio->usuario_nombre          =   Auth::user()->usuario;
        $envio->update();
    }
}
