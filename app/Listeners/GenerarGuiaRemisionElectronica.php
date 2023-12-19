<?php

namespace App\Listeners;

use App\Ventas\DetalleGuia;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\ventas\Documento\Detalle;

class GenerarGuiaRemisionElectronica
{

    public function handle($event)
    {
        //ARREGLO GUIA
        $arreglo_guia = array(

            "tipoDoc" => "09",
            "serie" => $event->serie,
            "correlativo"=> $event->guia->correlativo,
            "fechaEmision" => self::obtenerFecha($event->guia),

            "company" => array(
                "ruc" => $event->guia->ruc_empresa,
                "razonSocial" => $event->guia->empresa,
                "address" => array(
                    "direccion" => $event->guia->direccion_empresa,
                )),


            "destinatario" => array(
                "tipoDoc" =>  $event->guia->tipoDocumentoCliente(),
                "numDoc" => $event->guia->documento_cliente,
                "rznSocial" => $event->guia->cliente,
                "address" => array(
                    "direccion" => $event->guia->direccion_cliente,
                )
            ),

            "observacion" => $event->guia->observacion,

            "envio" => array(
                "modTraslado" =>  "01",
                "codTraslado" =>  "01",
                "desTraslado" =>  "VENTA",
                "fecTraslado" =>  self::obtenerFecha($event->guia),//FECHA DEL TRANSLADO
                "codPuerto" => "123",
                "indTransbordo"=> false,
                "pesoTotal" => $event->guia->peso_productos,
                "undPesoTotal"=> "KGM",
                "numBultos" => $event->guia->cantidad_productos,
                "llegada" => array(
                    "ubigueo" =>  $event->guia->ubigeo_llegada,
                    "direccion" => self::limitarDireccion($event->guia->direccion_llegada,50,"..."),
                ),
                "partida" => array(
                    "ubigueo" => $event->guia->ubigeo_partida,
                    "direccion" => self::limitarDireccion($event->guia->direccion_empresa,50,"..."),
                ),
                "transportista"=> self::condicionReparto($event->guia)
            ),

            "details" =>  self::obtenerProductos($event->guia),


        );
        return json_encode($arreglo_guia);
    }

    public function condicionReparto($guia)
    {
        $Transportista = array(
            "tipoDoc"=> "6",
            "numDoc"=> $guia->ruc_transporte_domicilio,
            "rznSocial"=> $guia->nombre_transporte_domicilio,
            "placa"=> $guia->placa_vehiculo,
            "choferTipoDoc"=> "1",
            "choferDoc"=> $guia->dni_conductor
        );

        return $Transportista;
    }


    public function obtenerProductos($guia)
    {
        $detalles = DetalleGuia::where('guia_id',$guia->id)->get();

        $arrayProductos = Array();
        for($i = 0; $i < count($detalles); $i++){

            $arrayProductos[] = array(
                "codigo" => $detalles[$i]->codigo_producto,
                "unidad" => $detalles[$i]->unidad,
                "descripcion"=> $detalles[$i]->nombre_producto,
                "cantidad" => $detalles[$i]->cantidad,
                "codProdSunat" => '10',
            );
        }

        return $arrayProductos;
    }


    public function obtenerFecha($guia)
    {
        $date = strtotime($guia->fecha_emision);
        $fecha_emision = date('Y-m-d', $date);
        $hora_emision = date('H:i:s', $date);
        $fecha = $fecha_emision.'T'.$hora_emision.'-05:00';

        return $fecha;
    }


    public function limitarDireccion($cadena, $limite, $sufijo){

        if(strlen($cadena) > $limite){
            return substr($cadena, 0, $limite) . $sufijo;
        }

        return $cadena;
    }
}
