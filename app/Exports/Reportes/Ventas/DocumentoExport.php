<?php

namespace App\Exports\Reportes\Ventas;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class DocumentoExport implements  FromCollection,WithHeadings,WithEvents
{
    public $cliente,$fecha_ini,$fecha_fin;
    use Exportable;

    public function headings(): array
    {
        return [
            [
                "FECHA",
                "CLIENTE",
                "MONTO",
                "MODO PAGO",
                "TIPO PAGO",
                "NUMERO",
            ]
        ];
    }

    function title(): String
    {
        return "VENTAS ".$this->fecha_ini."-".$this->fecha_fin;
    }

    public function __construct($cliente,$fecha_ini,$fecha_fin)
    {
        $this->cliente = $cliente;
        $this->fecha_ini = $fecha_ini;
        $this->fecha_fin = $fecha_fin;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $consulta = DB::table('cotizacion_documento')
        ->join('clientes','clientes.id','=','cotizacion_documento.cliente_id')
        ->join('condicions','condicions.id','=','cotizacion_documento.condicion_id')
        ->leftjoin('tipos_pago','tipos_pago.id','=','cotizacion_documento.tipo_pago_id')
        ->select(
            'cotizacion_documento.fecha_documento as fecha',
            'clientes.nombre as cliente',
            'cotizacion_documento.total as monto',
            'condicions.descripcion as modo_pago',
            'tipos_pago.descripcion as tipo_pago',
            DB::raw('(CONCAT(cotizacion_documento.serie, "-" , cotizacion_documento.correlativo)) as numero'),
        );

        if($this->cliente)
        {
            $consulta = $consulta->where('cotizacion_documento.cliente_id',$this->cliente);
        }

        if($this->fecha_ini && $this->fecha_fin)
        {
            $consulta->whereBetween('cotizacion_documento.fecha_documento',[$this->fecha_ini,$this->fecha_fin]);
        }
        return  $consulta->get();
        
    }
    public function registerEvents(): array
    {
        return [

            BeforeWriting::class => [self::class, 'beforeWriting'],
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getStyle('A1:F1')->applyFromArray([

                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                            'rotation' => 90,
                            'startColor' => [
                                'argb' => '00bbd4',
                            ],
                            'endColor' => [
                                'argb' => '00bbd4',
                            ],
                        ]
                    ]

                );
                $event->sheet->getStyle('A1:F1')->applyFromArray([
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
            $event->sheet->getColumnDimension('C')->setWidth(15);
            //    $event->sheet->getColumnDimension('D')->setWidth(20);
            //    $event->sheet->getColumnDimension('E')->setWidth(20);
            //    $event->sheet->getColumnDimension('F')->setWidth(20);
            //    $event->sheet->getColumnDimension('G')->setWidth(20);
            //    $event->sheet->getColumnDimension(''H)->setWidth(20);

            },
        ];
    }
}
