<?php

namespace App\Pos;

use App\Mantenimiento\Tabla\Detalle;
use Illuminate\Database\Eloquent\Model;

class Egreso extends Model
{
    protected $table="egreso";
    protected $guarded=[''];

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
