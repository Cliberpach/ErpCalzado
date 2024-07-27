<?php

namespace App\Listeners;

use App\Mantenimiento\Empresa\Numeracion;
use App\Ventas\Documento\Documento;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class ConsultarTipoNumeracion
{
    public function handle($event)
    {
        
        $numeracion = Numeracion::where('empresa_id',$event->documento->empresa_id)->where('estado','ACTIVO')->where('tipo_comprobante',$event->documento->tipo_venta)->first();
       
        if ($numeracion) {

            $resultado = $numeracion->exists();
            
            $enviar = [
                'existe'                =>  ($resultado == true) ? true : false,
                'numeracion'            =>  $numeracion,
                'correlativo_datos'     =>  self::obtenerCorrelativo($event->documento,$numeracion)
                // 'correlativo'   =>  self::obtenerCorrelativo($event->documento,$numeracion)
            ];
            $collection = collect($enviar);
            return  $collection;
        }
    }

    public function obtenerCorrelativo($documento, $numeracion)
    {
        
       if(empty($documento->correlativo))
       {

      
            //==== REVIZANDO SI ESTÁ ACTIVO EL TIPO DE DOC VENTA =======
            $serie_comprobantes = DB::table('empresa_numeracion_facturaciones')
            ->join('empresas','empresas.id','=','empresa_numeracion_facturaciones.empresa_id')
            //->join('cotizacion_documento','cotizacion_documento.empresa_id','=','empresas.id')
            ->where('empresa_numeracion_facturaciones.tipo_comprobante',$documento->tipo_venta)
            ->where('empresa_numeracion_facturaciones.empresa_id',$documento->empresa_id)
            ->where('empresa_numeracion_facturaciones.estado',"ACTIVO")
            //->where('cotizacion_documento.tipo_venta',$documento->tipo_venta)
            //->where('cotizacion_documento.estado','!=','ANULADO')
            //->select('cotizacion_documento.*','empresa_numeracion_facturaciones.*')
            ->select('empresa_numeracion_facturaciones.*')
            //->orderBy('cotizacion_documento.correlativo','DESC')
            ->get();

            if(count($serie_comprobantes) === 1){
                //====== TENER EN CUENTA QUE EL ACTUAL DOC DE VENTA YA ESTÁ EN LA BD ========
                //===== TOMAMOS EL ÚLTIMO COMPROBANTE GENERADO DE ESE TIPO ======
                // $cantidad_docs_venta    =   DB::select('select count(cd.id) as cant_docs 
                //                             from cotizacion_documento as cd
                //                             where cd.tipo_venta = ?',[$documento->tipo_venta])[0];
                    
                $penultimo_comprobante = DB::table('cotizacion_documento as cd')
                                        ->where('cd.tipo_venta', $documento->tipo_venta)
                                        ->orderBy('cd.id', 'DESC')
                                        ->skip(1)
                                        ->first();
                
                //====== SIGNIFICA QUE ES EL PRIMER DOCUMENTO DE VENTA DE SU TIPO =========
                if(!$penultimo_comprobante){
                    $documento->correlativo = $serie_comprobantes[0]->numero_iniciar;
                    $documento->serie       = $serie_comprobantes[0]->serie;
                    $documento->update();
                    self::actualizarNumeracion($numeracion);
                    return ['correlativo' =>    $documento->correlativo,
                            'serie'       =>    $documento->serie];
        
                }else{
                    //======= SI YA EXISTEN VARIOS DOCS DE VENTA DE SU TIPO =======
                    $documento->correlativo =   $penultimo_comprobante->correlativo + 1;
                    $documento->serie       =   $serie_comprobantes[0]->serie;
                    $documento->update();
        
                    //ACTUALIZAR LA NUMERACION (SE REALIZO EL INICIO)
                    self::actualizarNumeracion($numeracion);
                    return ['correlativo' =>    $documento->correlativo,
                            'serie'       =>    $documento->serie];
                }

            }

            // if (count($serie_comprobantes) == 1) {
            //     //OBTENER EL DOCUMENTO INICIADO
            //     $documento->correlativo = $numeracion->numero_iniciar;
            //     $documento->serie = $numeracion->serie;
            //     $documento->update();

            //     //ACTUALIZAR LA NUMERACION (SE REALIZO EL INICIO)
            //     self::actualizarNumeracion($numeracion);
            //     return ['correlativo' =>    $documento->correlativo,
            //                 'serie'       =>    $documento->serie];

            // }else{
            //     //DOCUMENTO DE VENTA ES NUEVO EN SUNAT
            //     if($documento->sunat != '1' || $documento->tipo_venta == 129){
            //         $ultimo_comprobante = $serie_comprobantes->first();
            //         $documento->correlativo = $ultimo_comprobante->correlativo + 1;
            //         $documento->serie = $numeracion->serie;
            //         $documento->update();

            //         //ACTUALIZAR LA NUMERACION (SE REALIZO EL INICIO)
            //         self::actualizarNumeracion($numeracion);
            //         return ['correlativo' =>    $documento->correlativo,
            //                 'serie'       =>    $documento->serie];
            //     }
            // }
       }
       else
       {
           return $documento->correlativo;
       }
    }


    public function actualizarNumeracion($numeracion)
    {
        $numeracion->emision_iniciada = '1';
        $numeracion->update();
    }
}
