<?php

namespace App\Http\Services\Ventas\Ventas;

use App\Mantenimiento\Empresa\Empresa;
use Illuminate\Support\Facades\DB;

class CorrelativoService
{
    /*
{#1844 // app\Http\Controllers\Ventas\RegistroVentaController.php:174
  +"correlativo": 1
  +"serie": "B001"
}
*/
    public static function getCorrelativo($tipo_comprobante, $sede_id)
    {

        $correlativo        =   null;
        $serie              =   null;

        //======= CONTABILIZANDO SI HAY DOCUMENTOS DE VENTA EMITIDOS PARA EL TYPE SALE ======
        $ultima_venta =   DB::select(
            'SELECT cd.correlativo
                        FROM cotizacion_documento AS cd
                        WHERE cd.sede_id = ?
                        AND cd.tipo_venta_id = ?
                        ORDER BY cd.id DESC
                        LIMIT 1',
            [
                $sede_id,
                $tipo_comprobante->id
            ]
        );


        $serializacion     =   DB::select(
                                    'SELECT
                                                    enf.*
                                                    from empresa_numeracion_facturaciones as enf
                                                    where
                                                    enf.empresa_id = ?
                                                    and enf.tipo_comprobante = ?
                                                    and enf.sede_id = ?',
                                    [
                                        Empresa::find(1)->id,
                                        $tipo_comprobante->id,
                                        $sede_id
                                    ]
                                )[0];

        //==== NO EXISTE UNA ÚLTIMA VENTA DE ESE TIPO DE COMPROBANTE =====
        if (count($ultima_venta) === 0) {

            //====== INICIAR DESDE EL STARTING NUMBER =======
            $correlativo        =   $serializacion->numero_iniciar;
            $serie              =   $serializacion->serie;
        } else {

            //======= EN CASO YA EXISTAN DOCUMENTOS DE VENTA DEL TYPE SALE ======
            $correlativo        =   $ultima_venta[0]->correlativo  +   1;
            $serie              =   $serializacion->serie;
        }

        return (object)['correlativo' => $correlativo, 'serie' => $serie];
    }
}
