<?php

namespace App\Listeners;

use App\Mantenimiento\Empresa\Numeracion;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RegistrarFacturacionEmpresa
{
    public function handle($event)
    {
        //INGRESO DE NUMERACION DE FACTURACION DE EMPRESA
        $numeracionJSON = $event->numeracion_empresa;
        $numeraciontabla = json_decode($numeracionJSON[0]);
        
        if ($numeraciontabla) {
            foreach ($numeraciontabla as $numeracion) {
                Numeracion::create([
                    'empresa_id' => $event->empresa->id,
                    'serie' => $numeracion->serie,
                    'tipo_comprobante' => $numeracion->tipo_id,
                    'numero_iniciar' => $numeracion->numero_iniciar,
                    'emision_iniciada' => $numeracion->emision,
                ]);
            }
        }
    }
}
