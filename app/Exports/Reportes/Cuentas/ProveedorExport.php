<?php

namespace App\Exports\Reportes\Cuentas;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;


class ProveedorExport implements FromCollection,WithHeadings,WithEvents
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
                "MONEDA",
                "NUMERO",
                "ACTA",
                "SALDO",
                "FECHA ULT. PAGO",
            ]
        ];
    }

    function title(): String
    {
        return "CUENTAS PROVEEDOR ".$this->fecha_ini."-".$this->fecha_fin;
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
        $consulta = DB::table('cuenta_proveedor')
            ->join('compra_documentos','compra_documentos.id','=','cuenta_proveedor.compra_documento_id')
            ->join('proveedores','proveedores.id','=','compra_documentos.proveedor_id')
            ->select(
                'compra_documentos.fecha_emision as fecha',
                'proveedores.descripcion as proveedor',
                'compra_documentos.total as monto',
                'compra_documentos.moneda',
                'compra_documentos.numero_doc as documento',
                DB::raw('(compra_documentos.total - cuenta_proveedor.saldo) as acta'),
                'cuenta_proveedor.saldo',
                DB::raw('ifnull((select fecha
                from detalle_cuenta_proveedor dcp
                where dcp.cuenta_proveedor_id = cuenta_proveedor.id
            order by id desc
            limit 1),"-") as fecha_ultima'),
            );
        if($this->proveedor)
        {
            $consulta = $consulta->where('compra_documentos.proveedor_id',$this->proveedor);
        }

        if($this->fecha_ini && $this->fecha_fin)
        {
            $consulta = $consulta->whereBetween('cuenta_proveedor.fecha_doc',[$this->fecha_ini,$this->fecha_fin]);
        }

        return $consulta->get();

    }
    public function registerEvents(): array
    {
        return [

            BeforeWriting::class => [self::class, 'beforeWriting'],
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getStyle('A1:H1')->applyFromArray([

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
                $event->sheet->getStyle('A1:H1')->applyFromArray([
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
