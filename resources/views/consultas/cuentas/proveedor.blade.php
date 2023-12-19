@extends('layout') @section('content')

@section('consulta-active', 'active')
@section('cuenta_proveedor-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
        <h2 style="text-transform:uppercase"><b>Lista de Cuentas Proveedores</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Cuentas Proveedores</strong>
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
                        <table class="table dataTables-cuentas table-striped table-bordered table-hover"
                            style="text-transform:uppercase">
                            <thead>
                                <tr>
                                    <th class="text-center">PROVEEDOR</th>
                                    <th class="text-center">NUMERO</th>
                                    <th class="text-center">FECHADOC</th>
                                    <th class="text-center">MONTO</th>
                                    <th class="text-center">ACTA</th>
                                    <th class="text-center">SALDO</th>
                                    <th class="text-center">ESTADO</th>
                                </tr>
                            </thead>
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
<link href="{{ asset('Inspinia/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<!-- DataTable -->
<script src="{{ asset('Inspinia/js/plugins/dataTables/datatables.min.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js') }}"></script>

<script>
    $(document).ready(function() {
        var cuentas = [];
        // DataTables
        initTable();

        $('.dataTables-cuentas').DataTable();

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
                        url : '{{ route('consultas.cuentas.proveedor.getTable') }}',
                        data : {'_token' : $('input[name=_token]').val(), 'fecha_desde' : fecha_desde, 'fecha_hasta' : fecha_hasta},
                        success: function(response) {
                            if (response.success) {
                                cuentas = [];
                                cuentas = response.cuentas;
                                loadTable();
                                timerInterval = 0;
                                Swal.resumeTimer();
                                //console.log(colaboradores);
                            } else {
                                Swal.resumeTimer();
                                cuentas = [];
                                loadTable();
                                toastr.error(response.mensaje)
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
        $('.dataTables-cuentas').dataTable().fnDestroy();
        $('.dataTables-cuentas').DataTable({
            "dom": '<"html5buttons"B>lTfgitp',
            "buttons": [{
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i> Excel',
                    titleAttr: 'Excel',
                    title: 'CONSULTA CUENTAS PROVEEDOR'
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
            "data": cuentas,
            "columns": [
                {
                    data: 'proveedor',
                    className: "text-left"
                },
                {
                    data: 'numero_doc',
                    className: "text-left"
                },
                {
                    data: 'fecha_doc',
                    className: "text-left"
                },
                {
                    data: 'monto',
                    className: "text-center"
                },
                {
                    data: 'acta',
                    className: "text-center"
                },
                {
                    data: 'saldo',
                    className: "text-center"
                },
                {
                    data: 'estado',
                    className: "text-center"
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
