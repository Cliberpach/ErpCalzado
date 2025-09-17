<?php

namespace App\Http\Services\Caja\Egreso;

use App\Pos\DetalleMovimientoEgresosCaja;
use App\Pos\Egreso;

class EgresoRepository
{

    public function registrarEgreso(array $data): Egreso
    {

        $egreso = new Egreso([
            'tipodocumento_id' => 120,
            'cuenta_id'        => $data['cuenta'],
            'documento'        => $data['documento'],
            'descripcion'      => $data['descripcion'],
            'monto'            => $data['monto'],
            'importe'          => $data['importe'],
            'efectivo'         => $data['efectivo'],
            'tipo_pago_id'     => $data['modo_pago'],
            'usuario'          => auth()->user()->usuario,
            'user_id'          => auth()->id(),
            'sede_id'          => auth()->user()->sede_id,

            'cuenta_bancaria_id'    =>  $data['cuenta_bancaria_id'],
            'nro_operacion'         =>  $data['nro_operacion'],
            'fecha_operacion'       =>  $data['fecha_operacion'],
            'banco_nombre'          =>  $data['banco_nombre'],
            'banco_nro_cuenta'      =>  $data['banco_nro_cuenta'],
            'banco_cci'             =>  $data['banco_cci'],
            'cuenta_celular'        =>  $data['cuenta_celular'],
            'cuenta_titular'        =>  $data['cuenta_titular'],
            'cuenta_moneda'         =>  $data['cuenta_moneda'],
            'tipo_pago_nombre'      =>  $data['tipo_pago_nombre']
        ]);

        $egreso->save();

        return $egreso;
    }

    public function registrarDetalleMovEgreso(int $egreso_id, int $movimiento_id)
    {
        $detalleMovimientoEgreso                =   new DetalleMovimientoEgresosCaja();
        $detalleMovimientoEgreso->mcaja_id      =   $movimiento_id;
        $detalleMovimientoEgreso->egreso_id     =   $egreso_id;
        $detalleMovimientoEgreso->save();
    }

    public function actualizarEgreso(array $data, int $egreso_id): Egreso
    {
        $egreso =   Egreso::findOrFail($egreso_id);
        $egreso->update([
            'tipodocumento_id'    => 120,
            'cuenta_id'           => $data['cuenta'],
            'documento'           => $data['documento'],
            'descripcion'         => $data['descripcion'],
            'monto'               => $data['monto'],
            'importe'             => $data['importe'],
            'efectivo'            => $data['efectivo'] ?? 0,
            'tipo_pago_id'        => $data['modo_pago'],

            // 'usuario'             => auth()->user()->usuario,
            // 'user_id'             => auth()->id(),
            // 'sede_id'             => auth()->user()->sede_id,

            'cuenta_bancaria_id'  => $data['cuenta_bancaria_id'],
            'nro_operacion'       => $data['nro_operacion'],
            'fecha_operacion'     => $data['fecha_operacion'],
            'banco_nombre'        => $data['banco_nombre'],
            'banco_nro_cuenta'    => $data['banco_nro_cuenta'],
            'banco_cci'           => $data['banco_cci'],
            'cuenta_celular'      => $data['cuenta_celular'],
            'cuenta_titular'      => $data['cuenta_titular'],
            'cuenta_moneda'       => $data['cuenta_moneda'],
            'tipo_pago_nombre'    => $data['tipo_pago_nombre'],
        ]);

        return $egreso;
    }

    public function eliminarEgreso(int $id)
    {
        $egreso = Egreso::findOrFail($id);
        $egreso->estado = "ANULADO";
        $egreso->update();
    }
}
