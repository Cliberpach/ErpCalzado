<?php

namespace App\Pos;

use App\Mantenimiento\Tabla\Detalle;
use Illuminate\Database\Eloquent\Model;

class Egreso extends Model
{
    protected $table="egreso";
    protected $fillable=[
        'tipodocumento_id',
        'cuenta_id',
        'documento',
        'descripcion',
        'monto',
        'importe',
        'efectivo',
        'tipo_pago_id',
        'usuario',
        'user_id',
        'estado'
    ];
    public $timestamps=true;
    public function cuenta() {
        return $this->belongsTo(Detalle::class,'cuenta_id');
    }
    public function tipoDocumento() {
        return $this->belongsTo(Detalle::class,'tipodocumento_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
