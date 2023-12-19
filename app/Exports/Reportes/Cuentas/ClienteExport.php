<?php

namespace App\Exports\Reportes\Cuentas;


use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class ClienteExport implements FromCollection,WithHeadings,WithEvents
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
                "NUMERO",
                "ACTA",
                "SALDO",
                "FECHA ULT. PAGO",
            ]
        ];
    }

    function title(): String
    {
        return "CUENTAS CLIENTE ".$this->fecha_ini."-".$this->fecha_fin;
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
       $consulta = DB::table('cuenta_cliente')
        ->join('cotizacion_documento','cotizacion_documento.id','=','cuenta_cliente.cotizacion_documento_id')
        ->join('clientes','clientes.id','=','cotizacion_documento.cliente_id')
        ->select(
            'cotizacion_documento.fecha_documento as fecha',
            'clientes.nombre as cliente',
            'cotizacion_documento.total as monto',
            DB::raw('(CONCAT(cotizacion_documento.serie, "-" , cotizacion_documento.correlativo)) as numero'),
            DB::raw('(cotizacion_documento.total - cuenta_cliente.saldo) as acta'),
            'cuenta_cliente.saldo',
            DB::raw('ifnull((select fecha
            from detalle_cuenta_cliente dcc
            where dcc.cuenta_cliente_id = cuenta_cliente.id
           order by id desc
           limit 1),"-") as fecha_ultima'),
        );

        if($this->cliente)
        {
            $consulta = $consulta->where('cotizacion_documento.cliente_id',$this->cliente);
        }

        if($this->fecha_ini && $this->fecha_fin)
        {
            $consulta = $consulta->whereBetween('cuenta_cliente.fecha_doc',[$this->fecha_ini,$this->fecha_fin]);
        }

        return $consulta->get();

    }
    public function registerEvents(): array
    {
        return [

            BeforeWriting::class => [self::class, 'beforeWriting'],
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getStyle('A1:G1')->applyFromArray([

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
                $event->sheet->getStyle('A1:G1')->applyFromArray([
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
