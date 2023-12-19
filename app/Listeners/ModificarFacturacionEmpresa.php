<?php

namespace App\Listeners;

use App\Mantenimiento\Empresa\Numeracion;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ModificarFacturacionEmpresa
{
    public function handle($event)
    {
        //MODIFICACION DE NUMERACION DE FACTURACION DE EMPRESA
        $numeracionJSON = $event->numeracion_empresa;
        $numeraciontabla = json_decode($numeracionJSON[0]);

        if ($numeraciontabla) {

            $numeraciones = Numeracion::where('empresa_id', $event->empresa->id)->where('estado','ACTIVO')->get();
            foreach ($numeraciones as $numeracion) {
                $numeracion->estado= "ANULADO";
                $numeracion->update();
            }

            foreach ($numeraciontabla as $numeracion) {
                Numeracion::create([
                    'empresa_id' => $event->empresa->id,
                    'serie' => $numeracion->serie,
                    'tipo_comprobante' => $numeracion->tipo_id,
                    'numero_iniciar' => $numeracion->numero_iniciar,
                    'emision_iniciada' => $numeracion->emision,
                ]);
            }


        }else{
            $numeraciones = Numeracion::where('empresa_id', $event->empresa->id)->get();
            foreach ($numeraciones as $numeracion) {
                $numeracion->estado= "ANULADO";
                $numeracion->update();
            }
        }
    }
}
