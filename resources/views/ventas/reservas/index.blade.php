@extends('layout')
@section('content')
    @include('ventas.reservas.modals.modal-detalles-doc')
    @include('ventas.reservas.modals.modal-bultos')
@section('ventas-active', 'active')
@section('reservas-active', 'active')

<style>
    .fila-pendiente {
        background-color: #fff0f0 !important;
        /* rojito leve */
    }

    .fila-reservado {
        background-color: #f7f0ff !important;
        /* moradito leve */
    }

    .fila-despachado {
        background-color: #f0faff !important;
        /* celestito leve */
    }

    .icono-pendiente {
        color: #f8b3b3;
    }

    .icono-reservado {
        color: #d8b3ff;
    }

    .icono-despachado {
        color: #b3e5ff;
    }
</style>

</style>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
        <h2 style="text-transform:uppercase"><b>Listado de Reservas</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Reservas</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="row mb-5">
                        <div class="col-3">
                            <label for="filtroEstado" style="font-weight: bold;">ESTADO:</label>
                            <select id="filtroEstado" class="form-control select2_form"
                                onchange="dtReservas.ajax.reload();">
                                <option value="PENDIENTE">PENDIENTE</option>
                                <option value="RESERVADO">RESERVADO</option>
                                <option value="DESPACHADO">DESPACHADO</option>
                            </select>
                        </div>

                        <div class="col-3">
                            <label for="filtroCliente" style="font-weight: bold;">CLIENTE:</label>
                            <select class="select2_form" style="text-transform: uppercase; width:100%"
                                name="filtroCliente" id="filtroCliente" required onchange="dtReservas.ajax.reload();">
                                <option value=""></option>
                            </select>
                        </div>

                        <div class="col-3">
                            <label for="filtroFechaInicio" style="font-weight: bold;">FEC INICIO:</label>
                            <input value="<?php echo date('Y-m-d'); ?>" type="date" class="form form-control"
                                id="filtroFechaInicio" onchange="filtrarDespachoFechaInic()">
                        </div>
                        <div class="col-3">
                            <label for="filtroFechaFin" style="font-weight: bold;">FEC FIN:</label>
                            <input value="<?php echo date('Y-m-d'); ?>" type="date" class="form form-control"
                                id="filtroFechaFin" onchange="filtrarDespachoFechaFin()">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-auto d-flex align-items-center mr-3">
                            <i class="fas fa-square icono-pendiente"></i>
                            <span class="ml-2">PENDIENTE</span>
                        </div>
                        <div class="col-auto d-flex align-items-center mr-3">
                            <i class="fas fa-square icono-reservado"></i>
                            <span class="ml-2">RESERVADO</span>
                        </div>
                        <div class="col-auto d-flex align-items-center">
                            <i class="fas fa-square icono-despachado"></i>
                            <span class="ml-2">DESPACHADO</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">

                                @include('ventas.reservas.tables.tbl_list_reservas')

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@stop
@push('styles')
<link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
<style>
    .letrapequeña {
        font-size: 11px;
    }

    .envio-despachado {
        background-color: rgb(220, 255, 255) !important;
    }

    .envio-embalado {
        background-color: rgb(239, 244, 213) !important;
    }

    .col-estado-pendiente {
        border: 2px solid #FF5A5F;
        background-color: #FF5A5F;
        color: #FFFFFF;
        font-weight: bold;
        border-radius: 7px;
        padding: 0 5px;
        text-align: center;
        display: inline-block;
    }

    .col-estado-despachado {
        border: 2px solid #014c5b;
        background-color: #014c5b;
        color: #FFFFFF;
        font-weight: bold;
        border-radius: 7px;
        padding: 0 5px;
        text-align: center;
        display: inline-block;
    }

    .col-estado-reservado {
        border: 2px solid #566003;
        background-color: #566003;
        color: #FFFFFF;
        font-weight: bold;
        border-radius: 7px;
        padding: 0 5px;
        text-align: center;
        display: inline-block;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
    let detallesDataTable;
    let dtReservas = null;

    document.addEventListener('DOMContentLoaded', () => {
        iniciarDtReservas();
        events();
        iniciarSelect2();
        detallesDataTable = dataTableDetalles();
    })

    function events() {
        eventsModalBultos();
    }

    function iniciarSelect2() {
        $(".select2_form").select2({
            placeholder: "SELECCIONAR",
            allowClear: true,
            height: '200px',
            width: '100%',
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

    function iniciarDtReservas() {

        dtReservas = new DataTable('#dataTables-reserva', {
            "bPaginate": true,
            "bLengthChange": true,
            "bFilter": true,
            "bInfo": true,
            "bAutoWidth": false,
            "processing": true,
            "serverSide": true,
            initComplete: function() {
                $('.dataTables_filter').append(`
                <div class="text-muted small mt-1">
                    <strong>Buscar por: Modo,Pedido, Doc, Cliente, Vendedor, Tipo envío, Empresa envío</strong>
                </div>
                `);
            },
            rowCallback: function(row, data, index) {
                // Asegúrate de que `estado` esté en el objeto `data`
                let estado = data.estado;

                if (estado === 'PENDIENTE') {
                    $(row).addClass('fila-pendiente');
                } else if (estado === 'RESERVADO') {
                    $(row).addClass('fila-reservado');
                } else if (estado === 'DESPACHADO') {
                    $(row).addClass('fila-despachado');
                }
            },
            "ajax": {
                "url": "{{ route('ventas.reservas.getTable') }}",
                "type": "GET",
                "beforeSend": function() {
                    mostrarAnimacion();
                },
                "data": function(d) {
                    d.fecha_inicio = $('#filtroFechaInicio').val();
                    d.fecha_fin = $('#filtroFechaFin').val();
                    d.estado = $('#filtroEstado').val();
                    d.cliente_id = $('#filtroCliente').val();
                },
                "complete": function() {
                    ocultarAnimacion();
                }
            },
            "columns": [
                {
                    data: 'modo',
                    name:'ev.modo',
                    className: "text-center letrapequeña"
                },
                {
                    data: 'pedido_nro',
                    name:'pedido_nro',
                    className: "text-center letrapequeña"
                },
                {
                    data: 'documento_nro',
                    name:'ev.documento_nro',
                    className: "text-center letrapequeña"
                },
                {
                    data: 'cliente_nombre',
                    name:'ev.cliente_nombre',
                    className: "text-left letrapequeña"
                },
                {
                    data: 'cliente_celular',
                    searchable:false,
                    className: "text-left letrapequeña"
                },

                {
                    data: 'user_vendedor_nombre',
                    name:'ev.user_vendedor_nombre',
                    className: "text-left letrapequeña"
                },
                {
                    data: 'almacen_nombre',
                    searchable:false,
                    className: "text-left letrapequeña"
                },
                {
                    data: 'sede_origen_nombre',
                    searchable:false,
                    className: "text-left letrapequeña"
                },
                {
                    data: 'sede_despachadora_nombre',
                    searchable:false,
                    className: "text-left letrapequeña"
                },
                {
                    data: 'user_despachador_nombre',
                    searchable:false,
                    className: "text-left letrapequeña"
                },
                {
                    data: 'fecha_envio_propuesta',
                    searchable:false,
                    className: "text-left letrapequeña"
                },
                {
                    data: 'fecha_envio',
                    searchable:false,
                    className: "text-left letrapequeña"
                },
                {
                    data: 'fecha_registro',
                    searchable:false,
                    className: "text-left letrapequeña"
                },
                {
                    data: 'tipo_envio',
                    name:'ev.tipo_envio',
                    className: "text-center letrapequeña"
                },
                {
                    data: 'empresa_envio_nombre',
                    name:'ev.empresa_envio_nombre',
                    className: "text-center letrapequeña"
                },
                {
                    data: 'sede_envio_nombre',
                    searchable:false,
                    className: "text-center letrapequeña"
                },
                {
                    data: 'ubigeo',
                    searchable:false,
                    className: "text-center letrapequeña"
                },
                {
                    data: 'entrega_domicilio',
                    searchable:false,
                    className: "text-center letrapequeña"
                },
                {
                    data: 'direccion_entrega',
                    searchable:false,
                    className: "text-center letrapequeña"
                },
                {
                    data: 'destinatario_nombre',
                    searchable:false,
                    className: "text-center letrapequeña"
                },
                {
                    data: 'destinatario_nro_doc',
                    searchable:false,
                    className: "text-center letrapequeña"
                },
                {
                    data: 'tipo_pago_envio',
                    searchable:false,
                    className: "text-center letrapequeña"
                },
                {
                    data: 'monto_envio',
                    searchable:false,
                    className: "text-center letrapequeña"
                },
                {
                    data: 'obs_despacho',
                    searchable:false,
                    className: "text-center letrapequeña"
                },
                {
                    data: 'estado',
                    searchable:false,
                    className: "text-center letrapequeña",
                    render: function(data) {
                        let estado = '';
                        if (data == "PENDIENTE") {
                            estado = `<div class="col-estado-pendiente">${data}</div>`;
                        }
                        if (data == "RESERVADO") {
                            estado = `<div class="col-estado-reservado">${data}</div>`;
                        }
                        if (data == "DESPACHADO") {
                            estado = `<div class="col-estado-despachado">${data}</div>`;
                        }
                        return estado;
                    }
                },
                {
                    data: null,
                    searchable:false,
                    className: "text-center",
                    render: function(data) {
                        //Ruta Detalle
                        var url_detalle = '{{ route('ventas.despachos.showDetalles', ':id') }}';
                        url_detalle = url_detalle.replace(':id', data.id);


                        //======== ACCIONES ========
                        let acciones = `<div class='btn-group' style='text-transform:capitalize;'><button data-toggle='dropdown' class='btn btn-primary btn-sm  dropdown-toggle'><i class='fa fa-bars'></i></button>
                                                        <ul class='dropdown-menu'>
                                                            <li><a class='dropdown-item' href='javascript:void(0);' onclick="verDetalles(${data.documento_id})" title='Modificar' ><b><i class='fa fa-eye'></i> Detalle</a></b></li>
                                                            <li><a class='dropdown-item' href='javascript:void(0);' onclick="imprimirEnvio(${data.documento_id},${data.id})" title='Imprimir' ><b><i class="fas fa-print"></i> Imprimir</a></b></li>
                                                       `;

                        if (data.estado == "PENDIENTE") {
                            acciones += `<li class='dropdown-divider'></li>
                                                            <li><a class='dropdown-item' href='javascript:void(0);' onclick="reservar(${data.documento_id},${data.id})" title='Reservar' ><b><i class="fas fa-tape"></i> Reservar</a></b></li>
                                                            <li><a class='dropdown-item' href='javascript:void(0);' onclick="despachar(${data.documento_id},${data.id})" title='Despachar' ><b><i class="fas fa-people-carry"></i> Despachar</a></b></li>
                                                    </ul>
                                                    </div>`;
                        }

                        if (data.estado == "RESERVADO") {
                            acciones += `<li class='dropdown-divider'></li>
                                                <li><a class='dropdown-item' href='javascript:void(0);' onclick="despachar(${data.documento_id},${data.id})" title='Despachar' ><b><i class="fas fa-people-carry"></i> Despachar</a></b></li>
                                                </ul></div>`;
                        }

                        if (data.estado == "DESPACHADO") {
                            acciones += `</ul></div>`;
                        }

                        return acciones;
                    }
                }

            ],
            "createdRow": function(row, data, dataIndex) {
                if (data.estado === 'EMBALADO') {
                    $(row).addClass('envio-embalado');
                }
                if (data.estado === 'DESPACHADO') {
                    $(row).addClass('envio-despachado');
                }
                $('td', row).css('vertical-align', 'middle');
            },
            "language": {
                "url": "{{ asset('Spanish.json') }}"
            },
            "order": [],
            "columnDefs": [{
                "searchable": false,
                "targets": 5
            }]

        });


    }


    //Modal Eliminar
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger',
        },
        buttonsStyling: false
    })



    async function verDetalles(documento_id) {
        try {
            mostrarAnimacion();
            const res = await axios.get(route('ventas.despachos.showDetalles', documento_id));

            if (res.data.success) {
                const detalles_doc_venta = res.data.detalles_doc_venta;

                $("#modal_detalles_doc").modal("show");
                pintarDetallesDoc(detalles_doc_venta);
                pintarMaestroDoc(res.data.documento)

            } else {
                toastr.error(`${res.data.message} - ${res.data.exception}`, "ERROR");
            }
        } catch (error) {
            toasr.error(error, 'ERROR EN LA PETICIÓN MOSTRAR DETALLE')
        } finally {
            ocultarAnimacion();
        }
    }

    function pintarMaestroDoc(documento) {
        document.querySelector('#info_documento').textContent = `${documento.serie}-${documento.correlativo}`;
        document.querySelector('#info_almacen_despacho').textContent = `${documento.almacen_despacho}`;
        document.querySelector('#info_sede_despacho').textContent = `${documento.sede_despacho}`;
    }

    function pintarDetallesDoc(detalles_doc_venta) {

        detallesDataTable.clear();

        detalles_doc_venta.forEach((ddc) => {
            detallesDataTable.row.add([
                ddc.nombre_modelo,
                ddc.nombre_producto,
                ddc.nombre_color,
                ddc.nombre_talla,
                parseInt(ddc.cantidad),
                parseInt(ddc.cantidad_cambiada),
                parseInt(ddc.cantidad_sin_cambio)
            ]);
        });

        detallesDataTable.draw();

    }

    function imprimirEnvio(documento_id, despacho_id) {
        document.querySelector('#documento_id').value = documento_id;
        document.querySelector('#despacho_id').value = despacho_id;

        $('#modal-bultos').modal('show');

    }

    //========= RESERVAR =========
    function reservar(documento_id, despacho_id) {
        //======= OBTENER LOS DATOS DEL DESPACHO ======
        var miTabla = dtReservas;

        const fila = miTabla.rows().data().filter(function(value, index) {
            return value['id'] == despacho_id;
        });

        let descripcion = ``;

        if (fila.length > 0) {
            descripcion += `DESTINO: ${fila[0].ubigeo}
                                DESTINATARIO: ${fila[0].destinatario_nombre} - ${fila[0].destinatario_nro_doc}`;
        }

        //======== ALERTA =========
        Swal.fire({
            title: "Desea reservar el envío?",
            text: descripcion,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, reservar!",
            showLoaderOnConfirm: true,
            allowOutsideClick: false,
            preConfirm: async () => {
                const res = await setEmbalaje(despacho_id, documento_id);
                return res;
            }
        }).then((result) => {
            if (result.value.success) {

                Swal.fire(result.value.message, descripcion, "success");
            } else {
                Swal.fire(`${result.value.message} - ${result.value.exception}`, descripcion, "error");
            }
        });
    }


    async function setEmbalaje(despacho_id, documento_id) {
        try {

            const res = await axios.post(route('ventas.despachos.setEmbalaje'), {
                despacho_id,
                documento_id
            })

            if (res.data.success) {
                //======= PINTANDO ESTADO EN DATATABLE ======
                const fila = dtReservas.row((idx, data) => data['id'] == despacho_id);
                const indiceFila = dtReservas.row((idx, data) => data['id'] == despacho_id).index();
                await fila.cell(indiceFila, 0).data('RESERVADO').draw();
                //toastr.success(res.data.message,'OPERACIÓN COMPLETADA');
            }

            return res.data;

        } catch (error) {

        }
    }


    function despachar(documento_id, despacho_id) {
        //======= OBTENER LOS DATOS DEL DESPACHO ======
        var miTabla = dtReservas;

        const fila = miTabla.rows().data().filter(function(value, index) {
            return value['id'] == despacho_id;
        });

        let descripcion = ``;

        if (fila.length > 0) {
            descripcion += `DESTINO: ${fila[0].ubigeo}
                                DESTINATARIO: ${fila[0].destinatario_nombre} - ${fila[0].destinatario_nro_doc}`;
        }

        //======== ALERTA =========
        Swal.fire({
            title: "Desea despachar el envío?",
            text: descripcion,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, despachar!",
            showLoaderOnConfirm: true,
            allowOutsideClick: false,
            preConfirm: async () => {
                const res = await setDespacho(despacho_id, documento_id);
                return res;
            }
        }).then((result) => {
            if (result.value.success) {

                Swal.fire(result.value.message, descripcion, "success");
            } else {
                Swal.fire(`${result.value.message} - ${result.value.exception}`, descripcion, "error");
            }
        });
    }


    async function setDespacho(despacho_id, documento_id) {
        try {

            const res = await axios.post(route('ventas.despachos.setDespacho'), {
                despacho_id,
                documento_id
            })

            if (res.data.success) {
                dtReservas.ajax.reload(null, false);
                //======= PINTANDO ESTADO EN DATATABLE ======

                // const fila          =   dtReservas.row((idx,data) => data['id'] == despacho_id);
                // const indiceFila    =   dtReservas.row((idx,data) => data['id'] == despacho_id).index();
                // await fila.cell(indiceFila,0).data('DESPACHADO').draw();
            }

            return res.data;

        } catch (error) {

        }
    }


    function filtrarDespachoFechaInic(fecha_inicio) {

        const fi = document.querySelector('#filtroFechaInicio').value;
        const ff = document.querySelector('#filtroFechaFin').value;

        if ((fi.toString().trim().length > 0 && ff.toString().trim().length > 0) & (fi > ff)) {
            document.querySelector('#filtroFechaInicio').value = '';
            toastr.error('FECHA INICIO DEBE SER MENOR O IGUAL A FECHA FIN', 'ERROR FECHAS');
            dtReservas.ajax.reload();

            return;
        }

        dtReservas.ajax.reload();
    }

    function filtrarDespachoFechaFin(fecha_fin) {
        const fi = document.querySelector('#filtroFechaInicio').value;
        const ff = document.querySelector('#filtroFechaFin').value;

        if ((fi.toString().trim().length > 0 && ff.toString().trim().length > 0) & (ff < fi)) {
            document.querySelector('#filtroFechaFin').value = '';
            toastr.error('FECHA FIN DEBE SER MAYOR O IGUAL A FECHA INICIO', 'ERROR FECHAS');
            dtReservas.ajax.reload();
            return;
        }

        dtReservas.ajax.reload();
    }
</script>
@endpush
