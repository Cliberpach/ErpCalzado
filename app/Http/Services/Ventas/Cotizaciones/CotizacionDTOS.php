<?php

namespace App\Http\Services\Ventas\Cotizaciones;

use App\Almacenes\Almacen;
use App\User;
use App\Ventas\Cliente;
use App\Ventas\Cotizacion;
use App\Ventas\CotizacionDetalle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CotizacionDTOS
{
    public function prepararDtoStore(array $datos): array
    {
        $almacen                =   Almacen::find($datos['almacen']);
        $registrador            =   User::find($datos['registrador_id']);

        //======== EMPRESA =======
        $dto['empresa_id']  =   1;

        //======== SEDE  =========
        $dto['sede_id']                 =   $datos['sede_id'];

        //======= ALMACÉN ==========
        $dto['almacen_id']         =   $almacen->id;
        $dto['almacen_nombre']     =   $almacen->descripcion;

        //=========  CLIENTE =========
        $cliente            =   Cliente::findOrFail($datos['cliente']);
        $dto['cliente_id']  =   $cliente->id;

        //========= REGISTRADOR ========
        $dto['registrador_id']     =   $datos['registrador_id'];
        $dto['registrador_nombre'] =   $registrador->usuario;

        //======== CONDICIÓN =======
        $dto['condicion_id']        =   $datos['condicion_id'];
        $dto['fecha_documento']     =   Carbon::now()->format('Y-m-d');
        $dto['fecha_atencion']      =   Carbon::now()->format('Y-m-d');

        //======== MONTOS =========
        $montos =   $datos['montos'];
        $dto['sub_total']            = $montos->monto_subtotal;
        $dto['monto_embalaje']       = $montos->monto_embalaje;
        $dto['monto_envio']          = $montos->monto_envio;
        $dto['total_igv']            = $montos->monto_igv;
        $dto['total']                = $montos->monto_total;
        $dto['total_pagar']          = $montos->monto_total_pagar;
        $dto['monto_descuento']      = $montos->monto_descuento;
        $dto['porcentaje_descuento'] = $montos->porcentaje_descuento;

        //======== MONEDA =======
        $dto['moneda']             =   4;
        $dto['igv']                =   $datos['porcentaje_igv'];
        $dto['igv_check']          =   "1";
        $dto['telefono']           =   $datos['telefono'];

        return $dto;
    }

    public function prepararDatosToPedido(array $datos): array
    {

        $cotizacion =   $datos['cotizacion'];

        $_datos['cliente_id']           =   $datos['cliente']->id;
        $_datos['cliente_nombre']       =   $datos['cliente']->nombre;
        $_datos['empresa_id']           =   $datos['empresa']->id;
        $_datos['razon_social']         =   $datos['empresa']->razon_social;
        $_datos['user_id']              =   Auth::user()->id;
        $_datos['user_nombre']          =   Auth::user()->nombre;
        $_datos['condicion_id']         =   $cotizacion->condicion_id;
        $_datos['moneda']               =   $cotizacion->moneda;

        $_datos['sub_total']            =   $cotizacion->sub_total;
        $_datos['total']                =   $cotizacion->total;
        $_datos['total_igv']            =   $cotizacion->total_igv;
        $_datos['total_pagar']          =   $cotizacion->total_pagar;
        $_datos['monto_embalaje']       =   $cotizacion->monto_embalaje;
        $_datos['monto_envio']          =   $cotizacion->monto_envio;
        $_datos['porcentaje_descuento'] =   $cotizacion->porcentaje_descuento;
        $_datos['monto_descuento']      =   $cotizacion->monto_descuento;
        $_datos['fecha_registro']       =   Carbon::now()->format('Y-m-d');
        $_datos['cotizacion_id']        =   $cotizacion->id;
        $_datos['sede_id']              =   $cotizacion->sede_id;
        $_datos['almacen_id']           =   $cotizacion->almacen_id;
        $_datos['registrador_id']       =   Auth::user()->id;
        $_datos['usuario_nombre']       =   Auth::user()->usuario;
        $_datos['fecha_propuesta']      =   $datos['fecha_propuesta'];
        $_datos['observacion']          =   $datos['observacion'];

        $detalle_cotizacion =   CotizacionDetalle::where('cotizacion_id', $cotizacion->id)->where('estado', 'ACTIVO')->get();

        $_datos['detalle_cotizacion']   =   $detalle_cotizacion;

        return $_datos;
    }
}
