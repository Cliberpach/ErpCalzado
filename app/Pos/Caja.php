<?php

namespace App\Pos;

use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    protected $table="caja";
    protected $fillable=[
        'nombre','estado','estado_caja'
    ];
    public $timestamps = true;
    public function movimientos()
    {
         return $this->hasMany(MovimientoCaja::class,'caja_id');
    }
}
