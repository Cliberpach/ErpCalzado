<?php

namespace App\Exports\Reportes\Compras;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class DocumentoExport implements  FromCollection,WithHeadings,WithEvents
{
    public $proveedor,$fecha_ini,$fecha_fin;
    use Exportable;

    public function headings(): array
    {
        return [
            [
                "FECHA",
                "PROVEEDOR",
                "MONTO",
                "MODO PAGO",
                "NUMERO",
            ]
        ];
    }

    function title(): String
    {
        return "COMPRAS ".$this->fecha_ini."-".$this->fecha_fin;
    }

    public function __construct($proveedor,$fecha_ini,$fecha_fin)
    {
        $this->proveedor = $proveedor;
        $this->fecha_ini = $fecha_ini;
        $this->fecha_fin = $fecha_fin;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        $consulta = DB::table('compra_documentos')
        ->join('proveedores','proveedores.id','=','compra_documentos.proveedor_id')
        ->select(
            'compra_documentos.fecha_emision as fecha',
            'proveedores.descripcion as proveedor',
            'compra_documentos.total as monto',
            'compra_documentos.modo_compra as modo_pago',
            'compra_documentos.numero_doc as documento',
        )->where('compra_documentos.estado','!=','ANULADO');
        if($this->proveedor)
        {
            $consulta = $consulta->where('compra_documentos.proveedor_id',$this->proveedor);
        }
        if($this->fecha_ini && $this->fecha_fin)
        {
            $consulta = $consulta->whereBetween('compra_documentos.fecha_emision',[$this->fecha_ini,$this->fecha_fin]);
        }

        return $consulta->get();

    }
    public function registerEvents(): array
    {
        return [

            BeforeWriting::class => [self::class, 'beforeWriting'],
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getStyle('A1:E1')->applyFromArray([

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
                $event->sheet->getStyle('A1:E1')->applyFromArray([
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

