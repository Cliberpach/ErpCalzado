@extends('layout') @section('content')

@section('kardex-active', 'active')
@section('producto_kardex-active', 'active')
@section('producto_kardex-top-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-8">
       <h2  style="text-transform:uppercase"><b>Top Productos mas vendido</b></h2>
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
        <div class="col-12">
            <div class="row align-items-end">
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label for="fecha_desde">Fecha desde</label>
                        <input type="date" id="fecha_desde" class="form-control">
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label for="fecha_desde">Fecha hasta</label>
                        <input type="date" id="fecha_hasta" class="form-control">
                    </div>
                </div>
                <div class="col-12 col-md-2">
                    <div class="form-group">
                        <label for="fecha_desde">Top</label>
                        <input type="text" id="top" class="form-control" onkeypress="isNumber(event)" value="10">
                    </div>
                </div>
                <div class="col-12 col-md-2">
                    <div class="form-group">
                        <button class="btn btn-primary btn-block" onclick="initTable()"><i class="fa fa-refresh"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table dataTables-kardex table-striped table-bordered table-hover"
                        style="text-transform:uppercase">
                            <thead>
                                <tr>
                                    <th class="text-center">CODIGO</th>
                                    <th class="text-center">PRODUCTO</th>
                                    <th class="text-center">CANTIDAD</th>
                                    <th class="text-center">IMPORTE</th>
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
    initTable();

    tablaDatos = $('.dataTables-kardex').DataTable();

});

function initTable()
{
    let verificar = true;
    var fecha_desde = $('#fecha_desde').val();
    var fecha_hasta = $('#fecha_hasta').val();
    var top = $('#top').val();
    if (fecha_desde != '' && fecha_desde != null && fecha_hasta == '') {
        verificar = false;
        toastr.error('Ingresar fecha hasta');
    }

    if (fecha_hasta != '' && fecha_hasta != null && fecha_desde == '') {
        verificar = false;
        toastr.error('Ingresar fecha desde');
    }

    if (fecha_desde > fecha_hasta && fecha_hasta != '' && fecha_desde != '') {
        verificar = false;
        toastr.error('Fecha desde debe ser menor que fecha hasta');
    }

    if (top == '') {
        verificar = false;
        toastr.error('Ingrese el top a consultar');
    }

    if (convertFloat(top) < 1) {
        verificar = false;
        toastr.error('El top de productos debe ser  mayor a 0');
    }

    if(verificar)
    {
        let timerInterval;
        Swal.fire({
            title: 'Cargando...',
            icon: 'info',
            customClass: {
                container: 'my-swal'
            },
            timer: 10,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
                Swal.stopTimer();
                $.ajax({
                    dataType : 'json',
                    type : 'post',
                    url : '{{ route('consultas.kardex.producto.getTableTop') }}',
                    data : {'_token' : $('input[name=_token]').val(), 'fecha_desde' : fecha_desde, 'fecha_hasta' : fecha_hasta, 'top' : top},
                    success: function(response) {
                        kardex = [];
                        kardex = response.kardex;
                        loadTable();
                        timerInterval = 0;
                        Swal.resumeTimer();
                    }
                });
            },
            willClose: () => {
                clearInterval(timerInterval)
            }
        });
    }
    return false;
}

function loadTable()
{
    $('.dataTables-kardex').dataTable().fnDestroy();
    $('.dataTables-kardex').DataTable({
        "dom": '<"html5buttons"B>lTfgitp',
        "buttons": [{
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i> Excel',
                titleAttr: 'Excel',
                title: 'CONSULTA TOP PRODUCTO'
            },
            {
                titleAttr: 'Imprimir',
                extend: 'print',
                text: '<i class="fa fa-print"></i> Imprimir',
                customize: function(win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table')
                        .addClass('compact')
                        .css('font-size', 'inherit');
                }
            }
        ],
        "bPaginate": true,
        "bLengthChange": true,
        "bFilter": true,
        "bInfo": true,
        "bAutoWidth": false,
        "data": kardex,
        "columns": [
            { data: 'codigo',className: "text-center"},
            { data: 'producto',className: "text-center"},
            { data: 'cantidad',className: "text-center"},
            { data: 'importe',className: "text-center"},
        ],
        "language": {
                    "url": "{{asset('Spanish.json')}}"
        },
        "order": [],


    });
    return false;
}
</script>
@endpush
