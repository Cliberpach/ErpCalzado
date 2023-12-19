<?php

namespace App\Pos;

use App\Compras\DetalleCuentaProveedor;
use App\Mantenimiento\Colaborador\Colaborador;
use App\Ventas\DetalleCuentaCliente;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class MovimientoCaja extends Model
{
    protected $table = "movimiento_caja";
    protected $fillable = [
        'caja_id', 'colaborador_id',
        'monto_inicial', 'monto_final',
        'fecha_apertura', 'fecha_cierre',
        'estado_movimiento',
        'fecha',
        'estado',
    ];
    public $timestamps = true;
    public function detalleMovimientoVentas()
    {
        return $this->hasMany(DetalleMovimientoVentaCaja::class, 'mcaja_id');
    }
    public function detalleMoviemientoEgresos()
    {
        return $this->hasMany(DetalleMovimientoEgresosCaja::class, 'mcaja_id');
    }
    public function caja()
    {
        return $this->belongsTo(Caja::class, 'caja_id');
    }
    public function colaborador()
    {
        return $this->belongsTo(Colaborador::class, 'colaborador_id');
    }
    public function detalleCuentaProveedor(){
        return $this->hasMany(DetalleCuentaProveedor::class,'mcaja_id');
    }
    public function detalleCuentaCliente(){
        return $this->hasMany(DetalleCuentaCliente::class,'mcaja_id');
    }

    public function totalIngresos($DetalleMovimientoVentaCaja)
    {
        $total = 0;
        foreach ($DetalleMovimientoVentaCaja as $key => $venta) {
            if($venta->documento->tipo_pago_id==1)
            {
                $total = $total + $venta->documento->importe;
            }
            else{
                $total = $total + $venta->documento->efectivo;
            }

        }
        return $total;
    }
    public function totalEgresos($detalleMoviemientoEgresos)
    {
        $total = 0;
        foreach ($detalleMoviemientoEgresos as $key => $egreso) {
            if ($egreso->egreso->estado == "ACTIVO") {
                $total = $total + $egreso->egreso->importe;
            }
        }
        return $total;
    }
}
