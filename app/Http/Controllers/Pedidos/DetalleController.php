<?php

namespace App\Http\Controllers\Pedidos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DetalleController extends Controller
{
    public function index(){
        return view('pedidos.detalles.index');
    }
}
