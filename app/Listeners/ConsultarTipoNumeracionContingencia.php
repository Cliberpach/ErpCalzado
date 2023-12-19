<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class ConsultarTipoNumeracionContingencia
{
    public function handle($event)
    {
        $enviar = [
            'existe' => true,
            'correlativo' => self::obtenerCorrelativo($event->documento)
        ];
        $collection = collect($enviar);
        return  $collection;
    }

    public function obtenerCorrelativo($documento)
    {
        if (empty($documento->correlativo_contingencia)) {
            $serie_comprobantes = DB::table('cotizacion_documento')
            ->join('empresas', 'empresas.id', '=', 'cotizacion_documento.empresa_id')
                ->where('cotizacion_documento.tipo_venta', $documento->tipo_venta)
                ->where('cotizacion_documento.estado', '!=', 'ANULADO')
                ->where('cotizacion_documento.contingencia', '1')
                ->select('cotizacion_documento.*')
                ->orderBy('cotizacion_documento.correlativo_contingencia', 'DESC')
                ->get();


            if (count($serie_comprobantes) == 1) {
                //OBTENER EL DOCUMENTO INICIADO
                $documento->correlativo_contingencia = 1;
                $documento->serie_contingencia = $documento->tipoDocumento() == '01' ? '0001' : '0002';
                $documento->update();

                //ACTUALIZAR LA NUMERACION (SE REALIZO EL INICIO)
                return $documento->correlativo_contingencia;
            } else {
                //DOCUMENTO DE VENTA ES NUEVO EN SUNAT
                if ($documento->sunat_contingencia != '1' || $documento->tipo_venta == 129) {
                    $ultimo_comprobante = $serie_comprobantes->first();
                    $documento->correlativo_contingencia = $ultimo_comprobante->correlativo_contingencia + 1;
                    $documento->serie_contingencia = $documento->tipoDocumento() == '01' ? '0001' : '0002';;
                    $documento->update();
                    return $documento->correlativo_contingencia;
                }
            }
        } else {
            return $documento->correlativo_contingencia;
        }
    }
}
