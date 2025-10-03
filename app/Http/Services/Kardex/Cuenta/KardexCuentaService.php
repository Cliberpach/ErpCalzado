<?php

namespace App\Http\Services\Kardex\Cuenta;

use App\Mantenimiento\TipoPago\TipoPago;
use App\Models\General\Herramientas\MetodoPago;
use App\Models\Grifo\Cuentas\CuentaClienteDetalle;
use App\Models\Grifo\Cuentas\CuentaProveedorDetalle;
use App\Models\Grifo\Ventas\Venta as VentasGrifo;
use App\Models\Kardex\Cuenta\KardexCuenta;
use App\Models\Ventas\Venta;
use App\Models\Market\Contabilidad\CuentaCobrar\CuentaClienteDetalle as CuentaClienteDetalleMarket;
use App\Pos\Egreso;
use App\Ventas\CuentaCliente;
use App\Ventas\DetalleCuentaCliente;
use App\Ventas\Documento\Documento;
use Exception;
use Illuminate\Support\Facades\Auth;

class KardexCuentaService
{
    /**
     * Create a new class instance.
     */
    public function __construct() {}


    //========== VENTA MARKET =========
    public function registrarDesdeVenta(Documento $venta)
    {
        $dto    =   $this->prepararDatosVenta($venta);

        KardexCuenta::create($dto);
    }

    public function actualizarDesdeVenta(Documento $venta)
    {
        $dto    =   $this->prepararDatosVenta($venta);

        $kardex_cuenta          =   KardexCuenta::where('venta_id', $venta->id)->first();
        if($kardex_cuenta){
            $kardex_cuenta->update($dto);
        }
    }

    public function registrarDesdeEgreso(Egreso $egreso)
    {
        $dto    =   $this->prepararDatosEgreso($egreso);
        KardexCuenta::create($dto);
    }

    public function actualizarDesdeEgreso(Egreso $egreso)
    {
        $dto    =   $this->prepararDatosEgreso($egreso);

        $kardex_cuenta  =   KardexCuenta::where('egreso_id', $egreso->id)->first();

        if($kardex_cuenta){
            $kardex_cuenta->update($dto);
        }else{
            KardexCuenta::create($dto);
        }
    }

    public function eliminarDesdeEgreso(int $egreso_id)
    {
        $kardex_cuenta          =   KardexCuenta::where('egreso_id', $egreso_id)->first();
        $kardex_cuenta->estado  =   'ANULADO';
        $kardex_cuenta->update();
    }

    public function registrarDesdeCuentaCliente(DetalleCuentaCliente $pago) {
        $dto    =   $this->prepararDatosCuentaCliente($pago);
        KardexCuenta::create($dto);
    }

    public function prepararDatosCuentaCliente(DetalleCuentaCliente $pago)
    {
        $cuenta_cliente =    CuentaCliente::findOrFail($pago->cuenta_cliente_id);
        $metodo_pago    =    TipoPago::findOrFail($pago->tipo_pago_id);

        $dto    =   [
            'cuenta_bancaria_id'    =>  $pago->cuenta_id,
            'venta_id'              =>  null,
            'pago_cliente_id'       =>  $pago->id,
            'pago_proveedor_id'     =>  null,
            'registrador_id'        =>  Auth::user()->id,
            'registrador_nombre'    =>  Auth::user()->usuario,
            'metodo_pago_id'        =>  $metodo_pago->id ,
            'metodo_pago_nombre'        =>  $metodo_pago->descripcion,
            'fecha_registro'            =>  $pago->fecha,
            'documento'                 =>  $cuenta_cliente->numero_doc,
            'banco_abreviatura'         =>  $pago->cuenta_banco_nombre,
            'nro_cuenta'                =>  $pago->cuenta_nro_cuenta,
            'monto'                     =>  $pago->monto,
            'tipo_documento'            =>  'COBRANZA',
            'tipo_operacion'            =>  'INGRESO'
        ];

        return $dto;
    }

    public function prepararDatosVenta(Documento $venta): array
    {
        $dto    =   [
            'cuenta_bancaria_id'    =>  $venta->pago_1_cuenta_id,
            'venta_id'              =>  $venta->id,
            'pago_cliente_id'       =>  null,
            'pago_proveedor_id'     =>  null,
            'registrador_id'        =>  $venta->user_id,
            'registrador_nombre'    =>  $venta->registrador_nombre,
            'metodo_pago_id'        =>  $venta->pago_1_tipo_pago_id,
            'metodo_pago_nombre'        =>  $venta->pago_1_tipo_pago_nombre,
            'fecha_registro'            =>  $venta->pago_1_fecha_operacion,
            'documento'                 =>  $venta->serie . '-' . $venta->correlativo,
            'banco_abreviatura'         =>  $venta->pago_1_banco_nombre,
            'nro_cuenta'                =>  $venta->pago_1_nro_cuenta,
            'monto'                     =>  $venta->pago_1_monto,
            'tipo_documento'            => 'VENTA',
            'tipo_operacion'            =>  'INGRESO'
        ];

        return $dto;
    }

    public function prepararDatosEgreso(Egreso $egreso): array
    {
        $dto    =   [
            'cuenta_bancaria_id'    =>  $egreso->cuenta_bancaria_id,
            'egreso_id'             =>  $egreso->id,
            'pago_cliente_id'       =>  null,
            'pago_proveedor_id'     =>  null,
            'registrador_id'        =>  $egreso->user_id,
            'registrador_nombre'    =>  $egreso->usuario,
            'metodo_pago_id'        =>  $egreso->tipo_pago_id,
            'metodo_pago_nombre'        =>  $egreso->tipo_pago_nombre,
            'fecha_registro'            =>  $egreso->fecha_operacion,
            'documento'                 =>  $egreso->documento,
            'banco_abreviatura'         =>  $egreso->banco_nombre,
            'nro_cuenta'                =>  $egreso->banco_nro_cuenta,
            'monto'                     =>  $egreso->monto,
            'tipo_documento'            => 'EGRESO',
            'tipo_operacion'            => 'EGRESO'
        ];

        return $dto;
    }
}
