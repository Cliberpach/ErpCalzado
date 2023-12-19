<?php

namespace App\InvDesarrollo;

use Illuminate\Database\Eloquent\Model;

class Prototipo extends Model
{
    protected $table='prototipos';
    protected $fillable =['producto','fecha_registro','fecha_inicio','fecha_fin','registro','ruta_imagen','imagen','ruta_archivo_word','archivo_word'];
    public $timestamps=true;
}
