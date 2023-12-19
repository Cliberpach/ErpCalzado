<?php

namespace App\Pos;

use Illuminate\Database\Eloquent\Model;

class DetalleMovimientoEgresosCaja extends Model
{
    protected $table="detalle_movimiento_egresos";
    protected $fillable=[
        'mcaja_id','egreso_id'
    ];
    public $timestamps=true;
    public function egreso() {
        return $this->belongsTo(Egreso::class,'egreso_id');
    }
}
