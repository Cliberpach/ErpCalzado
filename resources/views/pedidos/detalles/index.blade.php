@extends('layout')
@section('content')

@section('pedidos-active', 'active')
@section('pedidos-detalles-active', 'active')
@include('pedidos.detalles.modals.modal_detalles_anteciones')
@include('pedidos.detalles.modals.modal_detalles_despachos')
@include('pedidos.detalles.modals.modal_detalles_devoluciones')
@include('pedidos.detalles.modals.modal_detalles_fabricaciones')

<style>
    .overlay_pedidos_detalles {
        position: fixed;
        /* Fija el overlay para que cubra todo el viewport */
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        /* Color oscuro con opacidad */
        z-index: 99999999;
        /* Asegura que el overlay esté sobre todo */
        display: flex;
        justify-content: center;
        align-items: center;
        color: white;
        font-size: 24px;
        visibility: hidden;
    }

    /*========== LOADER SPINNER =======*/
    .loader_pedidos_detalles {
        position: relative;
        width: 75px;
        height: 100px;
        background-repeat: no-repeat;
        background-image: linear-gradient(#DDD 50px, transparent 0),
            linear-gradient(#DDD 50px, transparent 0),
            linear-gradient(#DDD 50px, transparent 0),
            linear-gradient(#DDD 50px, transparent 0),
            linear-gradient(#DDD 50px, transparent 0);
        background-size: 8px 100%;
        background-position: 0px 90px, 15px 78px, 30px 66px, 45px 58px, 60px 50px;
        animation: pillerPushUp 4s linear infinite;
    }

    .loader_pedidos_detalles:after {
        content: '';
        position: absolute;
        bottom: 10px;
        left: 0;
        width: 10px;
        height: 10px;
        background: #de3500;
        border-radius: 50%;
        animation: ballStepUp 4s linear infinite;
    }

    @keyframes pillerPushUp {

        0%,
        40%,
        100% {
            background-position: 0px 90px, 15px 78px, 30px 66px, 45px 58px, 60px 50px
        }

        50%,
        90% {
            background-position: 0px 50px, 15px 58px, 30px 66px, 45px 78px, 60px 90px
        }
    }

    @keyframes ballStepUp {
        0% {
            transform: translate(0, 0)
        }

        5% {
            transform: translate(8px, -14px)
        }

        10% {
            transform: translate(15px, -10px)
        }

        17% {
            transform: translate(23px, -24px)
        }

        20% {
            transform: translate(30px, -20px)
        }

        27% {
            transform: translate(38px, -34px)
        }

        30% {
            transform: translate(45px, -30px)
        }

        37% {
            transform: translate(53px, -44px)
        }

        40% {
            transform: translate(60px, -40px)
        }

        50% {
            transform: translate(60px, 0)
        }

        57% {
            transform: translate(53px, -14px)
        }

        60% {
            transform: translate(45px, -10px)
        }

        67% {
            transform: translate(37px, -24px)
        }

        70% {
            transform: translate(30px, -20px)
        }

        77% {
            transform: translate(22px, -34px)
        }

        80% {
            transform: translate(15px, -30px)
        }

        87% {
            transform: translate(7px, -44px)
        }

        90% {
            transform: translate(0, -40px)
        }

        100% {
            transform: translate(0, 0);
        }
    }
</style>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
        <h2 style="text-transform:uppercase"><b>Listado de Detalles de Pedidos</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Pedidos Detalles</strong>
            </li>
        </ol>
    </div>
</div>

<div class="overlay_pedidos_detalles">
    <span class="loader_pedidos_detalles"></span>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row mb-3">
        <div class="col-9">
            <div class="row">
                {{-- <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <label for="fecha_inicio" style="font-weight: bold;">Fecha desde:</label>
                    <input type="date" class="form-control" id="filtroFechaInicio" value="{{ old('fecha_inicio', now()->format('Y-m-d')) }}" onchange="filtrarDespachoFechaInic(this.value)">
                </div>
                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <label for="fecha_fin" style="font-weight: bold;">Fecha hasta:</label>
                    <input type="date" class="form-control" id="filtroFechaFin" value="{{ now()->format('Y-m-d') }}" onchange="filtrarDespachoFechaFin(this.value)">
                </div> --}}
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-12 col-xs-12">
                    <label for="pedido_estado" style="font-weight: bold;">ESTADO</label>
                    <select id="pedido_detalle_estado" class="form-control select2_form"
                        onchange="filtrarEstadoDetalle()">
                        <option value="PENDIENTE">PENDIENTE</option>
                        <option value="ATENDIDO">ATENDIDO</option>
                        <option value="FABRICACION">FABRICACION</option>
                    </select>
                </div>

                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-12 col-xs-12">
                    <label for="filtroCliente" style="font-weight: bold;">CLIENTE:</label>
                    <select class="select2_form" style="text-transform: uppercase; width:100%" name="filtroCliente"
                        id="filtroCliente" required onchange="dataTablePedidoDetalles.ajax.reload();">
                    </select>
                </div>

                {{-- <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-3">
                    <label for="modelo_id" style="font-weight: bold;">MODELO</label>
                    <select id="modelo_id" class="form-control select2_form" onchange="getProductosByModelo(this)">
                        <option value=""></option>
                        @foreach ($modelos as $modelo)
                            <option value="{{ $modelo->id }}">{{ $modelo->descripcion }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                    <label for="producto_id" style="font-weight: bold;">PRODUCTO</label>
                    <select id="producto_id" class="form-control select2_form" onchange="filtrarProducto()">
                        <option value=""></option>

                    </select>
                </div> --}}

            </div>
        </div>
        {{-- <div class="col-3 d-flex align-items-end justify-content-end">
        <button hidden class="btn btn-primary" onclick="llenarCantEnviada()">LLENAR CANT ENVIADA</button>
       </div> --}}
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        @include('pedidos.detalles.tables.table_pedidos_detalles')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="panel panel-success">
                <div class="panel-heading">PROGRAMACIÓN DE PRODUCCIÓN</div>
                <div class="panel-body">
                    <div class="row mb-3">
                        {{-- <div class="col-12 d-flex justify-content-end">
                            <a class="btn btn-success" href="javascript:void(0);" onclick="descargarPdfProgramacionProduccion()">
                                <i class="fas fa-file-pdf"></i> PDF
                            </a>
                        </div> --}}
                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                            <label class="required" style="font-weight: bold;" for="fecha_propuesta_atencion">FECHA
                                PROPUESTA ATENCIÓN</label>
                            <input id="fecha_propuesta_atencion" type="date" class="form-control">
                            <p style="font-weight: bold;color:red;" class="fecha_propuesta_atencion_error spanError">
                            </p>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                            <label style="font-weight: bold;" for="observacion" class="form-label">OBSERVACIÓN</label>
                            <textarea maxlength="260" id="observacion" class="form-control" rows="4" placeholder="Ingrese su texto aquí..."></textarea>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 d-flex justify-content-end">
                            <a class="btn btn-success" style="height: 32px;" href="javascript:void(0);"
                                onclick="generarOrdenProduccion()">
                                <i class="fas fa-save"></i> GENERAR ORDEN DE PRODUCCIÓN
                            </a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        @include('pedidos.detalles.tables.table_programacion_produccion')
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
<link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<!-- DataTable -->
<script src="{{ asset('Inspinia/js/plugins/dataTables/datatables.min.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>

<script>
    const lstProgramaProduccion = [];
    const lstChecksProductosMarcados = [];
    let dataTablePedidoDetalles = null;
    let dataTableProgramacionProduccion = null;

    document.addEventListener('DOMContentLoaded', () => {
        loadSelect2();
        loadDataTablePedidoDetalles();
        loadDataTableProgramacionProduccion();
        events();
    })

    function events() {
        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('checkProducto')) {

                const producto = getProducto(e.target);
                const checkProductoId = e.target.getAttribute('id');
                if (e.target.checked) {
                    agregarProductoToProduccion(producto);
                    lstChecksProductosMarcados.push(checkProductoId);
                }
                if (!e.target.checked) {
                    quitarProductoToProduccion(producto, checkProductoId);
                }

                destruirDataTableProgramacionProduccion();
                limpiarTableProgramacionProduccion();
                pintarTableProgramacionProduccion();
                loadDataTableProgramacionProduccion();
            }
        })

        //======= EVENTO DE DATATABLE - PETICIÓN DE TRAER DATOS SERVER SIDE =====
        dataTablePedidoDetalles.on('preXhr.dt', function(e, settings, data) {
            mostrarAnimacionCotizacion();
        });

        //===== EVENTO DATATABLE - DATOS LLEGARON DEL SERVER SIDE ======
        dataTablePedidoDetalles.on('xhr.dt', function(e, settings, json, xhr) {});

        //===== EVENTO DATATABLE - LA TABLA HA TERMINADO DE DIBUJARSE ========
        dataTablePedidoDetalles.on('draw.dt', function() {
            remarcarCheckboxProductos();
            ocultarAnimacionCotizacion();
        });
    }

    function loadSelect2() {
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

    function destruirDataTableProgramacionProduccion() {
        if (dataTableProgramacionProduccion) {
            dataTableProgramacionProduccion.destroy();
        }
    }

    function loadDataTableProgramacionProduccion() {
        dataTableProgramacionProduccion = $('#table_programacion_produccion').DataTable({
            language: {
                "decimal": "",
                "emptyTable": "No hay información disponible en la tabla",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                "infoFiltered": "(filtrado de _MAX_ registros totales)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ registros",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscar:",
                "zeroRecords": "No se encontraron resultados",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                },
                "aria": {
                    "sortAscending": ": activar para ordenar la columna de manera ascendente",
                    "sortDescending": ": activar para ordenar la columna de manera descendente"
                }
            }
        });

    }

    function loadDataTablePedidoDetalles() {
        dataTablePedidoDetalles = $('#pedidos_detalles').DataTable({
            "dom": '<"html5buttons"B>lTfgitp',
            "buttons": [{
                    text: '<i class="fa fa-file-excel-o"></i> Excel',
                    title: 'pedidos_detalles',
                    action: function(e, dt, button, config) {
                        const pedido_detalle_estado = $('#pedido_detalle_estado').val();
                        const cliente_id = $('#cliente_id').val();
                        const modelo_id = $('#modelo_id').val();
                        const producto_id = $('#producto_id').val();

                        let url = route('pedidos.pedidos_detalles.getExcel', {
                            pedido_detalle_estado: pedido_detalle_estado || '-',
                            cliente_id: cliente_id || '-',
                            modelo_id: modelo_id || '-',
                            producto_id: producto_id || '-'
                        });

                        window.location.href = url;
                    }
                },
                {
                    title: 'pedidos_detalles',
                    text: '<i class="fa fa-print"></i> PDF',
                    action: function(e, dt, button, config) {
                        const pedido_detalle_estado = $('#pedido_detalle_estado').val();
                        const cliente_id = $('#cliente_id').val();
                        const modelo_id = $('#modelo_id').val();
                        const producto_id = $('#producto_id').val();

                        let url = route('pedidos.pedidos_detalles.getPdf', {
                            pedido_detalle_estado: pedido_detalle_estado || '-',
                            cliente_id: cliente_id || '-',
                            modelo_id: modelo_id || '-',
                            producto_id: producto_id || '-'
                        });


                        // Redirigir a la URL
                        window.location.href = url;
                    }
                }
            ],
            "responsive": true,
            "bPaginate": true,
            "bLengthChange": true,
            "bFilter": true,
            "bInfo": true,
            "bAutoWidth": false,
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "{{ route('pedidos.pedidos_detalles.getTable') }}",
                "type": "GET",
                "data": function(d) {
                    d.pedido_detalle_estado = $('#pedido_detalle_estado').val();
                    d.cliente_id = $('#filtroCliente').val();
                }
            },
            "columns": [{
                    data: null,
                    className: "text-center",
                    searchable: false,
                    render: function(data, type, row) {
                        let etiqueta = ``;

                        if (row.orden_produccion_id == null && row.cantidad_pendiente > 0) {
                            etiqueta =
                                `<input class="form-control checkProducto" id="checkProducto_${row.pedido_id}_${row.producto_id}_${row.color_id}_${row.talla_id}" type="checkbox" data-modelo-id="${row.modelo_id}" data-producto-id="${row.producto_id}" data-color-id="${row.color_id}" data-talla-id="${row.talla_id}"
                            data-pedido-id="${row.pedido_id}" data-modelo-nombre="${row.modelo_nombre}" data-producto-nombre="${row.producto_nombre}" data-color-nombre="${row.color_nombre}" data-talla-nombre="${row.talla_nombre}"  data-cant-pend="${row.cantidad_pendiente}">`;
                        }

                        return etiqueta;
                    }
                },
                {
                    data: 'pedido_name_id',
                    className: "text-left",
                    name: 'p.pedido_nro',
                    searchable: true,
                    render: function(data, type, row) {
                        return `<p style="font-weight:bold;">${data}</p>`;
                    }
                },
                {
                    data: 'cliente_nombre',
                    className: "text-center",
                    searchable: false
                },
                {
                    data: 'pedido_fecha',
                    className: "text-center",
                    searchable: false
                },
                {
                    data: 'fecha_propuesta',
                    className: "text-center",
                    searchable: false
                },
                {
                    data: 'vendedor_nombre',
                    className: "text-center",
                    searchable: false
                },
                {
                    data: 'producto_nombre',
                    name: 'pd.producto_nombre',
                    className: "text-center",
                    searchable: true
                },
                {
                    data: 'color_nombre',
                    name: 'pd.color_nombre',
                    className: "text-center",
                    searchable: true
                },
                {
                    data: 'talla_nombre',
                    name: 'pd.talla_nombre',
                    className: "text-left",
                    searchable: true
                },
                {
                    data: 'cantidad',
                    className: "text-left",
                    searchable: false
                },
                {
                    data: 'precio_unitario_nuevo',
                    className: "text-center",
                    searchable: false
                },
                {
                    data: 'importe_nuevo',
                    className: "text-center",
                    searchable: false
                },
                {
                    data: 'cantidad_atendida',
                    className: "text-center",
                    searchable: false,
                    render: function(data, type, row) {
                        let etiqueta = ``;

                        if (data > 0) {
                            etiqueta =
                                `<p style="cursor:pointer;font-weight:bold;margin:0;color:blue;" onclick="openMdlAtenciones(${row.pedido_id}, ${row.producto_id}, ${row.color_id}, ${row.talla_id})" style="font-weight:bold;">${data}</p>`;
                        }

                        if (data == 0) {
                            etiqueta = `<p style="margin:0;">${data}</p>`
                        }

                        return etiqueta;
                    }
                },
                {
                    data: 'cantidad_pendiente',
                    className: "text-left",
                    searchable: false
                },
                {
                    data: 'cantidad_enviada',
                    className: "text-center",
                    searchable: false,
                    render: function(data, type, row) {
                        let etiqueta = ``;

                        if (row.cantidad_atendida > 0) {
                            etiqueta =
                                `<p style="cursor:pointer;font-weight:bold;margin:0;" onclick="openMdlDespachos(${row.pedido_id}, ${row.producto_id}, ${row.color_id}, ${row.talla_id})" style="font-weight:bold;">${data}</p>`;
                        }

                        if (row.cantidad_atendida == 0) {
                            etiqueta = `<p style="margin:0;">${data}</p>`
                        }

                        return etiqueta;
                    }
                },
                {
                    data: 'cantidad_fabricacion',
                    className: "text-center",
                    searchable: false,
                    render: function(data, type, row) {

                        let etiqueta = '';
                        if (data > 0) {
                            etiqueta =
                                `<p style="cursor:pointer;font-weight:bold;margin:0;color:blue;" onclick="openMdlFabricaciones(${row.pedido_id}, ${row.producto_id}, ${row.color_id}, ${row.talla_id})" style="font-weight:bold;">${data}</p>`;
                        }

                        if (data == 0) {
                            etiqueta = `<p style="margin:0;">${data}</p>`
                        }

                        return etiqueta;
                    }
                },
                {
                    data: 'detalle_id',
                    className: "text-center",
                    searchable: false,
                    render: function(data, type, row) {

                        return 'CANT CAMBIO';
                    }
                },
                {
                    data: 'cantidad_devuelta',
                    className: "text-center",
                    searchable: false,
                    render: function(data, type, row) {

                        return `<p style="cursor:pointer;font-weight:bold;margin:0;" onclick="openMdlDevoluciones(${row.pedido_id}, ${row.producto_id}, ${row.color_id}, ${row.talla_id})" >${data}</p>`;
                    }
                },
            ],
            "language": {
                "search": "Buscar código,producto,color,talla:",
                "lengthMenu": "Mostrar _MENU_ registros por página",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando página _PAGE_ de _PAGES_",
                "infoEmpty": "No hay registros disponibles",
                "infoFiltered": "(filtrado de _MAX_ registros totales)",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "order": [
                [1, "desc"]
            ],
            "initComplete": function(settings, json) {
                $('.dataTables_filter input').attr('placeholder', 'Buscar');
            }
        });

        let label = $('.dataTables_filter label');
        label.contents().first()[0].textContent = "Buscar código, producto, color, talla: ";
        label.css({
            'color': '#3f51b5',
            'font-weight': 'bold'
        });
    }

    function getProducto(checkProducto) {
        const pedido_id = checkProducto.getAttribute('data-pedido-id');
        const modelo_id = checkProducto.getAttribute('data-modelo-id');
        const producto_id = checkProducto.getAttribute('data-producto-id');
        const color_id = checkProducto.getAttribute('data-color-id');
        const talla_id = checkProducto.getAttribute('data-talla-id');
        const modelo_nombre = checkProducto.getAttribute('data-modelo-nombre');
        const producto_nombre = checkProducto.getAttribute('data-producto-nombre');
        const color_nombre = checkProducto.getAttribute('data-color-nombre');
        const talla_nombre = checkProducto.getAttribute('data-talla-nombre');
        const cantidad_pendiente = checkProducto.getAttribute('data-cant-pend');


        return {
            pedido_id,
            modelo_id,
            producto_id,
            color_id,
            talla_id,
            modelo_nombre,
            producto_nombre,
            color_nombre,
            talla_nombre,
            cantidad_pendiente
        };

    }


    //========= AGREGAR PRODUCTO AL LISTADO DE PRODUCCIÓN =======
    function agregarProductoToProduccion(producto_nuevo) {

        //====== REVIZAR SI EL PEDIDO ID YA EXISTE EN EL LISTADO ======
        const indicePedido = lstProgramaProduccion.findIndex((item) => {
            return item.id == producto_nuevo.pedido_id;
        })

        //======= EN CASO EL PEDIDO SEA NUEVO ======
        if (indicePedido === -1) {

            //======= INSTANCIAR PEDIDO ======
            const instancia_pedido = {
                id: producto_nuevo.pedido_id
            };


            //======== INSTANCIAR PRODUCTO ======
            const instancia_producto = {
                id: producto_nuevo.producto_id,
                nombre: producto_nuevo.producto_nombre
            }

            //======== INSTANCIANDO MODELO ======
            const instancia_modelo = {
                id: producto_nuevo.modelo_id,
                nombre: producto_nuevo.modelo_nombre
            };

            //====== INSTANCIANDO COLOR =======
            const instancia_color = {
                id: producto_nuevo.color_id,
                nombre: producto_nuevo.color_nombre
            };

            //====== INSTANCIANDO TALLA ======
            const instancia_talla = {
                id: producto_nuevo.talla_id,
                nombre: producto_nuevo.talla_nombre,
                cantidad_pendiente: parseInt(producto_nuevo.cantidad_pendiente)
            };

            //====== FORMANDO =======
            instancia_producto.modelo = instancia_modelo;
            instancia_producto.colores = [instancia_color];
            instancia_producto.colores[0].tallas = [instancia_talla];
            instancia_pedido.productos = [instancia_producto];

            lstProgramaProduccion.push(instancia_pedido);

        }

        //====== EN CASO EL PEDIDO YA EXISTA ======
        if (indicePedido !== -1) {

            const pedido_existe = lstProgramaProduccion[indicePedido];

            //========= REVIZAR SI EL PRODUCTO YA EXISTE EN EL LISTADO DE PRODUCTOS DEL PEDIDO ========
            const indiceProducto = pedido_existe.productos.findIndex((item) => {
                return item.id == producto_nuevo.producto_id;
            })


            //====== EL PRODUCTO  ES NUEVO ======
            if (indiceProducto === -1) {
                //======== INSTANCIAR PRODUCTO ======
                const instancia_producto = {
                    id: producto_nuevo.producto_id,
                    nombre: producto_nuevo.producto_nombre
                }

                //======== INSTANCIANDO MODELO ======
                const instancia_modelo = {
                    id: producto_nuevo.modelo_id,
                    nombre: producto_nuevo.modelo_nombre
                };

                //====== INSTANCIANDO COLOR =======
                const instancia_color = {
                    id: producto_nuevo.color_id,
                    nombre: producto_nuevo.color_nombre
                };

                //====== INSTANCIANDO TALLA ======
                const instancia_talla = {
                    id: producto_nuevo.talla_id,
                    nombre: producto_nuevo.talla_nombre,
                    cantidad_pendiente: parseInt(producto_nuevo.cantidad_pendiente)
                };

                //====== FORMANDO =======
                instancia_producto.modelo = instancia_modelo;
                instancia_producto.colores = [instancia_color];
                instancia_producto.colores[0].tallas = [instancia_talla];

                pedido_existe.productos.push(instancia_producto);
            }

            //====== PRODUCTO YA EXISTE =======
            if (indiceProducto !== -1) {
                //===== VERIFICAR SI EXISTE EL COLOR =======
                const producto_existe = pedido_existe.productos[indiceProducto];

                const indiceColor = producto_existe.colores.findIndex((item) => {
                    return item.id == producto_nuevo.color_id;
                })

                //====== EL COLOR NO EXISTE ======
                if (indiceColor === -1) {
                    //====== INSTANCIAR COLOR =====
                    const instancia_color = {
                        id: producto_nuevo.color_id,
                        nombre: producto_nuevo.color_nombre
                    };

                    //===== INSTANCIAR TALLA =======
                    const instancia_talla = {
                        id: producto_nuevo.talla_id,
                        nombre: producto_nuevo.talla_nombre,
                        cantidad_pendiente: parseInt(producto_nuevo.cantidad_pendiente)
                    };

                    //======= FORMANDO =======
                    instancia_color.tallas = [instancia_talla];

                    producto_existe.colores.push(instancia_color)
                }

                //======== EL COLOR YA EXISTE ======
                if (indiceColor !== -1) {
                    //==== VERIFICAR SI LA TALLA EXISTE =====
                    const color_existe = producto_existe.colores[indiceColor];

                    const indiceTalla = color_existe.tallas.findIndex((item) => {
                        return item.id == producto_nuevo.talla_id;
                    })

                    //==== SI LA TALLA ES NUEVA ======
                    if (indiceTalla === -1) {
                        //===== INSTANCIAR TALLA =======
                        const instancia_talla = {
                            id: producto_nuevo.talla_id,
                            nombre: producto_nuevo.talla_nombre,
                            cantidad_pendiente: parseInt(producto_nuevo.cantidad_pendiente)
                        };

                        //======= FORMANDO =======
                        color_existe.tallas.push(instancia_talla);
                    }

                    //===== SI LA TALLA YA EXISTE =====
                    if (indiceTalla !== -1) {
                        color_existe.tallas[indiceTalla].cantidad_pendiente += parseInt(producto_nuevo
                            .cantidad_pendiente);
                    }
                }
            }
        }



        //===== EL PRODUCTO YA EXISTE =======
        /**/
    }

    //========= QUITAR PRODUCTO DEL LISTADO DE PROGRAMACIÓN DE PRODUCCIÓN ======
    function quitarProductoToProduccion(producto_desmarcado, checkProductoId) {

        //======== VERIFICAR SI EXISTE EL PEDIDO ID ======
        const indicePedido = lstProgramaProduccion.findIndex((pedido) => {
            return pedido.id == producto_desmarcado.pedido_id;
        })


        //====== EN CASO EL PEDIDO NO EXISTA ======
        if (indicePedido === -1) {
            toastr.error('ERROR AL ELIMINAR PRODUCTO DE LA LISTA DE PRODUCCIÓN', 'PEDIDO NO ENCONTRADO');
            return;
        }

        //======= EN CASO EXISTA EL PEDIDO =======
        if (indicePedido !== -1) {

            const pedido_existe = lstProgramaProduccion[indicePedido];

            //===== VERIFICAR SI EXISTE EL PRODUCTO ======
            const indiceProducto = pedido_existe.productos.findIndex((producto) => {
                return producto.id == producto_desmarcado.producto_id;
            })

            //====== EN CASO EL PRODUCTO NO EXISTA =====
            if (indiceProducto === -1) {
                toastr.error('ERROR AL QUITAR EL PRODUCTO DEL LISTADO PROGRAMACIÓN DE PRODUCCIÓN',
                    'PRODUCTO NO ENCONTRADO');
                return;
            }

            //======== EN CASO EL PRODUCTO EXISTA ======
            if (indiceProducto !== -1) {
                //======= VERIFICAR SI EXISTE EL COLOR ========
                const producto_existe = pedido_existe.productos[indiceProducto];
                const indiceColor = producto_existe.colores.findIndex((color) => {
                    return color.id == producto_desmarcado.color_id;
                })

                //====== EN CASO EL COLOR EXISTA =====
                if (indiceColor !== -1) {
                    const color_existe = producto_existe.colores[indiceColor];

                    //======== VERIFICAR QUE LA TALLA EXISTA ======
                    const indiceTalla = color_existe.tallas.findIndex((talla) => {
                        return talla.id == producto_desmarcado.talla_id;
                    })

                    //====== EN CASO EXISTA LA TALLA ======
                    if (indiceTalla !== -1) {
                        const talla_existe = color_existe.tallas[indiceTalla];

                        //====== CONTROLAR LA CANTIDAD AL RESTAR =======
                        let aux_cant_resultante = talla_existe.cantidad_pendiente - parseInt(producto_desmarcado
                            .cantidad_pendiente);

                        //======== LA RESTA FUE CORRECTA =====
                        if (aux_cant_resultante >= 0) {
                            //==== RESTAMOS =======
                            talla_existe.cantidad_pendiente -= parseInt(producto_desmarcado.cantidad_pendiente);

                            //======= QUITAMOS EL CHECKBOX DEL LISTADO DE CHECKBOX MARCADOS ======
                            const indiceCheckMarcado = lstChecksProductosMarcados.findIndex((chkId) => {
                                return chkId === checkProductoId;
                            })
                            if (indiceCheckMarcado !== -1) {
                                lstChecksProductosMarcados.splice(indiceCheckMarcado, 1);
                            }

                            //======= ELIMINAR LA TALLA EN CASO SU CANTIDAD LLEGUE A SER 0 ========
                            if (talla_existe.cantidad_pendiente == 0) {
                                color_existe.tallas.splice(indiceTalla, 1);

                                //====== EN CASO EL COLOR SE QUEDE SIN TALLAS, ELIMINAR EL COLOR =========
                                if (color_existe.tallas.length === 0) {
                                    producto_existe.colores.splice(indiceColor, 1);

                                    //===== EN CASO EL PRODUCTO SE QUEDE SIN COLORES, ELIMINAR EL PRODUCTO =====
                                    if (producto_existe.colores.length === 0) {
                                        pedido_existe.productos.splice(indiceProducto, 1);
                                    }
                                }
                            }


                        }

                        //====== RESTA INCORRECTA =======
                        if (aux_cant_resultante < 0) {
                            toastr.error(
                                'ERROR AL RESTAR LA CANTIDAD DEL PRODUCTO DEL LISTADO DE PROGRAMACIÓN DE PRODUCCIÓN'
                            );
                        }
                    }

                    //==== EN CASO LA TALLA NO EXISTA ======
                    if (indiceTalla === -1) {
                        toastr.error('ERROR AL QUITAR EL PRODUCTO DEL LISTADO PROGRAMACIÓN DE PRODUCCIÓN',
                            'TALLA NO ENCONTRADA');
                    }
                }

                //======== EN CASO EL COLOR NO EXISTA =====
                if (indiceColor === -1) {
                    toastr.error('ERROR AL QUITAR EL PRODUCTO DEL LISTADO PROGRAMACIÓN DE PRODUCCIÓN',
                        'COLOR NO ENCONTRADO');
                }
            }


        }

    }

    function limpiarTableProgramacionProduccion() {
        const tbody = document.querySelector('#table_programacion_produccion tbody');
        while (tbody.firstChild) {
            tbody.removeChild(tbody.firstChild);
        }
    }

    function pintarTableProgramacionProduccion() {
        let filas = ``;
        const tallasBD = @json($tallas);
        const tbody = document.querySelector('#table_programacion_produccion tbody');

        lstProgramaProduccion.forEach((pedido) => {

            //========= RECORRIENDO PRODUCTOS DE CADA PEDIDO =====
            pedido.productos.forEach((producto) => {

                //===== COLORES =======
                producto.colores.forEach((color) => {
                    filas += `<tr>
                                    <th><div style="width:120px;">PE-${pedido.id}</div></th>
                                    <th><div style="width:120px;">${producto.modelo.nombre}</div></th>
                                    <td><div style="width:120px;">${producto.nombre}</div></td>
                                    <td><div style="width:120px;">${color.nombre}</div></td>`;

                    //====== RECORRIENDO EN BASE A LAS TALLAS DE LA BD =======
                    tallasBD.forEach((tallaBD) => {

                        //======== REVIZANDO EL COLOR TIENE ESTA TALLA =====
                        const indiceTalla = color.tallas.findIndex((t) => {
                            return t.id == tallaBD.id;
                        })

                        let elementCantPendiente = ``;
                        //===== SI TIENE LA TALLA =====
                        if (indiceTalla !== -1) {
                            elementCantPendiente =
                                `<p style="margin:0;font-weight:bold;">${color.tallas[indiceTalla].cantidad_pendiente}</p>`;
                        }

                        //========= AÑADIENDO A LA FILA =======
                        filas += `<td>${elementCantPendiente}</td>`;

                    })

                })
            })
        })

        tbody.innerHTML = filas;
    }

    async function llenarCantEnviada() {
        try {
            const res = await axios.post(route('pedidos.pedido.llenarCantEnviada'));
            console.log(res);
        } catch (error) {

        }
    }




    function mostrarAnimacionCotizacion() {

        document.querySelector('.overlay_pedidos_detalles').style.visibility = 'visible';
    }

    function ocultarAnimacionCotizacion() {

        document.querySelector('.overlay_pedidos_detalles').style.visibility = 'hidden';
    }

    function filtrarEstadoDetalle() {
        $('#pedidos_detalles').DataTable().draw();
    }

    function filtrarCliente() {
        $('#pedidos_detalles').DataTable().draw();
    }

    async function getProductosByModelo(e) {
        filtrarModelo();
        mostrarAnimacionCotizacion();
        modelo_id = e.value;

        if (modelo_id) {
            try {
                const res = await axios.get(route('ventas.cotizacion.getProductosByModelo', {
                    modelo_id
                }));
                if (res.data.success) {
                    pintarSelectProductos(res.data.productos);
                    toastr.info('PRODUCTOS CARGADOS', 'OPERACIÓN COMPLETADA');
                } else {
                    ocultarAnimacionCotizacion();
                    toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                }
            } catch (error) {
                ocultarAnimacionCotizacion();
                toastr.error(error, 'ERROR EN LA PETICIÓN DE OBTENER PRODUCTOS');
            } finally {
                ocultarAnimacionCotizacion();
            }

        } else {
            ocultarAnimacionCotizacion();
        }
    }

    function pintarSelectProductos(productos) {
        //======= LIMPIAR SELECT2 DE PRODUCTOS ======
        $('#producto_id').empty();

        //====== LLENAR =======
        productos.forEach((producto) => {
            const option = new Option(producto.nombre, producto.id, false, false);
            $('#producto_id').append(option);
        });

        // Refrescar Select2
        $('#producto_id').trigger('change');
    }

    function filtrarModelo() {
        $('#pedidos_detalles').DataTable().draw();
    }

    function filtrarProducto() {
        $('#pedidos_detalles').DataTable().draw();
    }

    //======= MANTENER LOS CHECKBOX PINTADOS =====
    function remarcarCheckboxProductos() {
        lstChecksProductosMarcados.forEach((checkProductoId) => {
            const checkProducto = document.querySelector(`#${checkProductoId}`);
            if (checkProducto) {
                checkProducto.checked = true;
            }
        })
    }

    //======= DESCARGAR PDF PROGRAMACIÓN PRODUCCIÓN =========
    /*function descargarPdfProgramacionProduccion(){
        if(lstProgramaProduccion.length === 0){
            toastr.error('LA PROGRAMACIÓN DE PRODUCCIÓN ESTÁ VACÍA','ERROR AL GENERAR PDF');
            return;
        }

        try {
            let jsonData = JSON.stringify(lstProgramaProduccion);

            let url = route('pedidos.pedidos_detalles.pdfProgramacionProduccion', {
                lstProgramaProduccion: jsonData
            });


            window.open(url, '_blank');
        } catch (error) {

        }
    }*/

    //======= GENERAR ORDEN DE PEDIDO =====
    async function generarOrdenProduccion() {
        if (lstProgramaProduccion.length === 0) {
            toastr.error('LA PROGRAMACIÓN DE PRODUCCIÓN ESTÁ VACÍA', 'OPERACIÓN INCORRECTA');
            return;
        }


        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: "Desea generar una orden de producción?",
            text: "Acción no reversible!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí, genérala!",
            cancelButtonText: "No, cancelar!",
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {

                try {
                    limpiarMessagesErrorValidacion();
                    mostrarAnimacionCotizacion();
                    const fecha_propuesta_atencion = document.querySelector('#fecha_propuesta_atencion')
                        .value;
                    const observacion = document.querySelector('#observacion').value;

                    const res = await axios.post(route(
                        'pedidos.pedidos_detalles.generarOrdenProduccion'), {
                        lstProgramacionProduccion: JSON.stringify(lstProgramaProduccion),
                        fecha_propuesta_atencion,
                        observacion
                    });


                    if (res.data.success) {
                        dataTablePedidoDetalles.ajax.reload();
                        toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                    } else {
                        console.log(res.data);

                        toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                    }

                } catch (error) {
                    if ('errors' in error.response.data) {
                        pintarMessagesErrorValidacion(error.response.data.errors);
                        return;
                    }
                    toastr.error(error, 'ERROR EN LA PETICIÓN GENERAR ORDEN DE PEDIDO');
                } finally {
                    ocultarAnimacionCotizacion();
                }


            } else if (
                /* Read more about handling dismissals below */
                result.dismiss === Swal.DismissReason.cancel
            ) {
                swalWithBootstrapButtons.fire({
                    title: "Operación Cancelada",
                    text: "No se realizaron cambios",
                    icon: "error"
                });
            }
        });

    }

    function limpiarMessagesErrorValidacion() {
        const spanErrors = document.querySelectorAll('.spanError');
        spanErrors.forEach((span) => {
            span.textContent = '';
        })
    }

    function pintarMessagesErrorValidacion(messagesErrors) {
        for (let key in messagesErrors) {
            if (messagesErrors.hasOwnProperty(key)) {
                const message = messagesErrors[key];

                const spanError = document.querySelector(`.${key}_error`);
                spanError.textContent = message;
            }
        }
    }
</script>
@endpush
