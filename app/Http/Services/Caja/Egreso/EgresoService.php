<?php

namespace App\Http\Services\Caja\Egreso;

use App\Http\Services\Kardex\Cuenta\KardexCuentaService;
use App\Mantenimiento\Cuenta\Cuenta;
use App\Mantenimiento\TipoPago\TipoPago;
use App\Pos\Egreso;
use Exception;

class EgresoService
{
    private EgresoRepository $s_repository;
    private ValidacionesService $s_validaciones;
    private KardexCuentaService $s_kardex_cuenta;

    public function __construct()
    {
        $this->s_repository     =   new EgresoRepository();
        $this->s_validaciones   =   new ValidacionesService();
        $this->s_kardex_cuenta  =   new KardexCuentaService();
    }

    public function store(array $data)
    {
        $movimiento =   $this->s_validaciones->validacionStore($data);

        $data       =   $this->prepararDatosStore($data);

        $egreso     =   $this->s_repository->registrarEgreso($data);
        $this->s_repository->registrarDetalleMovEgreso($egreso->id, $movimiento[0]->movimiento_id);
        $this->s_kardex_cuenta->registrarDesdeEgreso($egreso);
    }

    public function prepararDatosStore(array $data)
    {
        $metodo_pago_id =   $data['modo_pago'];

        if ($metodo_pago_id != 1) {
            $cuenta_bancaria_id             =   $data['cuenta_bancaria'];
            $cuenta_bancaria                =   Cuenta::findOrFail($cuenta_bancaria_id);
            $data['cuenta_bancaria_id']     =   $cuenta_bancaria->id;
            $data['banco_nombre']           =   $cuenta_bancaria->banco_nombre;
            $data['banco_nro_cuenta']       =   $cuenta_bancaria->nro_cuenta;
            $data['banco_cci']              =   $cuenta_bancaria->cci;
            $data['cuenta_celular']         =   $cuenta_bancaria->celular;
            $data['cuenta_titular']         =   $cuenta_bancaria->titular;
            $data['cuenta_moneda']          =   $cuenta_bancaria->moneda;
        } else {
            $data['cuenta_bancaria_id']     =   null;
            $data['banco_nombre']           =   null;
            $data['banco_nro_cuenta']       =   null;
            $data['banco_cci']              =   null;
            $data['cuenta_celular']         =   null;
            $data['cuenta_titular']         =   null;
            $data['cuenta_moneda']          =   null;
        }

        $metodo_pago                    =   TipoPago::findOrFail($data['modo_pago']);
        $data['tipo_pago_nombre']       =   $metodo_pago->descripcion;

        return $data;
    }

    public function update(array $data, int $id)
    {
        $data   =   $this->prepararDatosStore($data);
        $egreso =   $this->s_repository->actualizarEgreso($data, $id);
        $this->s_kardex_cuenta->actualizarDesdeEgreso($egreso);
    }

    public function destroy(int $id)
    {
        $this->s_repository->eliminarEgreso($id);
        $this->s_kardex_cuenta->eliminarDesdeEgreso($id);
    }
}
