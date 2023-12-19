<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Ventas\Documento\Detalle;
use App\Almacenes\LoteProducto;
use App\Almacenes\LoteDetalle;

class CargarLotesVenta
{
    public function handle($event)
    {

        $detalles = Detalle::where('documento_id', $event->documento->id)->get();

        foreach ($detalles as $detalle) {
            //OBTENER EL LOTE DEL PRODUCTO 
            $lote = LoteProducto::findOrFail($detalle->lote_id);
            
            //GUARDAR EN EL LOTE DETALLE 
            $loteDetalle =  new LoteDetalle();
            $loteDetalle->detalle_id = $detalle->id;
            $loteDetalle->lote_id = $lote->id;
            $loteDetalle->cantidad = $detalle->cantidad;
            $loteDetalle->save();
            //OBTENER EL RESTO DEL LOTE 
            $resto_lote = $lote->cantidad - $detalle->cantidad; 
            if ($resto_lote == 0) {
                $lote->estado = '0';
            }else{
                $lote->estado = '1';
            }
            $lote->cantidad = intval($resto_lote);
            $lote->update();

        }
        
    }
}
