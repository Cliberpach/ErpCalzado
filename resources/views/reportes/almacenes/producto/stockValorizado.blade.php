@extends('layout') @section('content')

@section('reporte-active', 'active')
@section('stock_valorizado_reporte-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-8">
       <h2  style="text-transform:uppercase"><b>Stock valorizado productos</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Productos</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table dataTables-stock table-striped table-bordered table-hover"
                        style="text-transform:uppercase">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 5%;">CODIGO</th>
                                    <th class="text-center" style="width: 30%;">PRODUCTO</th>
                                    <th class="text-center" style="width: 10%;">ALMACEN</th>
                                    <th class="text-center" style="width: 10%;">CATEGORIA</th>
                                    <th class="text-center" style="width: 10%;">MARCA</th>
                                    <th class="text-center" style="width: 10%;">P. VENTA</th>
                                    <th class="text-center" style="width: 10%;">STOCK</th>
                                    <th class="text-center" style="width: 15%;">STOCK VALORIZADO</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop
@push('styles')
<!-- DataTable -->
<link href="{{asset('Inspinia/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
<style>
    .letrapequeña {
        font-size: 11px;
    }

</style>
@endpush

@push('scripts')
<!-- DataTable -->
<script src="{{asset('Inspinia/js/plugins/dataTables/datatables.min.js')}}"></script>
<script src="{{asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>

<script>
$(document).ready(function() {
    var kardex = [];
    // DataTables
    tablaDatos = $('.dataTables-stock').DataTable({
        "dom": '<"html5buttons"B>lTfgitp',
            "buttons": [
                {
                    extend:    'excelHtml5',
                    text:      '<i class="fa fa-file-excel-o"></i> Excel',
                    titleAttr: 'Excel',
                    title: 'PRODUCTOS'
                },

                {
                    titleAttr: 'Imprimir',
                    extend: 'print',
                    text:      '<i class="fa fa-print"></i> Imprimir',
                    customize: function (win){
                        $(win.document.body).addClass('white-bg');
                        $(win.document.body).css('font-size', '10px');

                        $(win.document.body).find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                    }
                }
            ],
            "bPaginate": true,
            "serverSide":true,
            "processing":true,
            "bLengthChange": true,
            "bFilter": true,
            "order": [],
            "bInfo": true,
            'bAutoWidth': false,
            "ajax": "{{ route('reporte.producto.stockvalorizado.getTable') }}",
            "columns": [{
                    data: 'codigo',
                    className: "text-left",
                    name:"productos.codigo"
                },
                {
                    data: 'nombre',
                    className: "text-left",
                    name:"productos.nombre"
                },
                {
                    data: 'almacen',
                    className: "text-left",
                    name:"almacenes.descripcion"
                },
                {
                    data: 'categoria',
                    className: "text-left",
                    name:"categorias.descripcion"
                },
                {
                    data: 'marca',
                    className: "text-left",
                    name:"marcas.marca"
                },
                {
                    data: 'stock',
                    className: "text-center",
                    name:"productos.stock"
                },
                {
                    data: 'precio_venta_minimo',
                    className: "text-center",
                    name:"productos.precio_venta_minimo"
                },
                {
                    data: null,
                    defaultContent: "",
                    className: "text-center",
                    render: function(data) {
                        return data.stock_valorizado;
                    }
                }

            ],
            "language": {
                "url": "{{ asset('Spanish.json') }}"
            },
            createdRow: function(row, data, dataIndex, cells) {
                $(row).addClass('fila_lote');
                $(row).attr('data-href', "");
            },
    });

});
</script>
@endpush
