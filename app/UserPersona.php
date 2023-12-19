<?php

namespace App;

use App\Mantenimiento\Persona\Persona;
use Illuminate\Database\Eloquent\Model;

class UserPersona extends Model
{
    protected $table="user_persona";
    protected $fillable = [
        'user_id','persona_id'
    ];
    public $timestamps=true;

    public function persona(){
        return $this->belongsTo(Persona::class,'persona_id');
    }
    
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
