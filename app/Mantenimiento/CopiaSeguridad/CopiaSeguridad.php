<?php

namespace App\Mantenimiento\CopiaSeguridad;

use App\User;
use Illuminate\Database\Eloquent\Model;

class CopiaSeguridad extends Model
{
    protected $table = 'copias_seguridad';

    protected $fillable = [
        'nombre',
        'ruta',
        'tamano_bytes',
        'estado',
        'error',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
