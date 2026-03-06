<?php

namespace App\Models\Ventas\TipoCliente;

use Illuminate\Database\Eloquent\Model;

class TipoCliente extends Model
{
    protected $table = 'tipos_clientes';

    protected $fillable = [
        'nombre',
        'estado',

        'user_creator_id',
        'user_editor_id',
        'user_deletor_id',
        'user_creator_name',
        'user_editor_name',
        'user_deletor_name',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->user_creator_id = auth()->id();
                $model->user_creator_name = auth()->user()->usuario;
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->user_editor_id = auth()->id();
                $model->user_editor_name = auth()->user()->usuario;
            }
            if ($model->isDirty('estado') && $model->status === 'ANULADO') {
                if (auth()->check()) {
                    $model->user_deletor_id = auth()->id();
                    $model->user_deletor_name = auth()->user()->usuario;
                }
            }
        });
    }
}
