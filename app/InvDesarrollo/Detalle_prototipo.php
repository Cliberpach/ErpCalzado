<?php

namespace App\InvDesarrollo;

use Illuminate\Database\Eloquent\Model;

class Detalle_prototipo extends Model
{
    protected $table='detalle_prototipos';
    protected $fillable =['prototipo_id','nombre_articulo','cantidad','observacion'];
    public $timestamps=true;

    public function prototipo()
	{
	    return $this->belongsTo('App\InvDesarrollo\Prototipo');
	}
}
