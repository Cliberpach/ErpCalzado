<?php

namespace App\Compras;

use Illuminate\Database\Eloquent\Model;

class Enviado extends Model
{
    protected $table = 'correos_ordenes';
    public $timestamps = true;
    protected $fillable = [
        'orden_id',
        'enviado',
        'usuario',
    ];
    public function orden()
    {
        return $this->belongsTo('App\Compras\Orden');
    }
}
