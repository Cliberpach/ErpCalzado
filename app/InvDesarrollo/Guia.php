<?php

namespace App\InvDesarrollo;

use Illuminate\Database\Eloquent\Model;

class Guia extends Model
{
    protected $table='guias';
    protected $fillable = 
     [ 
         'prototipo_id',
         'unidades_a_producir',
         'area_responsable1',
         'area_responsable2',
         'fecha',
         'observacion',
         'usuario_id',
         'estado'
        ];
    public $timestamps=true;

    public function prototipo()
	{
	    return $this->belongsTo('App\InvDesarrollo\Prototipo','prototipo_id');
	}
}

