@extends('layout') @section('content')
@section('reporte-active', 'active')
@section('cuentas_x_pagar_reporte-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
        <h2 style="text-transform:uppercase"><b>Listado de Cuentas Proveedor</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Cuentas Proveedor</strong>
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
                        <label for="proveedor_id">Proveedor</label>
                        <select name="proveedor_id" id="proveedor_id" class="select2_form form-control">
                            <option value=""></option>
                            @foreach(proveedores() as $proveedor)
                                <option value="{{$proveedor->id}}">{{ $proveedor->tipoDocumento() }} - {{ $proveedor->descripcion }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label for="fecha_desde">Fecha de inicio</label>
                        <input type="date" id="fecha_ini" class="form-control">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label for="fecha_desde">Fecha final</label>
                        <input type="date" id="fecha_fin" class="form-control">
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
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Cuentas</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table dataTables-reporte table-striped table-bordered table-hover"
                            style="text-transform:uppercase">
                            <thead>
                                <tr>
                                    <th class="text-center">FECHA</th>
                                    <th class="text-center"># DOC</th>
                                    <th class="text-center">PROVEEDOR</th>
                                    <th class="text-center">MONEDA</th>
                                    <th class="text-center">MODO PAGO</th>
                                    <th class="text-center">MONTO</th>
                                    <th class="text-center">ACTA</th>
                                    <th class="text-center">SALDO</th>
                                    <th class="text-center">ULTIMA FECHA PAGO</th>
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
<style>
    @media (min-width: 992px){
        .modal-lg {
            max-width: 1200px;
        }
    }

    #table_reporte div.dataTables_wrapper div.dataTables_filter{
        text-align: left !important;
    }
    #table_reporte tr[data-href] {
        cursor: pointer;
    }
    #table_reporte tbody .fila_lote.selected {
        /* color: #151515 !important;*/
        font-weight: 400;
        color: white !important;
        background-color: #18a689 !important;
        /* background-color: #CFCFCF !important; */
    }
</style>
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

        $('.dataTables-reporte').DataTable({
            "language": {
                "url": "{{asset('Spanish.json')}}"
            },
        });

    });

    function initTable()
    {
        let verificar = true;
        var fecha_ini = $('#fecha_ini').val();
        var fecha_fin = $('#fecha_fin').val();
        var proveedor_id = $('#proveedor_id').val();

        if (fecha_ini != '' && fecha_ini != null && fecha_fin == '') {
            verificar = false;
            toastr.error('Ingresar fecha final');
        }

        if (fecha_fin != '' && fecha_fin != null && fecha_ini == '') {
            verificar = false;
            toastr.error('Ingresar fecha de inicio');
        }

        if (fecha_ini > fecha_fin && fecha_fin != '' && fecha_ini !== '') {
            verificar = false;
            toastr.error('Fecha desde debe ser menor que fecha hasta');
        }

        if(verificar)
        {
            loadTable(proveedor_id,fecha_ini,fecha_fin)
        }
        return false;
    }

    function loadTable(proveedor_id,fecha_ini,fecha_fin)
    {
        //ELIMINAR EL DATATABLE PARA VOLVER A INSTANCIARLO
        $(".dataTables-reporte").dataTable().fnDestroy();
        $('.dataTables-reporte').DataTable({
            "dom":
                "<'row'<'col-sm-12 col-md-12 col-lg-12'f>>" +
                "<'row'<'col-sm-12'tr>>"+
                "<'row justify-content-between'<'col information-content p-0'i><''p>>",
            "bPaginate": true,
            "serverSide":true,
            "processing":true,
            "bLengthChange": true,
            "bFilter": true,
            "order": [],
            "bInfo": true,
            'bAutoWidth': false,
            "ajax": {
                "url": "{{route('reporte.cuentas.proveedor.getTable')}}",
                "type": "POST",
                "data": {'_token' : $('input[name=_token]').val(), 'fecha_ini' : fecha_ini, 'fecha_fin' : fecha_fin, 'proveedor_id' : proveedor_id}
            },
            "columns": [
                {
                    data: 'fecha',
                    className: "text-left",
                    name:"compra_documentos.fecha_emision"
                },
                {
                    data: 'documento',
                    className: "text-center",
                    name:"compra_documentos.numero_doc"
                },
                {
                    data: 'proveedor',
                    className: "text-left",
                    name:"proveedores.descripcion"
                },
                {
                    data: 'moneda',
                    className: "text-center",
                    name:"compra_documentos.moneda"
                },
                {
                    data: 'modo_pago',
                    className: "text-center",
                    name:"compra_documentos.modo_compra"
                },
                {
                    data: 'monto',
                    className: "text-center",
                    name:"compra_documentos.total"
                },
                {
                    data: null,
                    className: "text-center",
                    searchable:false,
                    render: function(data) {
                        return data.acta;
                    }
                },
                {
                    data: 'saldo',
                    className: "text-center",
                    name:"cuenta_proveedor.saldo"
                },
                {
                    data: null,
                    className: "text-center",
                    searchable:false,
                    render: function(data) {
                        return data.fecha_ultima;
                    }
                },
                {
                    data: null,
                    className: "text-center",
                    searchable:false,
                    render: function(data) {
                        var html = "<div class='btn-group'><a class='btn btn-danger btn-sm' href='#'  onclick='reportePdf("+data.id+")' title='Pdf'><i class='fa fa-file-pdf-o'></i></a><a class='btn btn-primary btn-sm d-none' href='#'  title='Excel'><i class='fa fa-file-excel-o'></i></a></div>";
                        return html;
                    }
                }

            ],
            "language": {
                "url": "{{ asset('Spanish.json') }}"
            }
        });
        return false;
    }

    function reportePdf(id) {
        var url = '{{ route("cuentaProveedor.reporte", ":id")}}';
        url = url.replace(':id', id);
        window.open(url, "Cuenta proveedor SISCOM", "width=900, height=600")
    }

    function excelTable()
    {
        let verificar = true;
        var fecha_ini = $('#fecha_ini').val();
        var fecha_fin = $('#fecha_fin').val();
        var proveedor_id = $('#proveedor_id').val();

        if (fecha_ini != '' && fecha_ini != null && fecha_fin == '') {
            verificar = false;
            toastr.error('Ingresar fecha final');
        }

        if (fecha_fin != '' && fecha_fin != null && fecha_ini == '') {
            verificar = false;
            toastr.error('Ingresar fecha de inicio');
        }

        if (fecha_ini > fecha_fin && fecha_fin != '' && fecha_ini !== '') {
            verificar = false;
            toastr.error('Fecha desde debe ser menor que fecha hasta');
        }

        if(verificar)
        {
            window.location = '/reportes/cuentas/proveedor/getExcel?fecha_ini='+fecha_ini+'&fecha_fin='+fecha_fin+'&proveedor_id='+proveedor_id;
        }
        return false;
    }
</script>
@endpush
