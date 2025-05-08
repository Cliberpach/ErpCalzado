@extends('layout') 
@section('content')

@section('consulta-active', 'active')
@section('consulta-comprobantes-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
        <h2 style="text-transform:uppercase"><b>Listado de Documentos</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Documentos</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-12">
            <div class="row align-items-end">
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label for="tipo_id">Documento</label>
                        <select name="tipo_id" id="tipo_id" class="select2_form form-control">
                            <option value=""></option>
                            <option value="126">Ventas</option>
                            <option value="125">Fact., Boletas y Nota Crédito</option>
                            @foreach(comprobantes_empresa() as $doc)
                                <option value="{{$doc->tipo_comprobante}}">{{ $doc->descripcion }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label for="user_id">Usuario</label>
                        <select name="user_id" id="user_id" class="select2_form form-control">
                            <option value=""></option>
                            @foreach($users as $user)
                                <option value="{{$user->id}}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-12 col-md-2">
                    <div class="form-group">
                        <label for="fecha_desde">Fecha desde</label>
                        <input type="date" id="fecha_desde" class="form-control">
                    </div>
                </div>
                <div class="col-12 col-md-2">
                    <div class="form-group">
                        <label for="fecha_desde">Fecha hasta</label>
                        <input type="date" id="fecha_hasta" class="form-control">
                    </div>
                </div>
                <div class="col-12 col-md-1">
                    <div class="form-group">
                        <button class="btn btn-success btn-block" onclick="initTable()"><i class="fa fa-refresh"></i></button>
                    </div>
                </div>
                <div class="col-12 col-md-1">
                    <div class="form-group">
                        <button class="btn btn-primary btn-block" onclick="excelTable()"><i class="fa fa-file-excel-o"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table dataTables-documento table-striped table-bordered table-hover"
                            style="text-transform:uppercase">
                            <thead>
                                <tr>
                                    <th class="text-center">CLIENTE</th>
                                    <th class="text-center">TIPO DOC</th>
                                    <th class="text-center"># DOC</th>
                                    <th class="text-center">FECHA</th>
                                    <th class="text-center">MONTO</th>
                                    <th class="text-center">ESTADO</th>
                                    <th class="text-center">OPCIONES</th>
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

        var documentos = [];
        // DataTables
        //initTable();

        $('.dataTables-documento').DataTable({
            "language": {
                        "url": "{{asset('Spanish.json')}}"
            },
        });

    });

    function initTable()
    {
        let verificar = true;
        var fecha_desde = $('#fecha_desde').val();
        var fecha_hasta = $('#fecha_hasta').val();
        var tipo = $('#tipo_id').val();
        var user = $('#user_id').val();

        if (tipo == '') {
            verificar = false;
            toastr.error('Ingresar documento');
        }

        if (fecha_desde == '' && fecha_desde == null && fecha_hasta == '') {
            verificar = false;
            toastr.error('Ingresar fecha hasta');
        }

        if (fecha_hasta == '' && fecha_hasta == null && fecha_desde == '') {
            verificar = false;
            toastr.error('Ingresar fecha desde');
        }

        if (fecha_desde > fecha_hasta && fecha_hasta != '' && fecha_desde != '') {
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
                        url : '{{ route('consultas.documento.getTable') }}',
                        data : {'_token' : $('input[name=_token]').val(), 'fecha_desde' : fecha_desde, 'fecha_hasta' : fecha_hasta, 'tipo' : tipo,'user': user},
                        success: function(response) {
                            if (response.success) {
                                documentos = [];
                                documentos = response.documentos;
                                console.log(response)
                                loadTable();
                                timerInterval = 0;
                                Swal.resumeTimer();
                                //console.log(colaboradores);
                            } else {
                                toastr.error(response.mensaje)
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
        $('.dataTables-documento').dataTable().fnDestroy();
        $('.dataTables-documento').DataTable({
            "bPaginate": true,
            "bLengthChange": true,
            "bFilter": true,
            "bInfo": true,
            "bAutoWidth": false,
            "data": documentos,
            "columns": [
                {
                    data: 'cliente'
                },
                {
                    data: 'tipo_doc',
                    className: "text-center",
                },
                {
                    data: 'numero',
                    className: "text-center",
                },
                {
                    data: 'fecha',
                    className: "text-center",
                },
                {
                    data: 'total',
                    className: "text-center",
                },
                {
                    data: null,
                    className: "text-center",
                    render: function(data) {
                        switch (data.sunat) {
                            case "0":
                                return "<span class='badge badge-success' d-block>REGISTRADO</span>";
                                break;
                            case "1":
                                return "<span class='badge badge-primary' d-block>ENVIADO</span>";
                                break;
                            default:
                                return "<span class='badge badge-danger' d-block>NULO</span>";
                        }
                    },
                },
                {
                    data: null,
                    className: "text-center",
                    render: function(data) {
                        //Ruta Detalle
                        const url_venta = route("ventas.documento.comprobante", { id: data.id,size:100});

                        let url_nota = '{{ route("ventas.notas.show", ":id")}}';
                        url_nota = url_nota.replace(':id', data.id);

                        let url_convertir = '{{ route("consultas.documento.convertir", ":id")}}';
                        url_convertir = url_convertir.replace(':id', data.id);

                        let cadena = "";

                        if(data.tipo < 130)
                        {
                            cadena =  cadena + "<a class='btn btn-sm btn-info' href='"+ url_venta +"' target='_blank' title='Vista'><i class='fa fa-eye'></i> </a>";
                        }

                        if(data.tipo == 130)
                        {
                            cadena =  cadena + "<a class='btn btn-sm btn-info m-1' href='"+ url_nota +"' target='_blank' title='Vista'><i class='fa fa-eye'></i> </a>";
                        }

                        // if(data.tipo == 129 && (data.convertir == '' || data.convertir == null) && data.sunat != '2')
                        // {
                        //     cadena = cadena + "<a class='btn btn-sm btn-warning m-1' href='"+ url_convertir +"' title='Convertir'><i class='fa fa-exchange'></i> </a>"
                        // }

                        return cadena;
                    },
                },

            ],
            "language": {
                        "url": "{{asset('Spanish.json')}}"
            },
            "order": [[ 2, "desc" ]],
        });
        return false;
    }

    function excelTable()
    {
        let verificar = true;
        var fecha_desde = $('#fecha_desde').val();
        var fecha_hasta = $('#fecha_hasta').val();
        var tipo = $('#tipo_id').val();
        var user = $('#user_id').val();

        if (tipo == '') {
            verificar = false;
            toastr.error('Ingresar documento');
        }

        if (fecha_desde == '' && fecha_desde == null && fecha_hasta == '') {
            verificar = false;
            toastr.error('Ingresar fecha hasta');
        }

        if (fecha_hasta == '' && fecha_hasta == null && fecha_desde == '') {
            verificar = false;
            toastr.error('Ingresar fecha desde');
        }

        if (fecha_desde > fecha_hasta && fecha_hasta == '' && fecha_desde == '') {
            verificar = false;
            toastr.error('Fecha desde debe ser menor que fecha hasta');
        }

        if(verificar)
        {
            window.location = '/consultas/documentos/getDownload?fecha_desde='+fecha_desde+'&fecha_hasta='+fecha_hasta+'&tipo='+tipo+'&user='+user;
        }
        return false;
    }
</script>
@endpush
