<?php

namespace App\Http\Services\Ventas\Cotizaciones;

use App\Almacenes\Color;
use App\Almacenes\Producto;
use App\Almacenes\Talla;
use App\Ventas\Cotizacion;
use App\Ventas\CotizacionDetalle;
use Carbon\Carbon;
use Exception;

class CotizacionRepository
{
    public function registrarCotizacion(array $datos): Cotizacion
    {
        $cliente        =   $datos['cliente'];
        $almacen        =   $datos['almacen'];
        $registrador    =   $datos['registrador'];
        $montos         =   $datos['montos'];

        $cotizacion                     =   new Cotizacion();
        $cotizacion->cliente_nombre     =   $cliente->nombre;
        $cotizacion->empresa_id         =   1;
        $cotizacion->cliente_id         =   $cliente->id;
        $cotizacion->condicion_id       =   $datos['condicion_id'];
        $cotizacion->registrador_id     =   $datos['registrador_id'];
        $cotizacion->registrador_nombre =   $registrador->usuario;
        $cotizacion->fecha_documento    =   Carbon::now()->format('Y-m-d');
        $cotizacion->fecha_atencion     =   Carbon::now()->format('Y-m-d');
        $cotizacion->sede_id            =   $datos['sede_id'];
        $cotizacion->almacen_id         =   $almacen->id;
        $cotizacion->almacen_nombre     =   $almacen->descripcion;

        $cotizacion->sub_total              =   $montos->monto_subtotal;
        $cotizacion->monto_embalaje         =   $montos->monto_embalaje;
        $cotizacion->monto_envio            =   $montos->monto_envio;
        $cotizacion->total_igv              =   $montos->monto_igv;
        $cotizacion->total                  =   $montos->monto_total;
        $cotizacion->total_pagar            =   $montos->monto_total_pagar;
        $cotizacion->monto_descuento        =   $montos->monto_descuento;
        $cotizacion->porcentaje_descuento   =   $montos->porcentaje_descuento;

        $cotizacion->moneda             =   4;
        $cotizacion->igv                =   $datos['porcentaje_igv'];
        $cotizacion->igv_check          =   "1";
        $cotizacion->telefono           =   $datos['telefono'];
        $cotizacion->save();

        return $cotizacion;
    }

    public function registrarDetalleCotizacion(array $lstItems, Cotizacion $cotizacion)
    {
        foreach ($lstItems as $item) {

            $existe_producto    =   Producto::findOrFail($item->producto_id);
            $existe_color       =   Color::findOrFail($item->color_id);
            $existe_talla       =   Talla::find($item->talla_id);

            if (!$existe_producto) {
                throw new Exception("EL PRODUCTO NO EXISTE EN LA BD!!!");
            }

            if (!$existe_color) {
                throw new Exception("EL COLOR NO EXISTE EN LA BD!!!");
            }

            if (!$existe_talla) {
                throw new Exception("LA TALLA NO EXISTE EN LA BD!!!");
            }

            //==== CALCULANDO MONTOS PARA EL DETALLE ====
            $importe        =   floatval($item->cantidad) * floatval($item->precio_venta);
            $precio_venta   =   $item->porcentaje_descuento == 0 ? $item->precio_venta : $item->precio_venta_nuevo;


            $detalle                            =   new CotizacionDetalle();
            $detalle->cotizacion_id             =   $cotizacion->id;
            $detalle->almacen_id                =   $cotizacion->almacen_id;
            $detalle->producto_id               =   $item->producto_id;
            $detalle->color_id                  =   $item->color_id;
            $detalle->talla_id                  =   $item->talla_id;
            $detalle->almacen_nombre            =   $cotizacion->almacen_nombre;
            $detalle->producto_nombre           =   $existe_producto->nombre;
            $detalle->color_nombre              =   $existe_color->descripcion;
            $detalle->talla_nombre              =   $existe_talla->descripcion;
            $detalle->cantidad                  =   $item->cantidad;
            $detalle->precio_unitario           =   $item->precio_venta;
            $detalle->importe                   =   $importe;
            $detalle->precio_unitario_nuevo     =   floatval($precio_venta);
            $detalle->porcentaje_descuento      =   floatval($item->porcentaje_descuento);
            $detalle->monto_descuento           =   floatval($importe) * floatval($item->porcentaje_descuento) / 100;
            $detalle->importe_nuevo             =   floatval($precio_venta) * floatval($item->cantidad);
            $detalle->save();
        }
    }

    public function actualizarCotizacion(int $id, array $datos): Cotizacion
    {
        $cliente        =   $datos['cliente'];
        $almacen        =   $datos['almacen'];
        $registrador    =   $datos['registrador'];
        $montos         =   $datos['montos'];

        $cotizacion                     =   Cotizacion::findOrFail($id);
        $cotizacion->cliente_nombre     =   $cliente->nombre;
        $cotizacion->empresa_id         =   1;
        $cotizacion->cliente_id         =   $cliente->id;
        $cotizacion->condicion_id       =   $datos['condicion_id'];
        $cotizacion->registrador_id     =   $registrador->id;
        $cotizacion->registrador_nombre =   $registrador->usuario;
        $cotizacion->fecha_documento    =   Carbon::now()->format('Y-m-d');
        $cotizacion->fecha_atencion     =   Carbon::now()->format('Y-m-d');
        $cotizacion->sede_id            =   $datos['sede_id'];
        $cotizacion->almacen_id         =   $almacen->id;
        $cotizacion->almacen_nombre     =   $almacen->descripcion;

        $cotizacion->sub_total              =   $montos->monto_subtotal;
        $cotizacion->monto_embalaje         =   $montos->monto_embalaje;
        $cotizacion->monto_envio            =   $montos->monto_envio;
        $cotizacion->total_igv              =   $montos->monto_igv;
        $cotizacion->total                  =   $montos->monto_total;
        $cotizacion->total_pagar            =   $montos->monto_total_pagar;
        $cotizacion->monto_descuento        =   $montos->monto_descuento;
        $cotizacion->porcentaje_descuento   =   $montos->porcentaje_descuento;

        $cotizacion->moneda             =   4;
        $cotizacion->igv                =   $datos['porcentaje_igv'];
        $cotizacion->igv_check          =   "1";
        $cotizacion->telefono           =   $datos['telefono'];
        $cotizacion->update();

        return $cotizacion;
    }

    public function eliminarDetalleCotizacion(int $id)
    {
        CotizacionDetalle::where('cotizacion_id', $id)->delete();
    }
}
