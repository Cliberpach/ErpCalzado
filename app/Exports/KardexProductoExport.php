<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class KardexProductoExport implements FromView
{
    protected $kardex;
    protected $parametros;
    function __construct($kardex,$parametros){
       $this->kardex = $kardex;
       $this->parametros = $parametros;
    }
    
    public function view(): View
    {
        return view('consultas.kardex.exports.kardex-producto', [
            "kardex"=>$this->kardex,
            "empresa"=> DB::table("empresas")->where("estado","ACTIVO")->first(),
            "parametros"=> $this->parametros
        ]);
    } 
}