<?php

namespace App\Http\Services\Cuentas\Cliente;

use App\Http\Services\Kardex\Cuenta\KardexCuentaService;
use App\Http\Services\Ventas\Ventas\VentaService;
use App\Mantenimiento\Cuenta\Cuenta;
use App\Ventas\CuentaCliente;
use App\Ventas\Documento\Documento;
use App\Ventas\Pedido;
use Exception;

class CuentaService
{
    private CuentaRepository $s_crepository;
    private CuentaValidacion $s_cvalidacion;
    private KardexCuentaService $s_kardex_cuenta;
    private VentaService $s_venta;

    public function __construct()
    {
        $this->s_crepository    =   new CuentaRepository();
        $this->s_cvalidacion    =   new CuentaValidacion();
        $this->s_kardex_cuenta  =   new KardexCuentaService();
        $this->s_venta          =   new VentaService();
    }

    public function pagar(array $datos, int $cuenta_id):CuentaCliente
    {
        $cuenta_cliente             =   CuentaCliente::findOrFail($cuenta_id);
        $cuenta_banco               =   Cuenta::find($datos['cuenta']);
        $datos['cuenta_cliente']    =   $cuenta_cliente;
        $datos['cuenta_banco']      =   $cuenta_banco;
        $this->s_cvalidacion->validacionPago($datos);
        $pago                       =   $this->s_crepository->registrarPago($datos);

        //======== GUARDAR IMAGEN ========
        if (!empty($datos['imagen']) && $datos['imagen']->isValid()) {
            $pago->ruta_imagen = $datos['imagen']->store('public/cuenta/cobrar');
            $pago->update();
        }

        $this->actualizarEstadoPagoPedido($cuenta_cliente);

        $this->s_kardex_cuenta->registrarDesdeCuentaCliente($pago);

        return $cuenta_cliente;
    }

    public function actualizarEstadoPagoPedido(CuentaCliente $cuenta_cliente){
        $venta  =   Documento::findOrFail($cuenta_cliente->cotizacion_documento_id);

        if($cuenta_cliente->estado == 'PAGADO' && $venta->pedido_id){
            $pedido                                 =   Pedido::where('doc_venta_credito_id',$venta->id)->first();

            if($pedido->estado !== 'PENDIENTE'){
                throw new Exception("NO SE PUEDE PAGAR UNA RESERVA CON ESTADO: ".$pedido->estado);
            }

            $pedido->doc_venta_credito_estado_pago  =   'PAGADO';
            $pedido->update();
        }
    }

    public function decrementarSaldo(int $cuenta_id,float $monto){

    }

    public function eliminarPorVentaId(int $id){
        $this->s_crepository->eliminarPorVentaId($id);
    }

}

