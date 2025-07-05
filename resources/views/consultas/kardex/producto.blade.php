@extends('layout')

@section('content')
@section('kardex-active', 'active')
@section('producto_kardex-active', 'active')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
        <h2 style="text-transform:uppercase"><b>Kardex Producto</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Kardex Producto</strong>
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
                        <label for="producto_id">Producto</label>
                        <select name="producto_id" id="producto_id" class="select2_form form-control">
                            <option value=""></option>
                            @foreach($productos as $producto)
                                <option value="{{$producto->producto_id}}_{{$producto->color_id}}_{{$producto->talla_id}}">{{ $producto->producto_nombre.'-'.$producto->color_nombre.'-'.$producto->talla_nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label for="fecha_desde">Fecha desde</label>
                        <input type="date" id="fecha_desde" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label for="fecha_desde">Fecha hasta</label>
                        <input type="date" id="fecha_hasta" class="form-control" value="{{ date('Y-m-d') }}">
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
                                    <th class="text-center">FECHA</th>
                                    <th class="text-center">PRODUCTO</th>
                                    <th class="text-center">COLOR</th>
                                    <th class="text-center">TALLA</th>
                                    <th class="text-center">COMPRAS</th>
                                    <th class="text-center">INGRESOS</th>
                                    <th class="text-center">VENTAS</th>
                                    <th class="text-center">DEVOLUCIONES</th>
                                    <th class="text-center">SALIDAS</th>
                                    <th class="text-center">CANTIDAD</th>
                                    <th class="text-center">STOCK</th>
                                    <th class="text-center">ACCION</th>
                                    <th class="text-center">USUARIO/CLIENTE/PROVEEDOR</th>
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
<link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
<!-- DataTable -->
<link href="{{ asset('Inspinia/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>
<!-- DataTable -->
<script src="{{ asset('Inspinia/js/plugins/dataTables/datatables.min.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js') }}"></script>

<script>
    $(document).ready(function() {
        $(".select2_form").select2({
            placeholder: "SELECCIONAR",
            allowClear: true,
            height: '200px',
            width: '100%',
        });

        var kardex = [];
        // DataTables
        initTable();

        $('.dataTables-kardex').DataTable();

    });

    function initTable()
    {
        let verificar = true;
        var fecha_desde = $('#fecha_desde').val();
        var fecha_hasta = $('#fecha_hasta').val();
        var producto_id = $('#producto_id').val();
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
                        url : '{{ route('consultas.kardex.producto.getTable') }}',
                        data : {'_token' : $('input[name=_token]').val(), 'fecha_desde' : fecha_desde, 'fecha_hasta' : fecha_hasta, 'producto_id' : producto_id},
                        success: function(response) {
                            if (response.success) {
                                kardex = [];
                                kardex = response.kardex;
                                loadTable();
                                timerInterval = 0;
                                Swal.resumeTimer();
                                //console.log(colaboradores);
                            } else {
                                Swal.resumeTimer();
                                kardex = [];
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
        $('.dataTables-kardex').dataTable().fnDestroy();
        $('.dataTables-kardex').DataTable({
            "dom": '<"html5buttons"B>lTfgitp',
            "buttons": [{
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i> Excel',
                    titleAttr: 'Excel',
                    title: 'CONSULTA KARDEX CLIENTE'
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
                {
                    data: 'fecha',
                    className: "text-left"
                },
                {
                    data: 'producto',
                    className: "text-center"
                },
                {
                    data: 'color',
                    className: "text-center"
                },
                {
                    data: 'talla',
                    className: "text-center"
                },
                {
                    data: 'compras',
                    className: "text-center"
                },
                {
                    data: 'ingresos',
                    className: "text-center"
                },
                {
                    data: 'ventas',
                    className: "text-center"
                },
                {
                    data: 'devoluciones',
                    className: "text-center"
                },
                {
                    data: 'salidas',
                    className: "text-center"
                },
                {
                    data: 'cantidad',
                    className: "text-center"
                },
                {
                    data: 'stock',
                    className: "text-center"
                },
                {
                    data: 'accion',
                    className: "text-center"
                },
                {
                    data: 'descripcion',
                    className: "text-center"
                }
            ],
            "columnDefs": [{
                "targets": [1, 2, 3],
                "searchable": true,
                "orderable": true
            }],
            "language": {
                        "url": "{{asset('Spanish.json')}}"
            },
            "order": [[ 0, "desc" ]],


        });
        return false;
    }
</script>
@endpush
