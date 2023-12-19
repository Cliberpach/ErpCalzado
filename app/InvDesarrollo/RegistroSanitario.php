<?php

namespace App\InvDesarrollo;

use Illuminate\Database\Eloquent\Model;

class RegistroSanitario extends Model
{
    protected $table = 'registro_sanitario';
    protected $fillable = ['producto_id','fecha_inicio','fecha_fin','ruta_archivo_word','archivo_word','ruta_archivo_pdf','archivo_pdf'];
    public $timestamps = true;

    public function producto()
	{
	    return $this->belongsTo('App\Almacenes\Producto','producto_id');
	}
}