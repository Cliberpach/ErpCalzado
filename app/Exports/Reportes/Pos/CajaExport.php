<?php

namespace App\Exports\Reportes\Pos;

use App\Pos\MovimientoCaja;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class CajaExport implements FromCollection,WithHeadings,WithEvents
{
    public $caja,$fecha_ini,$fecha_fin;
    use Exportable;

    public function headings(): array
    {
        return [
            [
                "CAJA",
                "FECHA",
                "MONTO INICIAL",
                "VENTAS",
                "COBRANZAS",
                "PAGOS",
                "EGRESOS",
                "SALDO",
            ]
        ];
    }

    function title(): String
    {
        return "MOVIMIENTO CAJA ".$this->fecha_ini."-".$this->fecha_fin;
    }

    public function __construct($caja,$fecha_ini,$fecha_fin)
    {
        $this->caja = $caja;
        $this->fecha_ini = $fecha_ini;
        $this->fecha_fin = $fecha_fin;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        if($this->caja != '' && $this->fecha_ini != '' && $this->fecha_fin != '')
        {
            return DB::table('movimiento_caja')
            ->join('caja','movimiento_caja.caja_id','=','caja.id')
            ->select(
                'caja.nombre as caja',
                'movimiento_caja.fecha_apertura',
                'movimiento_caja.monto_inicial as inicio',
                DB::raw('ifnull((select sum(cv.total) from  detalle_movimiento_venta  dmv inner join cotizacion_documento cv on dmv.cdocumento_id = cv.id where dmv.mcaja_id = movimiento_caja.id and cv.condicion_id = 1 and cv.estado_pago = "PAGADA"), 0) as ventas'),
                DB::raw('ifnull((select sum(dcc.monto) from  detalle_cuenta_cliente dcc where dcc.mcaja_id = movimiento_caja.id), 0) as cobranzas'),
                DB::raw('ifnull((select sum(dcp.importe + dcp.efectivo) from  detalle_cuenta_proveedor dcp where dcp.mcaja_id = movimiento_caja.id), 0) as pagos'),
                DB::raw('ifnull((select sum(e.importe) from  detalle_movimiento_egresos dme inner join egreso e on dme.egreso_id = e.id where dme.mcaja_id = movimiento_caja.id), 0) as egresos'),
                'movimiento_caja.monto_final as saldo'
            )
            ->where('movimiento_caja.estado_movimiento','CIERRE')
            ->where('movimiento_caja.caja_id',$this->caja)
            ->whereBetween('movimiento_caja.fecha',[$this->fecha_ini,$this->fecha_fin])->get();

        }
        else if($this->caja != '' && $this->fecha_ini == '' && $this->fecha_fin == '')
        {
            return DB::table('movimiento_caja')
            ->join('caja','movimiento_caja.caja_id','=','caja.id')
            ->select(
                'caja.nombre as caja',
                'movimiento_caja.fecha_apertura',
                'movimiento_caja.monto_inicial as inicio',
                DB::raw('ifnull((select sum(cv.total) from  detalle_movimiento_venta  dmv inner join cotizacion_documento cv on dmv.cdocumento_id = cv.id where dmv.mcaja_id = movimiento_caja.id and cv.condicion_id = 1 and cv.estado_pago = "PAGADA"), 0) as ventas'),
                DB::raw('ifnull((select sum(dcc.monto) from  detalle_cuenta_cliente dcc where dcc.mcaja_id = movimiento_caja.id), 0) as cobranzas'),
                DB::raw('ifnull((select sum(dcp.importe + dcp.efectivo) from  detalle_cuenta_proveedor dcp where dcp.mcaja_id = movimiento_caja.id), 0) as pagos'),
                DB::raw('ifnull((select sum(e.importe) from  detalle_movimiento_egresos dme inner join egreso e on dme.egreso_id = e.id where dme.mcaja_id = movimiento_caja.id), 0) as egresos'),
                'movimiento_caja.monto_final as saldo'
            )
            ->where('movimiento_caja.estado_movimiento','CIERRE')
            ->where('movimiento_caja.caja_id',$this->caja)->get();
        }
        else if($this->caja == '' && $this->fecha_ini != '' && $this->fecha_fin != '')
        {
            return DB::table('movimiento_caja')
            ->join('caja','movimiento_caja.caja_id','=','caja.id')
            ->select(
                'caja.nombre as caja',
                'movimiento_caja.fecha_apertura',
                'movimiento_caja.monto_inicial as inicio',
                DB::raw('ifnull((select sum(cv.total) from  detalle_movimiento_venta  dmv inner join cotizacion_documento cv on dmv.cdocumento_id = cv.id where dmv.mcaja_id = movimiento_caja.id and cv.condicion_id = 1 and cv.estado_pago = "PAGADA"), 0) as ventas'),
                DB::raw('ifnull((select sum(dcc.monto) from  detalle_cuenta_cliente dcc where dcc.mcaja_id = movimiento_caja.id), 0) as cobranzas'),
                DB::raw('ifnull((select sum(dcp.importe + dcp.efectivo) from  detalle_cuenta_proveedor dcp where dcp.mcaja_id = movimiento_caja.id), 0) as pagos'),
                DB::raw('ifnull((select sum(e.importe) from  detalle_movimiento_egresos dme inner join egreso e on dme.egreso_id = e.id where dme.mcaja_id = movimiento_caja.id), 0) as egresos'),
                'movimiento_caja.monto_final as saldo'
            )
            ->where('movimiento_caja.estado_movimiento','CIERRE')
            ->whereBetween('movimiento_caja.fecha',[$this->fecha_ini,$this->fecha_fin])->get();
        }
        else{
            return DB::table('movimiento_caja')
            ->join('caja','movimiento_caja.caja_id','=','caja.id')
            ->select(
                'caja.nombre as caja',
                'movimiento_caja.fecha_apertura',
                'movimiento_caja.monto_inicial as inicio',
                DB::raw('ifnull((select sum(cv.total) from  detalle_movimiento_venta  dmv inner join cotizacion_documento cv on dmv.cdocumento_id = cv.id where dmv.mcaja_id = movimiento_caja.id and cv.condicion_id = 1 and cv.estado_pago = "PAGADA"), 0) as ventas'),
                DB::raw('ifnull((select sum(dcc.monto) from  detalle_cuenta_cliente dcc where dcc.mcaja_id = movimiento_caja.id), 0) as cobranzas'),
                DB::raw('ifnull((select sum(dcp.importe + dcp.efectivo) from  detalle_cuenta_proveedor dcp where dcp.mcaja_id = movimiento_caja.id), 0) as pagos'),
                DB::raw('ifnull((select sum(e.importe) from  detalle_movimiento_egresos dme inner join egreso e on dme.egreso_id = e.id where dme.mcaja_id = movimiento_caja.id), 0) as egresos'),
                'movimiento_caja.monto_final as saldo'
            )
            ->where('movimiento_caja.estado_movimiento','CIERRE')->get();
        }
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
