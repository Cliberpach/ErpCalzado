@extends('layout') @section('content')
    {{-- @include('pos.caja_chica.edit') --}}
@section('cuentas-active', 'active')
@section('cuenta-cliente-active', 'active')
@include('ventas.cuentaCliente.modalDetalle')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
        <h2 style="text-transform:uppercase"><b>Lista de Cuentas Clientes</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Cuentas Clientes</strong>
            </li>

        </ol>
    </div>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-md-12">
            <div class="ibox">
                <div class="ibox-content">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="" class="required">Cliente</label>
                                <select name="cliente_b" id="cliente_b" class="select2_form form-control">
                                    <option value=""></option>
                                    @foreach (clientes() as $cliente)
                                        <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="" class="required">Estado</label>
                                <select name="estado_b" id="estado_b" class="select2_form form-control">
                                    <option value=""></option>
                                    <option value="PENDIENTE">PENDIENTES</option>
                                    <option value="PAGADO">PAGADOS</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <button class="btn btn-primary btn-block" id="btn_buscar" type="button"><i
                                    class="fa fa-search"></i> Buscar</button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <button class="btn btn-danger btn-block" id="btn_pdf" type="button"><i
                                    class="fa fa-file-pdf-o"></i> PDF</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table dataTables-cajas table-striped table-bordered table-hover"
                            style="text-transform:uppercase">
                            <thead>
                                <tr>
                                    <th class="text-center">CLIENTE</th>
                                    <th class="text-center">NUMERO</th>
                                    <th class="text-center">FECHADOC</th>
                                    <th class="text-center">MONTO</th>
                                    <th class="text-center">ACTA</th>
                                    <th class="text-center">SALDO</th>
                                    <th class="text-center">ESTADO</th>
                                    <th class="text-center">ACCIONES</th>
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
<link href="{{ asset('Inspinia/css/plugins/textSpinners/spinners.css') }}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
<style>
    .my-swal {
        z-index: 3000 !important;
    }

    @media (min-width: 768px) {
        .modal-xl {
            width: 90%;
            max-width: 1200px;
        }
    }

</style>
@endpush
@push('scripts')
<!-- DataTable -->
<script src="{{ asset('Inspinia/js/plugins/dataTables/datatables.min.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.1.2/axios.min.js"></script>
<script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>
<script>
    $(".select2_form").select2({
        placeholder: "SELECCIONAR",
        allowClear: true,
        height: '200px',
        width: '100%',
    });
    $('.dataTables-cajas').DataTable({
        "dom": '<"html5buttons"B>lTfgitp',
        "buttons": [{
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i> Excel',
                titleAttr: 'Excel',
                title: 'Tablas Generales'
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
        "processing": true,
        "columnDefs": [{
                targets: 7,
                className: "text-center",
                render: function(data, type, row) {
                    return "<button data-id='" + row[8] +
                        "' class='btn btn-primary btn-sm btn-detalle' ><i class='fa fa-list'></i> detalles</button>"
                }
            }

        ],
        "language": {
            "url": "{{ asset('Spanish.json') }}"
        },
        "order": [
            [0, "desc"]
        ],
    });
    //-----------------------------
    axios.get("{{ route('cuentaCliente.getTable') }}").then((value) => {
        var detalle = value.data.data;
        var table = $(".dataTables-cajas").DataTable();
        table.clear().draw();
        detalle.forEach((value, index, array) => {
            table.row.add([
                value.cliente,
                value.numero_doc,
                value.fecha_doc,
                value.monto,
                value.acta,
                value.saldo,
                value.estado,
                '',
                value.id
            ]).draw(false);
        })
    }).catch((value) => {

    })
    //------------------------------
    $('.dataTables-detalle').DataTable({
        "bPaginate": false,
        "bLengthChange": true,
        "bFilter": true,
        "bInfo": false,
        "bAutoWidth": false,
        "processing": true,
        "language": {
            "url": "{{ asset('Spanish.json') }}"
        },
        "order": [
            [0, "desc"]
        ],
        'aoColumns': [
                {
                    sClass: 'text-center'
                },
                {
                    sClass: 'text-center'
                },
                {
                    sClass: 'text-center'
                },
                {
                    sClass: 'text-center'
                }
            ],
    });

    $("#btn_buscar").on('click', function() {
        var cliente = $("#cliente_b").val();
        var estado = $("#estado_b").val();

        axios.get("{{ route('cuentaCliente.consulta') }}", {
            params: {
                cliente: cliente,
                estado: estado
            }
        }).then((value) => {
            var detalle = value.data;

            var table = $(".dataTables-cajas").DataTable();
            table.clear().draw();
            detalle.forEach((value, index, array) => {
                table.row.add([
                    value.cliente,
                    value.numero_doc,
                    value.fecha_doc,
                    value.monto,
                    value.acta,
                    value.saldo,
                    value.estado,
                    '',
                    value.id
                ]).draw(false);
            })
        }).catch((value) => {

        })

    });

    $("#btn_pdf").on('click', function() {
        var cliente = $("#cliente_b").val();
        var estado = $("#estado_b").val();

        let enviar = true;

        if(cliente == null || cliente == '')
        {
            toastr.error("Seleccionar cliente","Error")
            enviar = false;
        }

        if(estado == null || estado == '')
        {
            toastr.error("Seleccionar estado","Error")
            enviar = false;
        }

        if(enviar)
        {
            var url_open_pdf = '/cuentaCliente/detalle?id='+cliente+'&estado='+estado;
            window.open(url_open_pdf,'Informe SISCOM','location=1, status=1, scrollbars=1,width=900, height=600');
        }
    });

    $(document).on('click', '.btn-detalle', function(e) {
        var id = $(this).data('id');
        axios.get("{{ route('cuentaCliente.getDatos') }}", {
            params: {
                id: id
            }
        }).then((value) => {

            var datos = value.data;
            var detalle = datos.detalle;
            $("#modal_detalle #cuenta_cliente_id").val(id)
            $("#modal_detalle #cliente").val(datos.cliente)
            $("#modal_detalle #numero").val(datos.numero)
            $("#modal_detalle #monto").val(datos.monto)
            $("#modal_detalle #saldo").val(datos.saldo)
            $("#modal_detalle #estado").val(datos.estado)
            $("#modal_detalle").modal("show");
            $("#btn-detalle").attr('href','/cuentaCliente/reporte/'+id)
            $("#frmDetalle").attr('action','/cuentaCliente/detallePago/'+id)
            var table = $(".dataTables-detalle").DataTable();
            table.clear().draw();
            detalle.forEach((value, index, array) => {
                if(value.ruta_imagen)
                {
                    table.row.add([
                        value.fecha,
                        value.observacion,
                        value.monto,
                        '<a class="btn btn-primary btn-xs" href="/cuentaCliente/imagen/'+value.id+'"><i class="fa fa-download"></i></a>'
                    ]).draw(false);
                }
                else
                {
                    table.row.add([
                        value.fecha,
                        value.observacion,
                        value.monto,
                        '-'
                    ]).draw(false);
                }
            })
        }).catch((value) => {

        })

    });
</script>
@endpush
