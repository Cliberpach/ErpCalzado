<?php

namespace App\Pos;

use App\Ventas\Documento\Documento;
use Illuminate\Database\Eloquent\Model;

class DetalleMovimientoVentaCaja extends Model
{
    protected $table="detalle_movimiento_venta";
    protected $fillable=[
        'mcaja_id','cdocumento_id'
    ];
    public $timestamps=true;
    public function documento() {
        return $this->belongsTo(Documento::class,'cdocumento_id');
    }
}
