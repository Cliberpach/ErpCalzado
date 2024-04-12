<?php

namespace App\Almacenes;

use Illuminate\Database\Eloquent\Model;

class TestTarea extends Model
{
    protected $table = 'test_tareas_programadas';
    protected $guarded = [];
    public $timestamps = true;
}
