@extends('layout')
@section('content')

@section('cuentas-active', 'active')
@section('cuenta-cliente-active', 'active')

@include('ventas.cuentaCliente.modals.mdl_pagar')
@include('ventas.cuentaCliente.modals.mdl_generar_comprobante')

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
                                <label for="" class="required" style="font-weight: bold;">Cliente</label>
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
                                <label for="" class="required" style="font-weight: bold;">Estado</label>
                                <select name="estado_b" id="estado_b" class="select2_form form-control">
                                    <option value=""></option>
                                    <option selected value="PENDIENTE">PENDIENTES</option>
                                    <option value="PAGADO">PAGADOS</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <button class="btn btn-success btn-block" id="btn_buscar" type="button"><i
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
                        @include('ventas.cuentaCliente.tables.tbl_list_cuentas')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@push('styles')
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
<script>
    let dtCuentasCliente    = null;

    document.addEventListener('DOMContentLoaded', () => {
        iniciarDtCuentasCliente();
        iniciarClienteSelect();
        events();
        setDatosDefault();
    })

    function events() {
        eventsMdlPagar();
        eventsGenerarComprobante();
        iniciarSelectsMdlPagar();
    }

    function iniciarClienteSelect() {
        window.clienteSelect = new TomSelect('#cliente', {
            valueField: 'id',
            labelField: 'text',
            searchField: 'text',
            load: function(query, callback) {
                if (!query.length || query.length < 2) {
                    return callback(); // mínimo 2 caracteres
                }
                fetch("{{ route('utilidades.getClientes') }}?search=" + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const clientes = data.clientes.map(item => ({
                                id: item.id,
                                text: item.descripcion
                            }));
                            callback(clientes);
                        } else {
                            toastr.error(data.message, 'ERROR EN EL SERVIDOR');
                            callback();
                        }
                    })
                    .catch(() => {
                        toastr.error("Error al obtener clientes", "ERROR");
                        callback();
                    });
            },
            render: {
                option: function(item, escape) {
                    return `<div>${escape(item.text)}</div>`;
                },
                item: function(item, escape) {
                    return `<div>${escape(item.text)}</div>`;
                },
                no_results: function(data, escape) {
                    return `<div class="no-results">No se encontraron clientes</div>`;
                },
                loading: function(data, escape) {
                    return `<div><i class="fa fa-spinner fa-spin text-primary"></i> Buscando...</div>`;
                }
            },
        });
    }

    function iniciarDtCuentasCliente() {
        dtCuentasCliente = $('.dataTables-cajas').DataTable({
            processing: true,
            serverSide: true,
            bPaginate: true,
            bLengthChange: true,
            bFilter: true,
            bInfo: true,
            bAutoWidth: false,
            ajax: {
                url: "{{ route('cuentaCliente.getTable') }}",
                data: function(d) {
                    d.cliente = $("#cliente_b").val();
                    d.estado = $("#estado_b").val();
                }
            },
            columns: [{
                    data: 'id',
                    name: 'cc.id',
                    visible: false
                },
                {
                    data: 'cliente',
                    name: 'cd.cliente'
                },
                {
                    data: 'numero_doc',
                    name: 'cc.numero_doc'
                },
                {
                    data: 'fecha_doc',
                    name: 'cc.fecha_doc'
                },
                {
                    searchable: false,
                    data: 'monto',
                    name: 'monto'
                },
                {
                    data: 'acta',
                    name: 'cc.acta'
                },
                {
                    searchable: false,
                    data: 'saldo',
                    name: 'saldo'
                },
                {
                    searchable: false,
                    data: 'estado',
                    name: 'estado',
                    render: function(data, type, row) {
                        if (data === 'PAGADO') {
                            return '<span class="badge badge-success">PAGADO</span>';
                        } else if (data === 'PENDIENTE') {
                            return '<span class="badge badge-danger">PENDIENTE</span>';
                        } else {
                            return data;
                        }
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: "text-center",
                    render: function(data, type, row) {
                        return `<button data-id='${row.id}' onclick="openMdlPagar(${row.id})" class='btn btn-success btn-sm btn-detalle'>
                    <i class='fa fa-list'></i>
                </button>`;
                    }
                }

            ],
            language: {
                url: "{{ asset('Spanish.json') }}"
            },
            order: [
                [0, "desc"]
            ]
        });
    }

    $(".select2_form").select2({
        placeholder: "SELECCIONAR",
        allowClear: true,
        height: '200px',
        width: '100%',
    });

    $("#btn_buscar").on('click', function() {
        $('.dataTables-cajas').DataTable().ajax.reload();
    });

    $("#btn_pdf").on('click', function() {
        var cliente = $("#cliente_b").val();
        var estado = $("#estado_b").val();

        let enviar = true;

        if (cliente == null || cliente == '') {
            toastr.error("Seleccionar cliente", "Error")
            enviar = false;
        }

        if (estado == null || estado == '') {
            toastr.error("Seleccionar estado", "Error")
            enviar = false;
        }

        if (enviar) {
            var url_open_pdf = '/cuentaCliente/detalle?id=' + cliente + '&estado=' + estado;
            window.open(url_open_pdf, 'Informe SISCOM',
                'location=1, status=1, scrollbars=1,width=900, height=600');
        }
    });

    function setDatosDefault() {
        window.modoPagoSelect.setValue(3);
    }
</script>
@endpush
