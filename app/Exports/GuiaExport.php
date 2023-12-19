<?php

namespace App\Exports;
use App\Ventas\Guia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class GuiaExport implements FromCollection,WithHeadings,WithEvents
{
    use Exportable;
    public $fecha_desde,$fecha_hasta;

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
            "ENVIADA"
            ]
        ];
    }

    function title(): String
    {
        return "Documentos";
    }

    public function __construct($fecha_desde,$fecha_hasta)
    {
        $this->fecha_desde = $fecha_desde;
        $this->fecha_hasta = $fecha_hasta;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $consulta = Guia::where('estado','!=','NULO');
        if($this->fecha_desde && $this->fecha_hasta)
        {
            $consulta = $consulta->whereBetween(DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d")'), [$this->fecha_desde, $this->fecha_hasta]);
        }

        $consulta = $consulta->orderBy('id', 'desc')->get();

        $coleccion = collect();
        foreach($consulta as $doc){
            $coleccion->push([
                'RUC-EMISOR' => $doc->documento->ruc_empresa,
                'DOC.' => 'GUIA DE REMISIÓN',
                'CODIGO.DOC' => '09',
                'FECHA' => Carbon::parse($doc->created_at)->format( 'Y-m-d'),
                'TICKET' => $doc->serie.' - '.$doc->correlativo,
                'TIENDA' => $doc->documento->empresa,
                'RUC/DNI' => $doc->documento->documento_cliente,
                'TIPO.CLIENTE' => $doc->documento->tipoDocumentoCliente(),
                'CLIENTE' => $doc->documento->cliente,
                'ESTADO' => $doc->estado,
                'ENVIADA' => $doc->sunat == '1' ? 'SI' : 'NO',
            ]);
        }

        return $coleccion->sortBy('FECHA');
    }

    public function registerEvents(): array
    {
        return [

            BeforeWriting::class => [self::class, 'beforeWriting'],
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getStyle('A1:K1')->applyFromArray([

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
                $event->sheet->getStyle('A1:K1')->applyFromArray([
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
