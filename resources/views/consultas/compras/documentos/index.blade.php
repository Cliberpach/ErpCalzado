@extends('layout') @section('content')

@section('consulta-active', 'active')
@section('consulta-compras-active', 'active')
@section('consulta-compras-documento-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
       <h2  style="text-transform:uppercase"><b>Listado de Documentos de Compra</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Documentos de Compra</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-12">
            <div class="row align-items-end">
                <div class="col-12 col-md-5">
                    <div class="form-group">
                        <label for="fecha_desde">Fecha desde</label>
                        <input type="date" id="fecha_desde" class="form-control">
                    </div>
                </div>
                <div class="col-12 col-md-5">
                    <div class="form-group">
                        <label for="fecha_desde">Fecha hasta</label>
                        <input type="date" id="fecha_hasta" class="form-control">
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
                        <table class="table dataTables-orden table-striped table-bordered table-hover"
                        style="text-transform:uppercase">
                            <thead>
                                <tr>
                                    <th class="text-center">TIPO PAGO</th>
                                    <th class="text-center">EMISION</th>
                                    <th class="text-center">TIPO</th>
                                    <th class="text-center">PROVEEDOR</th>
                                    <th class="text-center">MONTO</th>
                                    <th class="text-center">CONDICION</th>
                                    <th class="text-center">ESTADO</th>
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
    var documentos = [];
    // DataTables
    initTable();

    tablaDatos = $('.dataTables-orden').DataTable();

});

function initTable()
{
    let verificar = true;
    var fecha_desde = $('#fecha_desde').val();
    var fecha_hasta = $('#fecha_hasta').val();
    if (fecha_desde !== '' && fecha_desde !== null && fecha_hasta == '') {
        verificar = false;
        toastr.error('Ingresar fecha hasta');
    }

    if (fecha_hasta !== '' && fecha_hasta !== null && fecha_desde == '') {
        verificar = false;
        toastr.error('Ingresar fecha desde');
    }

    if (fecha_desde > fecha_hasta && fecha_hasta !== '' && fecha_desde !== '') {
        verificar = false;
        toastr.error('Fecha desde debe ser menor que fecha hasta');
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
                    url : '{{ route('consultas.compras.documento.getTable') }}',
                    data : {'_token' : $('input[name=_token]').val(), 'fecha_desde' : fecha_desde, 'fecha_hasta' : fecha_hasta},
                    success: function(response) {
                        if (response.success) {
                            documentos = [];
                            documentos = response.documentos;
                            loadTable();
                            timerInterval = 0;
                            Swal.resumeTimer();
                            //console.log(colaboradores);
                        } else {
                            Swal.resumeTimer();
                            documentos = [];
                            loadTable();
                        }
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
    $('.dataTables-orden').dataTable().fnDestroy();
    $('.dataTables-orden').DataTable({
        "dom": '<"html5buttons"B>lTfgitp',
        "buttons": [{
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i> Excel',
                titleAttr: 'Excel',
                title: 'CONSULTA DOCUMENTO VENTA'
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
        "data": documentos,
        "columns": [
            //DOCUMENTO DE COMPRA
            {
                data: 'tipo_pago',
                className: "text-center",
                visible: false
            },
            {
                data: 'fecha_emision',
                className: "text-center"
            },
            {
                data: 'tipo',
                className: "text-center",
            },
            {
                data: 'proveedor',
                className: "text-left"
            },
            {
                data: 'total',
                className: "text-center"
            },
            {
                data: 'condicion',
                className: "text-center"
            },


            {
                data: null,
                className: "text-center",
                render: function(data) {
                    switch (data.estado) {
                        case "PENDIENTE":
                            return "<span class='badge badge-warning' d-block>" + data.estado +
                                "</span>";
                            break;
                        case "PAGADA":
                            return "<span class='badge badge-danger' d-block>" + data.estado +
                                "</span>";
                            break;
                        case "ADELANTO":
                            return "<span class='badge badge-success' d-block>" + data.estado +
                                "</span>";
                            break;
                        default:
                            return "<span class='badge badge-success' d-block>" + data.estado +
                                "</span>";
                    }
                },
            },

        ],
        "language": {
                    "url": "{{asset('Spanish.json')}}"
        },
        "order": [[ 0, "desc" ]],
        

    });
    return false;
}
</script>
@endpush