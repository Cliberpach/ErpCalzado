<?php

namespace App\InvDesarrollo;

use Illuminate\Database\Eloquent\Model;

class Detalle_guia extends Model
{
    protected $table='detalle_guias';
    protected $fillable =['guia_id','articulo_id','cantidad_solicitada','cantidad_entregada','cantidad_devuelta','observacion'];
    public $timestamps=true;

    public function guia()
	{
	    return $this->belongsTo('App\InvDesarrollo\Guia');
	}
	public function articulo()
	{
	    return $this->belongsTo('App\Compras\Articulo');
	}
}


