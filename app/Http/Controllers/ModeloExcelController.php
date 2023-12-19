<?php

namespace App\Http\Controllers;

use App\Exports\Modelo\ModeloExport;
use App\Exports\Categoria\CategoriaExport;
use App\Exports\Cliente\ClienteExport;
use App\Exports\Cliente\ClienteMultiExport;
use App\Exports\Marca\MarcaExport;
use App\Exports\Producto\ProductoMultiExport;
use App\Exports\Proveedor\ProveedorMultiExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ModeloExcelController extends Controller
{
    public function cliente()
    {
        ob_end_clean();
        ob_start();
        return  Excel::download(new ClienteMultiExport(), 'modelo_cliente.xlsx');
    }
    public function categoria(){
        ob_end_clean();
        ob_start();
        return  Excel::download(new CategoriaExport(), 'modelo_categoria.xlsx');
    }
    public function marca() {
        ob_end_clean();
        ob_start();
        return Excel::download(new MarcaExport(), 'modelo_marca.xlsx');
    }
    public function modelo() {
        ob_end_clean();
        ob_start();
        return Excel::download(new ModeloExport(), 'modelo_modelo.xlsx');
    }
    public function producto() {
        ob_end_clean();
        ob_start();
        return Excel::download(new ProductoMultiExport(), 'modelo_producto.xlsx');
    }
    public function proveedor() {
        ob_end_clean();
        ob_start();
        return Excel::download(new ProveedorMultiExport(), 'modelo_proveedor.xlsx');
    }
}
