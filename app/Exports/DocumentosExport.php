<?php

namespace App\Exports;

use App\Ventas\Documento\Documento;
use App\Ventas\Guia;
use App\Ventas\Nota;
use App\Ventas\Resumen;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class DocumentosExport implements FromCollection,WithHeadings,WithEvents
{

    use Exportable;
    public $tipo,$user,$fecha_desde,$fecha_hasta;

    public function headings(): array
    {
        return [
            ["RUC-EMISOR",
            "DOC.",
            "CODIGO.DOC",
            "FECHA",
            "TICKET",
            "TIENDA",
            "RUC/DNI",
            "TIPO.CLIENTE",
            "CLIENTE",
            "ESTADO",
            "MONEDA",
            "MONTO",
            "OP.GRAVADA",
            "IVG",
            "EFECTIVO",
            "TRANSFERENCIA",
            "YAPE/PLIN",
            "ENVIADA",
            "HASH"]
        ];
    }

    function title(): String
    {
        return "Documentos";
    }

    public function __construct($tipo,$fecha_desde,$fecha_hasta,$user)
    {
        $this->tipo = $tipo;
        $this->fecha_desde = $fecha_desde;
        $this->fecha_hasta = $fecha_hasta;
        $this->user = $user;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        if($this->tipo == 129 || $this->tipo == 128 || $this->tipo == 127)
        {
            $consulta = Documento::where('estado','!=','ANULADO')->where('tipo_venta', $this->tipo);
            if($this->fecha_desde && $this->fecha_hasta)
            {
                $consulta = $consulta->whereBetween('fecha_documento', [$this->fecha_desde, $this->fecha_hasta]);
            }

            if($this->user)
            {
                $consulta = $consulta->where('user_id',$this->user);
            }

            $consulta = $consulta->orderBy('fecha_documento', 'asc')
            ->orderBy('serie', 'asc')
            ->orderBy('correlativo', 'asc')
            ->get();

            $coleccion = collect();
            foreach($consulta as $doc){
                $transferencia = 0.00;
                $otros = 0.00;
                $efectivo = 0.00;

                if($doc->tipo_pago_id)
                {
                    if ($doc->tipo_pago_id == 1) {
                        $efectivo = $doc->importe;
                    }
                    else if ($doc->tipo_pago_id == 2){
                        $transferencia = $doc->importe ;
                        $efectivo = $doc->efectivo;
                    }
                    else {
                        $otros = $doc->importe;
                        $efectivo = $doc->efectivo;
                    }
                }
                $coleccion->push([
                    'RUC-EMISOR' => $doc->ruc_empresa,
                    'DOC.' => $doc->nombreDocumento(),
                    'CODIGO.DOC' => $doc->tipoDocumento(),
                    'FECHA' => $doc->fecha_documento,
                    'TICKET' => $doc->serie.'-'.$doc->correlativo . ' ' . ($doc->contingencia == '0' ? '' : '(CONTINGENCIA '.$doc->serie_contingencia.'-'.$doc->correlativo.')'),
                    'TIENDA' => $doc->empresa,
                    'RUC/DNI' => $doc->documento_cliente,
                    'TIPO.CLIENTE' => $doc->tipoDocumentoCliente(),
                    'CLIENTE' => $doc->cliente,
                    'ESTADO' => $doc->sunat == '2' ? "NULO" : "VALIDO",
                    'MONEDA' => $doc->simboloMoneda(),
                    'MONTO' => $doc->total_pagar,
                    'OP.GRAVADA' => $doc->total,
                    'IVG' => $doc->total_igv,
                    'EFECTIVO' => $efectivo,
                    'TRANSFERENCIA' => $transferencia,
                    'YAPE/PLIN' => $otros,
                    'ENVIADA' => $doc->contingencia == '0' ? ($doc->sunat == '1' || $doc->sunat == '2' ? 'SI' : 'NO') : ($doc->sunat_contingencia == '1' ? 'SI' : 'NO'),
                    'HASH' => $doc->hash
                ]);
            }

            return $coleccion->sortBy('FECHA');
        }

        if($this->tipo == 126) //Ventas
        {
            $ventas = Documento::where('estado','!=','ANULADO');
            if($this->fecha_desde && $this->fecha_hasta)
            {
                $ventas = $ventas->whereBetween('fecha_documento', [$this->fecha_desde, $this->fecha_hasta]);
            }

            if($this->user)
            {
                $ventas = $ventas->where('user_id',$this->user);
            }

            $ventas = $ventas->orderBy('fecha_documento', 'asc')
            ->orderBy('serie', 'asc')
            ->orderBy('correlativo', 'asc')
            ->get();

            $coleccion = collect();
            foreach($ventas as $doc){
                $transferencia = 0.00;
                $otros = 0.00;
                $efectivo = 0.00;

                if($doc->tipo_pago_id)
                {
                    if ($doc->tipo_pago_id == 1) {
                        $efectivo = $doc->importe;
                    }
                    else if ($doc->tipo_pago_id == 2){
                        $transferencia = $doc->importe ;
                        $efectivo = $doc->efectivo;
                    }
                    else {
                        $otros = $doc->importe;
                        $efectivo = $doc->efectivo;
                    }
                }
                $coleccion->push([
                    'RUC-EMISOR' => $doc->ruc_empresa,
                    'DOC.' => $doc->nombreDocumento(),
                    'CODIGO.DOC' => $doc->tipoDocumento(),
                    'FECHA' => $doc->fecha_documento,
                    'TICKET' => $doc->serie . '-' . $doc->correlativo . ' ' . ($doc->contingencia == '0' ? '' : '(CONTINGENCIA ' . $doc->serie_contingencia . '-' . $doc->correlativo . ')'),
                    'TIENDA' => $doc->empresa,
                    'RUC/DNI' => $doc->documento_cliente,
                    'TIPO.CLIENTE' => $doc->tipoDocumentoCliente(),
                    'CLIENTE' => $doc->cliente,
                    'ESTADO' => $doc->sunat == '2' ? "NULO" : "VALIDO",
                    'MONEDA' => $doc->simboloMoneda(),
                    'MONTO' => $doc->total_pagar,
                    'OP.GRAVADA' => $doc->total,
                    'IVG' => $doc->total_igv,
                    'EFECTIVO' => $efectivo,
                    'TRANSFERENCIA' => $transferencia,
                    'YAPE/PLIN' => $otros,
                    'ENVIADA' => $doc->contingencia == '0' ? ($doc->sunat == '1'|| $doc->sunat == '2' ? 'SI' : 'NO') : ($doc->sunat_contingencia == '1' ? 'SI' : 'NO'),
                    'HASH' => $doc->hash
                ]);
            }
            return $coleccion;
        }

        if($this->tipo == 125) //Fact, Boletas y Nota Crédito
        {
            $ventas = Documento::where('estado','!=','ANULADO')->where('tipo_venta','!=',129);
            if($this->fecha_desde && $this->fecha_hasta)
            {
                $ventas = $ventas->whereBetween('fecha_documento', [$this->fecha_desde, $this->fecha_hasta]);
            }

            if($this->user)
            {
                $ventas = $ventas->where('user_id',$this->user);
            }

            $ventas = $ventas->orderBy('fecha_documento', 'asc')
            ->orderBy('serie', 'asc')
            ->orderBy('correlativo', 'asc')
            ->get();

            $coleccion = collect();

            foreach($ventas as $doc){
                $transferencia = 0.00;
                $otros = 0.00;
                $efectivo = 0.00;

                if($doc->tipo_pago_id)
                {
                    if ($doc->tipo_pago_id == 1) {
                        $efectivo = $doc->importe;
                    }
                    else if ($doc->tipo_pago_id == 2){
                        $transferencia = $doc->importe ;
                        $efectivo = $doc->efectivo;
                    }
                    else {
                        $otros = $doc->importe;
                        $efectivo = $doc->efectivo;
                    }
                }
                $coleccion->push([
                    'RUC-EMISOR' => $doc->ruc_empresa,
                    'DOC.' => $doc->nombreDocumento(),
                    'CODIGO.DOC' => $doc->tipoDocumento(),
                    'FECHA' => Carbon::parse($doc->fecha_documento)->format( 'Y-m-d'),
                    'TICKET' => $doc->serie . '-' . $doc->correlativo . ' ' . ($doc->contingencia == '0' ? '' : '(CONTINGENCIA ' . $doc->serie_contingencia . '-' . $doc->correlativo . ')'),
                    'TIENDA' => $doc->empresa,
                    'RUC/DNI' => $doc->documento_cliente,
                    'TIPO.CLIENTE' => $doc->tipoDocumentoCliente(),
                    'CLIENTE' => $doc->cliente,
                    'ESTADO' => $doc->sunat == '2' ? "NULO" : "VALIDO",
                    'MONEDA' => $doc->simboloMoneda(),
                    'MONTO' => $doc->total_pagar,
                    'OP.GRAVADA' => $doc->total,
                    'IVG' => $doc->total_igv,
                    'EFECTIVO' => $efectivo,
                    'TRANSFERENCIA' => $transferencia,
                    'YAPE/PLIN' => $otros,
                    'ENVIADA' => $doc->contingencia == '0' ? ($doc->sunat == '1'|| $doc->sunat == '2' ? 'SI' : 'NO') : ($doc->sunat_contingencia == '1' ? 'SI' : 'NO'),
                    'HASH' => $doc->hash
                ]);
            }

            $notas_electronicas = Nota::where('estado','!=','ANULADO')->where('tipo_nota',"0")->where('tipDocAfectado','!=','04');
            if($this->fecha_desde && $this->fecha_hasta)
            {
                $notas_electronicas = $notas_electronicas->whereBetween('fechaEmision', [$this->fecha_desde, $this->fecha_hasta]);
            }

            $notas_electronicas = $notas_electronicas->orderBy('id', 'asc')->get();

            foreach($notas_electronicas as $nota){
                $coleccion->push([
                    'RUC-EMISOR' => $nota->ruc_empresa,
                    'DOC.' => 'NOTA DE CRÉDITO',
                    'CODIGO.DOC' => $nota->tipoDoc,
                    'FECHA' => Carbon::parse($nota->fechaEmision)->format( 'Y-m-d'),
                    'TICKET' => $nota->serie.' - '.$nota->correlativo,
                    'TIENDA' => $nota->empresa,
                    'RUC/DNI' => $nota->documento_cliente,
                    'TIPO.CLIENTE' => $nota->cod_tipo_documento_cliente,
                    'CLIENTE' => $nota->cliente,
                    'ESTADO' => $nota->sunat == '2' ? "NULO" : "VALIDO",
                    'MONEDA' => $nota->tipoMoneda,
                    'MONTO' => -($nota->mtoImpVenta),
                    'OP.GRAVADA' => -($nota->mtoOperGravadas),
                    'IVG' => -($nota->mtoIGV),
                    'EFECTIVO' => '-',
                    'TRANSFERENCIA' => '-',
                    'YAPE/PLIN' => '-',
                    'ENVIADA' => $nota->sunat == '1' || $nota->sunat == '2' ? 'SI' : 'NO',
                    'HASH' => $nota->hash
                ]);
            }

            return $coleccion;
        }

        if($this->tipo == 130)
        {
            $notas_electronicas = Nota::where('estado','!=','ANULADO')->where('tipo_nota',"0")->where('tipDocAfectado','!=','04');
            if($this->fecha_desde && $this->fecha_hasta)
            {
                $notas_electronicas = $notas_electronicas->whereBetween('fechaEmision', [$this->fecha_desde, $this->fecha_hasta]);
            }

            $notas_electronicas = $notas_electronicas->orderBy('fechaEmision', 'asc')
            ->orderBy('serie', 'asc')
            ->orderBy('correlativo', 'asc')
            ->get();

            $coleccion = collect();
            foreach($notas_electronicas as $nota){
                $coleccion->push([
                    'RUC-EMISOR' => $nota->ruc_empresa,
                    'DOC.' => 'NOTA DE CRÉDITO',
                    'CODIGO.DOC' => $nota->tipoDoc,
                    'FECHA' => Carbon::parse($nota->fechaEmision)->format( 'Y-m-d'),
                    'TICKET' => $nota->serie.' - '.$nota->correlativo,
                    'TIENDA' => $nota->empresa,
                    'RUC/DNI' => $nota->documento_cliente,
                    'TIPO.CLIENTE' => $nota->cod_tipo_documento_cliente,
                    'CLIENTE' => $nota->cliente,
                    'ESTADO' => $nota->sunat == '2' ? "NULO" : "VALIDO",
                    'MONEDA' => $nota->tipoMoneda,
                    'MONTO' => $nota->mtoImpVenta,
                    'OP.GRAVADA' => $nota->mtoOperGravadas,
                    'IVG' => $nota->mtoIGV,
                    'EFECTIVO' => '-',
                    'TRANSFERENCIA' => '-',
                    'YAPE/PLIN' => '-',
                    'ENVIADA' => $nota->sunat == '1' || $nota->sunat == '2' ? 'SI' : 'NO',
                    'HASH' => $nota->hash
                ]);
            }

            return $coleccion->sortBy('FECHA');
        }
    }

    public function registerEvents(): array
    {
        return [

            BeforeWriting::class => [self::class, 'beforeWriting'],
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getStyle('A1:S1')->applyFromArray([

                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                            'rotation' => 90,
                            'startColor' => [
                                'argb' => '00bbd4',
                            ],
                            'endColor' => [
                                'argb' => '00bbd4',
                            ],
                        ],


                    ]

                );
                $event->sheet->getStyle('A1:S1')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                        'rotation' => 90,
                        'startColor' => [
                            'argb' => '1ab394',
                        ],
                        'endColor' => [
                            'argb' => '1ab394',
                        ],
                    ],


                ]

                );



               $event->sheet->getColumnDimension('A')->setWidth(20);
               $event->sheet->getColumnDimension('B')->setWidth(20);
               $event->sheet->getColumnDimension('C')->setWidth(20);
               $event->sheet->getColumnDimension('D')->setWidth(20);
               $event->sheet->getColumnDimension('E')->setWidth(20);
               $event->sheet->getColumnDimension('F')->setWidth(20);
               $event->sheet->getColumnDimension('G')->setWidth(20);
               $event->sheet->getColumnDimension('H')->setWidth(20);
               $event->sheet->getColumnDimension('I')->setWidth(20);
               $event->sheet->getColumnDimension('J')->setWidth(20);
               $event->sheet->getColumnDimension('K')->setWidth(20);
               $event->sheet->getColumnDimension('L')->setWidth(20);
               $event->sheet->getColumnDimension('M')->setWidth(20);
               $event->sheet->getColumnDimension('N')->setWidth(20);
               $event->sheet->getColumnDimension('O')->setWidth(20);
               $event->sheet->getColumnDimension('P')->setWidth(20);
               $event->sheet->getColumnDimension('Q')->setWidth(20);
               $event->sheet->getColumnDimension('R')->setWidth(20);
               $event->sheet->getColumnDimension('S')->setWidth(20);

            },
        ];
    }
}
