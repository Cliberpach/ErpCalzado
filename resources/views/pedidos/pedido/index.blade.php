@extends('layout')
@section('content')

@section('pedidos-active', 'active')
@section('pedido-active', 'active')
@include('pedidos.pedido.modal-historial-atenciones')
@include('pedidos.pedido.modal-pedido-detalles')


<div class="row wrapper border-bottom white-bg page-heading align-items-center" style="padding:10px 5px;">
    <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
        <h2 style="text-transform:uppercase"><b>Listado de Pedidos</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Pedidos</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-3 mt-3 d-flex align-items-center justify-content-center">
        <div class="alert alert-warning alert-dismissible fade show m-0" role="alert">
            <strong>Holy {{ Auth::user()->usuario }}!</strong> Los pedidos facturados no podrán modificarse.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 d-flex justify-content-end">
        <button id="btn_añadir_cotizacion" class="btn btn-primary" onclick="añadirPedido()">
            <i class="fa fa-plus-square"></i> Añadir nuevo
        </button>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row mb-3">
        <div class="col-9">
            <div class="row">
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-12 col-xs-12">
                    <label for="fecha_inicio" style="font-weight: bold;">Fecha desde:</label>
                    <input type="date" class="form-control" id="filtroFechaInicio"
                        value="{{ old('fecha_inicio', now()->format('Y-m-d')) }}"
                        onchange="filtrarDespachoFechaInic(this.value)">
                </div>
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-12 col-xs-12">
                    <label for="fecha_fin" style="font-weight: bold;">Fecha hasta:</label>
                    <input type="date" class="form-control" id="filtroFechaFin" value="{{ now()->format('Y-m-d') }}"
                        onchange="filtrarDespachoFechaFin(this.value)">
                </div>
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-12 col-xs-12">
                    <label for="pedido_estado" style="font-weight: bold;">Estado</label>
                    <select id="pedido_estado" class="form-control select2_form"
                        onchange="filtrarDespachosEstado(this.value)">
                        <option value=""></option>
                        <option value="PENDIENTE">PENDIENTE</option>
                        <option value="ATENDIENDO">ATENDIENDO</option>
                        <option value="FINALIZADO">FINALIZADO</option>
                    </select>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-12 col-xs-12">
                    <label for="filtroCliente" style="font-weight: bold;">CLIENTE:</label>
                    <select class="select2_form" style="text-transform: uppercase; width:100%" name="filtroCliente"
                        id="filtroCliente" required onchange="pedidos_data_table.ajax.reload();">
                    </select>
                </div>

            </div>
        </div>
        <div class="col-3 d-flex align-items-end justify-content-end">

        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="pedidos_table"
                            style="text-transform:uppercase" width="100%">
                            <thead>
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">FACTURADO</th>
                                    <th class="text-center">COT</th>
                                    <th class="text-center">ALMACEN</th>
                                    <th class="text-center">CLIENTE</th>
                                    <th class="text-center">FECHA</th>
                                    <th class="text-center">TOTAL</th>
                                    <th class="text-center">USUARIO</th>
                                    <th class="text-center">ESTADO</th>
                                    <th class="text-center">ACCIONES</th>
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
<style>
    .search-length-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .buttons-container {
        display: flex;
        justify-content: end;
    }


    .custom-button {
        background-color: #ffffff !important;
        color: #000000 !important;
        border: 1px solid #dcdcdc !important;
        border-radius: 5px;
        padding: 8px 16px;
        font-size: 14px;
        margin: 8px 0px !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: background-color 3s, color 3s;
    }

    .custom-button:hover {
        background-color: #d7e9fb !important;
        color: #000000 !important;
        border-color: #d7e9fb !important;
    }

    .dropdown-menu {
        max-height: 140px;
        overflow-y: auto;
    }
</style>
@endpush

@push('scripts')

<script>
    //======== DATATABLES ====
    let pedidos_data_table = null;
    //====== DATATABLE PEDIDO DETALLES ========
    let detalles_data_table = null;
    //===== DATATABLE ATENCIONES =======
    let atenciones_data_table = null;

    document.addEventListener('DOMContentLoaded', () => {
        sessionMessages();
        loadSelect2();
        loadDataTable();

        eventsModalAtenciones();
    })

    function sessionMessages() {
        let existe = @json(Session::has('pedido_error'));
        if (existe) {
            toastr.error(@json(Session::get('pedido_error')), 'NO SE PUEDE MODIFICAR ESTE PEDIDO');
        }
    }


    function loadSelect2() {
        $(".select2_form").select2({
            placeholder: "SELECCIONAR",
            allowClear: true,
            height: '200px',
            width: '100%',
            minimumResultsForSearch: -1
        });

        $('#filtroCliente').select2({
            width: '100%',
            placeholder: "Buscar Cliente...",
            allowClear: true,
            language: {
                inputTooShort: function(args) {
                    var min = args.minimum;
                    return "Por favor, ingrese " + min + " o más caracteres";
                },
                searching: function() {
                    return "BUSCANDO...";
                },
                noResults: function() {
                    return "No se encontraron clientes";
                }
            },
            ajax: {
                url: '{{ route('utilidades.getClientes') }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function(data, params) {
                    if (data.success) {
                        params.page = params.page || 1;
                        const clientes = data.clientes;
                        return {
                            results: clientes.map(item => ({
                                id: item.id,
                                text: item.descripcion
                            })),
                            pagination: {
                                more: data.more
                            }
                        };
                    } else {
                        toastr.error(data.message, 'ERROR EN EL SERVIDOR');
                        return {
                            results: []
                        }
                    }

                },
                cache: true
            },
            minimumInputLength: 2,
            templateResult: function(data) {
                if (data.loading) {
                    return $(
                        '<span><i style="color:blue;" class="fa fa-spinner fa-spin"></i> Buscando...</span>'
                    );
                }
                return data.text;
            },
        });
    }

    function loadDataTable() {
        const getPedidosUrl = "{{ route('pedidos.pedido.getTable') }}";

        pedidos_data_table = new DataTable('#pedidos_table', {
            serverSide: true,
            ajax: {
                url: getPedidosUrl,
                type: 'GET',
                "data": function(d) {
                    d.fecha_inicio = $('#filtroFechaInicio').val();
                    d.fecha_fin = $('#filtroFechaFin').val();
                    d.pedido_estado = $('#pedido_estado').val();
                    d.cliente_id = $('#filtroCliente').val();
                }
            },
            "order": [
                [0, 'desc']
            ],

            buttons: [{
                    text: '<a id="btn-excel-pedidos" href="javascript:void(0);"><i class="fa fa-file-excel-o"></i> Excel</a>',
                    className: 'custom-button btn-hola',
                    action: function(e, dt, node, config) {
                        excelPedidos();
                    }
                },
                {
                    extend: 'pdf',
                    className: 'custom-button btn-check',
                    text: '<i class="fas fa-file-pdf"></i> Pdf',
                    title: 'Pedidos'
                },
                {
                    extend: 'print',
                    className: 'custom-button btn-check',
                    text: '<i class="fa fa-print"></i> Imprimir',
                    title: 'Pedidos'
                }
            ],
            dom: '<"buttons-container"B><"search-length-container"lf>tp',
            bProcessing: true,
            columns: [
                {
                    data: 'id',
                    searchable: true
                },
                {
                    data: 'documento_venta',
                    searchable: true
                },
                {
                    data: 'cotizacion_nro',
                    searchable: true
                },
                {
                    data: 'almacen_nombre',
                    searchable: false
                },
                {
                    data: 'cliente_nombre',
                    name: 'pedidos.cliente_nombre',
                    searchable: true
                },
                {
                    data: 'created_at',
                    searchable: false,
                    render: function(data, type, row) {
                        const date = new Date(data);
                        const formattedDate = date.getFullYear() + '-' +
                            ('0' + (date.getMonth() + 1)).slice(-2) + '-' +
                            ('0' + date.getDate()).slice(-2) + ' ' +
                            ('0' + date.getHours()).slice(-2) + ':' +
                            ('0' + date.getMinutes()).slice(-2) + ':' +
                            ('0' + date.getSeconds()).slice(-2);
                        return formattedDate;
                    }
                },
                {
                    data: 'total_pagar',
                    searchable: false
                },
                {
                    data: 'user_nombre',
                    searchable: true
                },
                {
                    data: 'estado',
                    searchable: false,
                    className: "text-center",
                    render: function(data, type, row) {
                        if (data === 'PENDIENTE') {
                            return '<span class="badge badge-danger">PENDIENTE</span>';
                        } else if (data === 'FINALIZADO') {
                            return '<span class="badge badge-primary">FINALIZADO</span>';
                        } else if (data === "ATENDIENDO") {
                            return '<span class="badge badge-success">ATENDIENDO</span>';
                        }
                    }
                },
                {
                    data: null,
                    searchable: false,
                    className: "text-center",
                    render: function(data, type, row) {
                        let url_reporte = '{{ route('pedidos.pedido.reporte', ':id') }}';
                        url_reporte = url_reporte.replace(':id', row.id);

                        const url_atender = '{{ route('pedidos.pedido.atender') }}';

                        let accion_facturar = '';

                        if (!row.facturado && row.estado === 'PENDIENTE') {
                            accion_facturar += `<li><a class='dropdown-item'  onclick="facturar(${row.id})" title='Facturar'><b><i class="fas fa-file-invoice-dollar"></i> Facturar</a></b></li>
                                    <div class="dropdown-divider"></div>`;
                        }

                        let acciones =
                            `<div class="btn-group" style="text-transform:capitalize;">
                                <button data-toggle='dropdown' class='btn btn-primary btn-sm  dropdown-toggle'><i class="fas fa-bars"></i></button>
                                <ul class='dropdown-menu dropdown-menu-up'>
                                    ${accion_facturar}
                                    <li><a class='dropdown-item'  target='_blank' href="${url_reporte}" title='PDF'><b><i class='fa fa-file-pdf-o'></i> Pdf</a></b></li>`;



                        //========= SOLO PUEDEN ELIMINARSE O EDITARSE PEDIDOS NO FACTURADOS,NO FINALIZADOS =====
                        if (row.estado !== "FINALIZADO" && !row.documento_venta_facturacion_id) {
                            acciones +=
                                `<li><a class='dropdown-item' onclick="modificarPedido(${row.id})" href="javascript:void(0);" title='Modificar' ><b><i class='fa fa-edit'></i> Modificar</a></b></li>`;
                        }

                        if (row.estado !== 'FINALIZADO') {
                            acciones +=
                                `<li><a class='dropdown-item' onclick="eliminarPedido(${row.id})"  title='FINALIZAR'><b><i class='fa fa-trash'></i> Finalizar</a></b></li>`;
                        }

                        acciones += `<li><a class='dropdown-item' data-toggle="modal" data-pedido-id="${row.id}" data-target="#modal_pedido_detalles"  title='Detalles'><b><i class="fas fa-info-circle"></i> Detalles</a></b></li>
                                <div class="dropdown-divider"></div>`;



                        if (row.estado === "ATENDIENDO" || row.estado === "PENDIENTE") {
                            acciones += ` <li>
                                    <form id="formAtenderPedido_${row.id}" method="POST" action="${url_atender}">
                                        @csrf
                                        <input hidden name="pedido_id" value="${row.id}"></input>
                                        <a class='dropdown-item' onclick="atenderPedido(${row.id})"  title='Atender'><b><i class="fas fa-concierge-bell"></i> Atender</a></b>
                                    </form>
                                </li> `;
                        }

                        let optionReciboCaja = ``;
                        if (!row.facturado && row.estado === 'PENDIENTE') {
                            optionReciboCaja +=
                                `<li><a class='dropdown-item' href="javascript:void(0);" onclick="generarRecibo(${row.id})"  title='Recibo'><b><i class="fas fa-receipt"></i> Generar Recibo</a></b></li>`;
                        }

                        acciones += `<li><a class='dropdown-item' data-toggle="modal" data-pedido-id="${row.id}" data-target="#modal_historial_atenciones"  title='Historial'><b><i class="fas fa-history"></i> Historial Atenciones</a></b></li>
                                        ${optionReciboCaja}</ul></div>`;



                        return acciones;
                    }
                }
            ],
            language: getLanguajeDataTable(),
            initComplete: function() {
                $('#pedidos_table input[type=search]').attr('placeholder', 'Buscar nro,facturado,cot');
            },

        })

        $('label[for="dt-search-0"]').css({
            'color': '#3f51b5',
            'font-weight': 'bold'
        });


        document.querySelector('.dt-buttons').classList.add('btn-group');
    }


    function getLanguajeDataTable() {
        const userIsAdmin = @json($isAdmin);
        return {
            processing: "Procesando...",
            search: userIsAdmin ?
                "Buscar nro,facturado,cot,cliente,usuario: " : "Buscar nro,facturado,cot,cliente: ",
            lengthMenu: "MOSTRAR _MENU_ PEDIDOS",
            info: "MOSTRANDO _START_ A _END_ DE _TOTAL_ PEDIDOS",
            infoEmpty: "MOSTRANDO 0 PEDIDOS",
            infoFiltered: "(FILTRADO de _MAX_ PEDIDOS)",
            infoPostFix: "",
            loadingRecords: "CARGANDO...",
            zeroRecords: "No se encontraron elementos",
            emptyTable: "NO HAY PEDIDOS DISPONIBLES",
            paginate: {
                first: "PRIMERO",
                previous: "ANTERIOR",
                next: "SIGUIENTE",
                last: "ÚLTIMO"
            },
            aria: {
                sortAscending: ": activar para ordenar la columna de forma ascendente",
                sortDescending: ": activar para ordenar la columna de forma descendente"
            }
        }
    }

    function añadirPedido() {
        window.location = "{{ route('pedidos.pedido.create') }}";
    }

    async function modificarPedido(pedido_id) {

        //======== VALIDAR ESTADO DEL PEDIDO ======
        const pedido = pedidos_data_table.rows().data().filter(function(value, index) {
            return value['id'] == pedido_id;
        });

        if (pedido.length > 0) {
            const estado = pedido[0].estado;

            if (estado === "FINALIZADO") {
                toastr.error('EL PEDIDO NO PUEDE SER MODIFICADO', 'PEDIDO FINALIZADO');
                return;
            }

            if (estado === "ANULADO") {
                toastr.error('EL PEDIDO NO PUEDE SER MODIFICADO', 'PEDIDO ANULADO');
                return;
            }

            window.location = `{{ route('pedidos.pedido.edit', ['id' => ':id']) }}`.replace(':id', pedido_id);

        } else {
            toastr.error('ERROR EN EL ID DEL PEDIDO', 'PEDIDO NO ENCONTRADO');
        }

    }

    function reportePedido(pedido_id) {
        window.location = `{{ route('pedidos.pedido.reporte', ['id' => ':id']) }}`.replace(':id', pedido_id);
    }

    function atenderPedido(pedido_id) {
        Swal.fire({
            title: "DESEA ATENDER EL PEDIDO?",
            text: "Se separará el stock disponible!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "SÍ, atender el pedido!",
            cancelButtonText: "No, cancelar!",
        }).then((result) => {
            if (result.isConfirmed) {

                //======== VALIDAR ESTADO DEL PEDIDO ======
                const pedido = pedidos_data_table.rows().data().filter(function(value, index) {
                    return value['id'] == pedido_id;
                });

                if (pedido.length > 0) {
                    const estado = pedido[0].estado;

                    if (estado === "FINALIZADO") {
                        toastr.error('EL PEDIDO NO PUEDE SER ATENDIDO', 'PEDIDO FINALIZADO');
                        return;
                    }

                    if (estado === "ANULADO") {
                        toastr.error('EL PEDIDO NO PUEDE SER ATENDIDO', 'PEDIDO ANULADO');
                        return;
                    }

                    if (estado === "PENDIENTE" || estado === "ATENDIENDO") {
                        //======== ATENDER EN EL CONTROLLER ========
                        document.querySelector(`#formAtenderPedido_${pedido_id}`).submit();
                    }

                } else {
                    toastr.error('ERROR EN EL ID DEL PEDIDO', 'PEDIDO NO ENCONTRADO');
                }

            }
        });
    }

    $('#modal_historial_atenciones').on('show.bs.modal', async function(event) {


        var button = $(event.relatedTarget)
        const pedido_id = button.data('pedido-id');


        document.querySelector('.pedido_id_span').textContent = pedido_id;

        //===== OBTENIENDO ATENCIONES DEL PEDIDO =======
        try {
            const res = await axios.get(route('pedidos.pedido.getAtenciones', {
                pedido_id
            }));
            console.log(res);
            const type = res.data.type;
            if (type == 'success') {
                const pedido_atenciones = res.data.pedido_atenciones;
                pintarTablePedidoAtenciones(pedido_atenciones);
            }
        } catch (error) {

        }

    })


    $('#modal_pedido_detalles').on('show.bs.modal', async function(event) {

        var button = $(event.relatedTarget)
        const pedido_id = button.data('pedido-id');

        document.querySelector('.pedido_id_span_pd').textContent = pedido_id;

        //===== OBTENIENDO DETALLES DEL PEDIDO =======
        try {
            const res = await axios.get(route('pedidos.pedido.getPedidoDetalles', {
                pedido_id
            }));
            console.log(res);
            const type = res.data.type;

            if (type == 'success') {
                const pedido_detalles = res.data.pedido_detalles;
                pintarTablePedidoDetalles(pedido_detalles);
            }

            if (type == 'error') {
                const message = res.data.message;
                const exception = res.data.exception;

                toastr.error(`${message} - ${exception}`, 'ERROR');
            }
        } catch (error) {
            toastr.error(error, 'ERROR EN LA PETICIÓN VER DETALLE DEL PEDIDO');
        }

    })

    function pintarTablePedidoDetalles(pedido_detalles) {
        const bodyPedidoDetalles = document.querySelector('#table-pedido-detalles tbody');

        if (detalles_data_table) {
            detalles_data_table.destroy();
        }

        bodyPedidoDetalles.innerHTML = '';
        let body = ``;

        pedido_detalles.forEach((pd) => {
            body += `<tr>
                <th scope="row">${pd.producto_nombre}</th>
                <td scope="row">${pd.color_nombre}</td>
                <td scope="row">${pd.talla_nombre}</td>
                <td scope="row">${pd.cantidad}</td>
                <td scope="row">${pd.cantidad_atendida}</td>
                <td scope="row">${pd.cantidad_pendiente}</td>
                <td scope="row">${pd.cantidad_enviada}</td>
                <td scope="row">${pd.cantidad_devuelta}</td>
                <td scope="row">${pd.cantidad_fabricacion}</td>
            </tr>`;
        })

        bodyPedidoDetalles.innerHTML = body;

        detalles_data_table = new DataTable('#table-pedido-detalles', {
            "order": [
                [0, 'desc']
            ],
            buttons: [{
                    extend: 'excelHtml5',
                    className: 'custom-button btn-check',
                    text: '<i class="fa fa-file-excel-o" style="font-size:15px;"></i> Excel',
                    title: 'DETALLES DEL PEDIDO',
                },
                {
                    extend: 'print',
                    className: 'custom-button btn-check',
                    text: '<i class="fa fa-print"></i> Imprimir',
                    title: 'DETALLES DEL PEDIDO'
                },
            ],
            dom: '<"buttons-container"B><"search-length-container"lf>tp',
            bProcessing: true,
            language: {
                processing: "Procesando datos...",
                search: "BUSCAR: ",
                lengthMenu: "MOSTRAR _MENU_ ITEMS",
                info: "MOSTRANDO _START_ A _END_ DE _TOTAL_ ITEMS",
                infoEmpty: "MOSTRANDO 0 ITEMS",
                infoFiltered: "(FILTRADO de _MAX_ ITEMS)",
                infoPostFix: "",
                loadingRecords: "CARGA EN CURSO",
                zeroRecords: "Aucun &eacute;l&eacute;ment &agrave; afficher",
                emptyTable: "NO HAY ITEMS DISPONIBLES",
                paginate: {
                    first: "PRIMERO",
                    previous: "ANTERIOR",
                    next: "SIGUIENTE",
                    last: "ÚLTIMO"
                },
                aria: {
                    sortAscending: ": activer pour trier la colonne par ordre croissant",
                    sortDescending: ": activer pour trier la colonne par ordre décroissant"
                }
            }
        });

    }

    function pintarTablePedidoAtenciones(pedido_atenciones) {
        const bodyPedidoDetalles = document.querySelector('#table-atenciones-detalles tbody');
        bodyPedidoDetalles.innerHTML = '';

        const bodyPedidoAtenciones = document.querySelector('#table-pedido-atenciones tbody');

        if (atenciones_data_table) {
            atenciones_data_table.destroy();
        }

        bodyPedidoAtenciones.innerHTML = '';
        let body = ``;

        pedido_atenciones.forEach((pa) => {
            body += `<tr class="rowAtencion" data-pedido-id="${pa.pedido_id}" data-documento-id=${pa.documento_id}>
                <th scope="row">${pa.documento_serie}-${pa.documento_correlativo}</th>
                <td scope="row">${pa.fecha_atencion}</td>
                <td scope="row">${pa.documento_usuario}</td>
                <td scope="row">${pa.documento_monto_envio}</td>
                <td scope="row">${pa.documento_monto_embalaje}</td>
                <td scope="row">${pa.documento_total_pagar}</td>
            </tr>`;
        })

        bodyPedidoAtenciones.innerHTML = body;

        atenciones_data_table = new DataTable('#table-pedido-atenciones', {
            "order": [
                [0, 'desc']
            ],
            buttons: [{
                    extend: 'excelHtml5',
                    className: 'custom-button btn-check',
                    text: '<i class="fa fa-file-excel-o" style="font-size:15px;"></i> Excel',
                    title: 'ATENCIONES DEL PEDIDO'
                },
                {
                    extend: 'print',
                    className: 'custom-button btn-check',
                    text: '<i class="fa fa-print"></i> Imprimir',
                    title: 'ATENCIONES DEL PEDIDO'
                },
            ],
            dom: '<"buttons-container"B><"search-length-container"lf>tp',
            bProcessing: true,
            language: {
                processing: "Procesando datos...",
                search: "BUSCAR: ",
                lengthMenu: "MOSTRAR _MENU_ ITEMS",
                info: "MOSTRANDO _START_ A _END_ DE _TOTAL_ ITEMS",
                infoEmpty: "MOSTRANDO 0 ITEMS",
                infoFiltered: "(FILTRADO de _MAX_ ITEMS)",
                infoPostFix: "",
                loadingRecords: "CARGA EN CURSO",
                zeroRecords: "Aucun &eacute;l&eacute;ment &agrave; afficher",
                emptyTable: "NO HAY ITEMS DISPONIBLES",
                paginate: {
                    first: "PRIMERO",
                    previous: "ANTERIOR",
                    next: "SIGUIENTE",
                    last: "ÚLTIMO"
                },
                aria: {
                    sortAscending: ": activer pour trier la colonne par ordre croissant",
                    sortDescending: ": activer pour trier la colonne par ordre décroissant"
                }
            }
        });

    }

    async function eliminarPedido(pedido_id) {

        //======== VALIDAR ESTADO DEL PEDIDO ======
        const pedido = pedidos_data_table.rows().data().filter(function(value, index) {
            return value['id'] == pedido_id;
        });

        let descripcion = '';
        if (pedido[0].facturado === 'SI') {
            descripcion = 'Recuerde realizar la nota de crédito del documento facturado';
        } else {
            descripcion = 'Operación no reversible';
        }

        if (pedido.length > 0) {
            const estado = pedido[0].estado;

            if (estado === "FINALIZADO") {
                toastr.error('EL PEDIDO NO PUEDE SER FINALIZADO', 'PEDIDO FINALIZADO');
                return;
            }

            if (estado === "ANULADO") {
                toastr.error('EL PEDIDO NO PUEDE SER ANULADO', 'PEDIDO ANULADO');
                return;
            }

        } else {
            toastr.error('ERROR EN EL ID DEL PEDIDO', 'PEDIDO NO ENCONTRADO');
            return;
        }

        //====== OBTENIENDO INFO DEL CLIENTE ======
        const res_cliente = await axios.get(route('pedidos.pedido.getCliente', {
            pedido_id
        }));

        if (!res_cliente.data.success) {
            toastr.error('ERROR AL OBTENER INFORMACION DEL CLIENTE');
        }

        if (res_cliente.data.success) {
            const cliente_pedido = res_cliente.data.cliente;
            const tipo_comprobante = cliente_pedido.tipo_documento === 'RUC' ? 'FACTURA' : 'BOLETA';
            Swal.fire({
                title: `Desea finalizar el pedido? CLIENTE: ${cliente_pedido.nombre} - ${cliente_pedido.tipo_documento}:${cliente_pedido.documento}`,
                text: descripcion,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "SÍ, FINALIZAR EL PEDIDO!"
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const res = await axios.delete(
                        `{{ route('pedidos.pedido.destroy', ['id' => ':id']) }}`.replace(':id',
                            pedido_id));
                    if (res.data.type == 'success') {
                        //====== ELIMINAR PEDIDO DEL DATATABLE ======
                        pedidos_data_table.rows().draw();
                        //===== ALERTA ======
                        toastr.success(`PEDIDO NRO° ${res.data.pedido_id}`, 'FINALIZADO');
                    }
                    if (res.data.type == 'error') {
                        toastr.error(res.data.message, 'ERROR');
                    }
                    console.log(res);
                    Swal.fire({
                        title: "FINALIZADO!",
                        text: "EL PEDIDO HA SIDO FINALIZADO.",
                        icon: "success"
                    });
                }
            });

        }
    }

    function controlFechas(target) {
        const id = target.getAttribute('id');

        if (id == "fecha_inicio") {
            const fecha_inicio = target.value;
            const fecha_fin = document.querySelector('#fecha_fin').value;

            if (fecha_inicio > fecha_fin && fecha_inicio) {
                document.querySelector('#fecha_fin').value = fecha_inicio;
            }
            document.querySelector('#fecha_fin').setAttribute('min', fecha_inicio);
        }

        if (id == "fecha_fin") {
            const fecha_fin = target.value;
            const fecha_inicio = document.querySelector('#fecha_inicio').value;

            if (fecha_fin < fecha_inicio && fecha_fin) {
                document.querySelector('#fecha_inicio').value = fecha_fin;
            }
            document.querySelector('#fecha_inicio').setAttribute('max', fecha_fin);
        }
    }

    async function generarRecibo(pedido_id) {
        const res_caja_apert = await buscarCajaApertUsuario();

        //======= REDIRIGIR A CREAR RECIBO DE CAJA ======
        if (res_caja_apert) {
            var url = "{{ route('recibos_caja.create', ':id') }}";
            url = url.replace(':id', pedido_id);
            window.location.href = url;
        }

    }


    //========= BUSCAR CAJA APERTURADA DE USUARIO ===========
    async function buscarCajaApertUsuario() {
        try {
            const res = await axios.get(route('recibos_caja.buscarCajaApertUsuario'));
            console.log(res);
            let validacion = true;

            if (res.data.success) {
                toastr.success(res.data.message, 'CAJA VERIFICADA');
            }

            if (!res.data.success) {
                validacion = false;
                toastr.error(res.data.message, 'CAJA VERIFICADA');
            }

            return validacion;

        } catch (error) {
            toastr.error('ERROR EN EL SERVIDOR', 'ERROR');
            return false;
        }
    }


    function filtrarDespachoFechaInic(fecha_inicio) {

        const fi = document.querySelector('#filtroFechaInicio').value;
        const ff = document.querySelector('#filtroFechaFin').value;

        if ((fi.toString().trim().length > 0 && ff.toString().trim().length > 0) & (fi > ff)) {
            document.querySelector('#filtroFechaInicio').value = '';
            toastr.error('FECHA INICIO DEBE SER MENOR O IGUAL A FECHA FIN', 'ERROR FECHAS');
            pedidos_data_table.ajax.reload();

            return;
        }

        pedidos_data_table.ajax.reload();
    }

    function filtrarDespachoFechaFin(fecha_fin) {
        const fi = document.querySelector('#filtroFechaInicio').value;
        const ff = document.querySelector('#filtroFechaFin').value;

        if ((fi.toString().trim().length > 0 && ff.toString().trim().length > 0) & (ff < fi)) {
            document.querySelector('#filtroFechaFin').value = '';
            toastr.error('FECHA FIN DEBE SER MAYOR O IGUAL A FECHA INICIO', 'ERROR FECHAS');
            pedidos_data_table.ajax.reload();
            return;
        }

        pedidos_data_table.ajax.reload();
    }

    function filtrarDespachosEstado(pedido_estado) {
        $('#pedidos_table').DataTable().draw();
    }


    //========== EXCEL PEDIDOS =======
    function excelPedidos() {
        const fecha_inicio = $('#filtroFechaInicio').val() ? $('#filtroFechaInicio').val() : null;
        const fecha_fin = $('#filtroFechaFin').val() ? $('#filtroFechaFin').val() : null;
        const estado = document.querySelector('#pedido_estado').value ? document.querySelector('#pedido_estado').value :
            null;

        const rutaExcelPedidos = @json(route('pedidos.pedido.getExcel')) + `/${fecha_inicio}/${fecha_fin}/${estado}`;

        window.location.href = rutaExcelPedidos;

    }

    async function facturar(pedido_id) {
        try {
            //====== VALIDANDO CLIENTE =====
            const res_cliente = await axios.get(route('pedidos.pedido.getCliente', {
                pedido_id
            }));

            if (res_cliente.data.success) {

                const cliente_pedido    = res_cliente.data.cliente;
                const tipo_comprobante  = cliente_pedido.tipo_documento === 'RUC' ? 'FACTURA' : 'BOLETA';

                let numero_documento = '99999999';
                if (cliente_pedido.tipo_documento === 'RUC' || cliente_pedido.tipo_documento === 'DNI') {
                    numero_documento = cliente_pedido.documento;
                }

                Swal.fire({
                    title: `DESEA GENERAR UNA ${tipo_comprobante} PARA EL CLIENTE: ${cliente_pedido.nombre} CON DOCUMENTO ${cliente_pedido.tipo_documento}: ${numero_documento}`,
                    text: "Esta acción no genera despacho y es IRREVERSIBLE!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: `SÍ, GENERAR ${tipo_comprobante}`
                }).then(async (result) => {
                    if (result.isConfirmed) {

                        Swal.fire({
                            title: `Generando ${tipo_comprobante}`,
                            text: 'Por favor, espere...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        const res = await axios.post(route('pedidos.pedido.facturar'), {
                            pedido_id
                        });

                        Swal.close();
                        if (res.data.success) {

                            pedidos_data_table.ajax.reload();
                            toastr.success(res.data.message, 'Exito');

                            let url_open_pdf =
                                '{{ route('ventas.documento.comprobante', [':id1', ':size']) }}'
                                .replace(':id1', res.data.documento_id)
                                .replace(':size', 80);

                            window.location.href = '{{ route('ventas.documento.index') }}';

                            window.open(url_open_pdf, 'Comprobante MERRIS',
                                'location=1, status=1, scrollbars=1,width=900, height=600');
                        } else {
                            toastr.error(res.data.message,
                                `ERROR AL GENERAR EL COMPROBANTE ${tipo_comprobante}`);
                            Swal.close();
                        }

                    }
                });

            } else {
                toastr.error('ERROR AL COMPROBAR EL DOCUMENTO DE IDENTIDAD DEL CLIENTE', 'OPERACIÓN ERRÓNEA');
                return;
            }

        } catch (error) {
            console.log(error);
        }
    }
</script>
@endpush
